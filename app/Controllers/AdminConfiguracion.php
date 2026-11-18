<?php

namespace App\Controllers;

use App\Models\ConfiguracionModel;

/**
 * Panel: datos del instituto (contacto, inscripción, textos e imagen de la portada). Solo administradores.
 *
 * La tabla configuracion tiene una sola fila.
 */
class AdminConfiguracion extends BaseController
{
    public function index()
    {
        $configuracion = $this->db->table('configuracion')->get()->getRowArray() ?? [
            'direccion' => '', 'telefono' => '', 'email_contacto' => '', 'descripcion_pie' => '', 'instagram' => '',
            'facebook' => '', 'inscripcion_texto' => '', 'inscripcion_abierta' => 0, 'portada_imagen_id' => null,
            'portada_etiqueta' => '', 'portada_titulo' => '', 'portada_texto' => '', 'portada_boton1_texto' => '',
            'portada_boton1_enlace' => '', 'portada_boton2_texto' => '', 'portada_boton2_enlace' => '', 'portada_boton2_visible' => 1,
        ];

        return $this->vistaAdmin('admin/configuracion/editar', [
            'configuracion' => $configuracion,
            'imagenes'      => $this->listaImagenes(),
            'titulo'        => 'Datos del instituto',
            'menu'          => 'configuracion',
        ]);
    }

    public function actualizar()
    {
        $reglas = [
            'direccion'         => ['label' => 'Dirección', 'rules' => 'permit_empty|max_length[200]'],
            'telefono'          => ['label' => 'Teléfono', 'rules' => 'permit_empty|max_length[60]'],
            'email_contacto'    => ['label' => 'Email de contacto', 'rules' => 'permit_empty|valid_email|max_length[150]'],
            'inscripcion_texto' => ['label' => 'Texto de la insignia', 'rules' => 'permit_empty|max_length[100]'],
            'descripcion_pie'   => ['label' => 'Descripción corta', 'rules' => 'permit_empty|max_length[255]'],
            'portada_imagen_id' => ['label' => 'Imagen de la portada', 'rules' => 'permit_empty|is_natural_no_zero|is_not_unique[media.id]'],
            // Solo enlaces http o https (evita enlaces "javascript:").
            'instagram'         => ['label' => 'Instagram', 'rules' => 'permit_empty|valid_url_strict|max_length[200]'],
            'facebook'          => ['label' => 'Facebook', 'rules' => 'permit_empty|valid_url_strict|max_length[200]'],
            // Portada: los campos vacíos muestran el texto original.
            'portada_etiqueta'     => ['label' => 'Texto chico de arriba', 'rules' => 'permit_empty|max_length[100]'],
            'portada_titulo'       => ['label' => 'Título de la portada', 'rules' => 'permit_empty|max_length[200]'],
            'portada_texto'        => ['label' => 'Texto de la portada', 'rules' => 'permit_empty|max_length[400]'],
            'portada_boton1_texto' => ['label' => 'Texto del botón principal', 'rules' => 'permit_empty|max_length[60]'],
            'portada_boton2_texto' => ['label' => 'Texto del segundo botón', 'rules' => 'permit_empty|max_length[60]'],
        ];

        // Enlaces de los botones: un ancla (#oferta), una ruta del sitio (carreras) o una dirección http(s).
        // Nunca "javascript:". Las reglas van en array porque la expresión regular lleva "|".
        $reglaEnlace = ['permit_empty', 'max_length[255]', 'regex_match[/^(#[A-Za-z0-9_-]+|https?:\/\/[^\s<>"]+|[A-Za-z0-9_\/.?=&#-]+)$/]'];
        $errorEnlace = ['regex_match' => '{field}: usá una sección (#oferta), una página del sitio (carreras) o una dirección que empiece con https://.'];
        $reglas['portada_boton1_enlace'] = ['label' => 'Enlace del botón principal', 'rules' => $reglaEnlace, 'errors' => $errorEnlace];
        $reglas['portada_boton2_enlace'] = ['label' => 'Enlace del segundo botón', 'rules' => $reglaEnlace, 'errors' => $errorEnlace];

        $imagenNueva = $this->archivoSubido('imagen_nueva');
        if ($imagenNueva !== null) {
            $reglas['imagen_nueva'] = ['label' => 'Imagen nueva', 'rules' => $this->reglasImagenNueva];
        }

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $datos = [
            'direccion'           => (string) $this->request->getPost('direccion'),
            'telefono'            => (string) $this->request->getPost('telefono'),
            'email_contacto'      => (string) $this->request->getPost('email_contacto'),
            'inscripcion_texto'   => (string) $this->request->getPost('inscripcion_texto'),
            'inscripcion_abierta' => $this->request->getPost('inscripcion_abierta') ? 1 : 0,
            'descripcion_pie'     => (string) $this->request->getPost('descripcion_pie'),
            'instagram'           => (string) $this->request->getPost('instagram'),
            'facebook'            => (string) $this->request->getPost('facebook'),
            'portada_imagen_id'   => $this->idONulo($this->request->getPost('portada_imagen_id')),
        ];

        $camposPortada = ['portada_etiqueta', 'portada_titulo', 'portada_texto', 'portada_boton1_texto', 'portada_boton1_enlace', 'portada_boton2_texto', 'portada_boton2_enlace'];
        foreach ($camposPortada as $campo) {
            $datos[$campo] = trim((string) $this->request->getPost($campo));
        }
        $datos['portada_boton2_visible'] = $this->request->getPost('portada_boton2_visible') ? 1 : 0;

        // Si se subió una imagen desde el formulario, reemplaza a la elegida en el select.
        if ($imagenNueva !== null) {
            $datos['portada_imagen_id'] = $this->guardarArchivo($imagenNueva, 'Portada del sitio');

            if ($datos['portada_imagen_id'] === false) {
                return redirect()->back()->withInput()->with('errores', ['imagen_nueva' => 'No se pudo procesar la imagen. Probá con otro archivo.']);
            }
        }

        $configuracion = new ConfiguracionModel();
        $fila          = $configuracion->first();

        if ($fila === null) {
            $configuracion->insert($datos);
        } else {
            $configuracion->update($fila['id'], $datos);
        }

        return redirect()->to(base_url('admin/configuracion'))->with('mensaje', 'Los datos del instituto se guardaron.');
    }
}
