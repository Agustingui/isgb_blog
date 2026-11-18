<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Controlador base del que heredan todos los demás.
 *
 * Tiene lo que comparten varios controladores: el armado de las páginas, las
 * consultas de noticias y posteos con su autor e imagen, la subida de
 * archivos y el inicio de sesión.
 */
abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'isgb'];

    /**
     * Conexión a la base de datos para las consultas con Query Builder.
     *
     * @var \CodeIgniter\Database\BaseConnection
     */
    protected $db;

    /**
     * Reglas de validación de una imagen subida en el campo "imagen_nueva".
     */
    protected $reglasImagenNueva = 'uploaded[imagen_nueva]|max_size[imagen_nueva,5120]|is_image[imagen_nueva]'
        . '|ext_in[imagen_nueva,jpg,jpeg,png,gif,webp]|mime_in[imagen_nueva,image/jpeg,image/png,image/gif,image/webp]';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->db = db_connect();
    }

    /**
     * Arma una página del sitio público: header + vista + footer.
     *
     * El header y el footer necesitan los datos del instituto y las carreras
     * del pie, así que se agregan acá.
     */
    protected function vistaPublica($vista, $datos = [])
    {
        $instituto = $this->db->table('configuracion')
            ->select('configuracion.*, media.archivo AS portada_archivo, media.alt AS portada_alt')
            ->join('media', 'media.id = configuracion.portada_imagen_id AND media.fecha_borrado IS NULL', 'left')
            ->get()
            ->getRowArray();

        // Valores por defecto si la tabla configuracion todavía está vacía.
        $datos['instituto'] = $instituto ?? [
            'direccion' => '', 'telefono' => '', 'email_contacto' => '', 'descripcion_pie' => '',
            'inscripcion_abierta' => 0, 'inscripcion_texto' => '', 'instagram' => '', 'facebook' => '',
            'portada_archivo' => null, 'portada_alt' => null,
        ];

        $datos['carreras_pie'] = $this->db->table('carreras')
            ->where('activa', 1)
            ->where('fecha_borrado', null)
            ->orderBy('nombre')
            ->limit(6)
            ->get()
            ->getResultArray();

        return view('publico/header', $datos) . view($vista, $datos) . view('publico/footer', $datos);
    }

    /**
     * Arma una página del panel: header + vista + footer.
     */
    protected function vistaAdmin($vista, $datos = [])
    {
        return view('admin/header', $datos) . view($vista, $datos) . view('admin/footer', $datos);
    }

    /**
     * Consulta de noticias con el nombre del autor y el archivo de la imagen.
     *
     * Devuelve el Query Builder para que cada controlador agregue sus filtros.
     */
    protected function consultaNoticias()
    {
        return $this->db->table('noticias')
            ->select('noticias.*, usuarios.nombre AS autor, media.archivo AS imagen_archivo, media.alt AS imagen_alt')
            ->join('usuarios', 'usuarios.id = noticias.autor_id', 'left')
            ->join('media', 'media.id = noticias.imagen_id AND media.fecha_borrado IS NULL', 'left')
            ->where('noticias.fecha_borrado', null);
    }

    /**
     * Consulta de posteos publicados con el nombre del autor y el archivo de la imagen.
     */
    protected function consultaPosteos()
    {
        return $this->db->table('posteos')
            ->select('posteos.*, usuarios.nombre AS autor, media.archivo AS imagen_archivo, media.alt AS imagen_alt')
            ->join('usuarios', 'usuarios.id = posteos.autor_id', 'left')
            ->join('media', 'media.id = posteos.imagen_id AND media.fecha_borrado IS NULL', 'left')
            ->where('posteos.estado', 'publicado')
            ->where('posteos.fecha_borrado', null);
    }

    /**
     * Responde el resultado de una acción, como un borrado.
     *
     * Si el pedido es AJAX devuelve JSON (con el token CSRF nuevo); si no,
     * redirige al listado con el mensaje.
     */
    protected function respuestaAccion($ok, $mensaje, $destino)
    {
        if ($this->request->isAJAX()) {
            return $this->response
                ->setStatusCode($ok ? 200 : 422)
                ->setJSON(['ok' => $ok, 'mensaje' => $mensaje, 'csrf' => csrf_hash()]);
        }

        return redirect()->to(base_url($destino))->with($ok ? 'mensaje' : 'error', $mensaje);
    }

    /**
     * Inicia la sesión de un usuario ya autenticado y lo lleva al panel o a la portada.
     */
    protected function iniciarSesion($usuario)
    {
        // Nuevo id de sesión al ingresar, para evitar que alguien reutilice uno anterior.
        session()->regenerate(true);
        session()->set([
            'id_usuario' => (int) $usuario['id'],
            'nombre'     => $usuario['nombre'],
            'email'      => $usuario['email'],
            'rol'        => $usuario['rol'],
        ]);

        if ($usuario['rol'] === 'admin' || $usuario['rol'] === 'editor') {
            return redirect()->to(base_url('admin'));
        }

        return redirect()->to(base_url());
    }

    /**
     * Convierte el valor de un select opcional en un id o en null (opción vacía).
     */
    protected function idONulo($valor)
    {
        return (int) $valor > 0 ? (int) $valor : null;
    }

    /**
     * Devuelve el archivo subido en un campo del formulario, o null si no se eligió ninguno.
     */
    protected function archivoSubido($campo)
    {
        $archivo = $this->request->getFile($campo);

        if ($archivo === null || $archivo->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return $archivo;
    }

    /**
     * Guarda un archivo en public/uploads y lo registra en la tabla media.
     *
     * @return int|false Id del archivo en la tabla media, o false si la imagen no se pudo procesar.
     */
    protected function guardarArchivo($archivo, $alt)
    {
        $nombre = $this->moverArchivo($archivo);

        if ($nombre === false) {
            return false;
        }

        return (new \App\Models\MediaModel())->insert([
            'alt'     => mb_substr(trim($alt), 0, 200),
            'archivo' => $nombre,
        ]);
    }

    /**
     * Mueve el archivo subido a public/uploads con un nombre aleatorio.
     *
     * Las imágenes se achican a 1600 px de ancho como máximo y se vuelven a
     * guardar; eso también descarta cualquier contenido extra escondido en el
     * archivo. Si no se pueden procesar, se borran.
     *
     * @return string|false Nombre del archivo guardado, o false si la imagen no se pudo procesar.
     */
    protected function moverArchivo($archivo)
    {
        $nombre = $archivo->getRandomName();
        $ruta   = FCPATH . 'uploads/' . $nombre;

        $archivo->move(FCPATH . 'uploads', $nombre);

        if (es_pdf($nombre)) {
            return $nombre;
        }

        $medidas = @getimagesize($ruta);

        if ($medidas === false) {
            $this->borrarArchivo($nombre);

            return false;
        }

        try {
            $imagen = service('image')->withFile($ruta);

            if ($medidas[0] > 1600) {
                $imagen->resize(1600, 1600, true, 'width');
            }

            $imagen->save($ruta, 85);
        } catch (\Throwable $e) {
            log_message('error', 'No se pudo procesar la imagen subida: ' . $e->getMessage());
            $this->borrarArchivo($nombre);

            return false;
        }

        return $nombre;
    }

    /**
     * Borra un archivo de public/uploads si existe.
     */
    protected function borrarArchivo($nombre)
    {
        // basename() evita que un nombre con "../" apunte fuera de la carpeta.
        $ruta = FCPATH . 'uploads/' . basename($nombre);

        if (is_file($ruta)) {
            unlink($ruta);
        }
    }

    /**
     * Imágenes de la biblioteca (sin los PDF) para los selectores de imagen de los formularios.
     */
    protected function listaImagenes()
    {
        return $this->db->table('media')
            ->notLike('archivo', '.pdf', 'before')
            ->where('fecha_borrado', null)
            ->orderBy('alt')
            ->get()
            ->getResultArray();
    }

    /**
     * Usuarios que pueden figurar como autores (administradores y editores), como [id => nombre].
     */
    protected function listaAutores()
    {
        $autores = $this->db->table('usuarios')
            ->whereIn('rol', ['admin', 'editor'])
            ->where('fecha_borrado', null)
            ->orderBy('nombre')
            ->get()
            ->getResultArray();

        return array_column($autores, 'nombre', 'id');
    }

    /**
     * Revisa lo que las reglas de validación no cubren en noticias y posteos.
     *
     * - El contenido del editor tiene que tener texto después de sanitizarlo.
     * - El autor elegido tiene que ser administrador o editor.
     *
     * @return array Errores por campo (vacío si todo está bien).
     */
    protected function erroresDeContenido($contenido, $autorId)
    {
        $errores = [];

        $texto = html_entity_decode(strip_tags($contenido), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (trim(preg_replace('/[\s\p{Z}]+/u', '', $texto)) === '') {
            $errores['contenido'] = 'El campo Contenido es obligatorio.';
        }

        if ($autorId !== null) {
            $autor = (new \App\Models\UsuariosModel())->find($autorId);

            if ($autor === null || ! in_array($autor['rol'], ['admin', 'editor'])) {
                $errores['autor_id'] = 'El autor elegido tiene que ser un administrador o editor.';
            }
        }

        return $errores;
    }
}
