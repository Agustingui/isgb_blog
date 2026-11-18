<?php

namespace App\Controllers;

use App\Models\PosteosModel;

/**
 * Panel: alta, edición y borrado de posteos del blog (contenido con el editor Quill).
 * El borrado es solo para administradores (ver app/Config/Routes.php).
 */
class AdminPosteos extends BaseController
{
    public function index()
    {
        // Cada posteo con su cantidad de "me gusta" y de lecturas, en una sola consulta.
        $posteos = $this->db->table('posteos')
            ->select('posteos.id, posteos.titulo, posteos.slug, posteos.extracto, posteos.estado, posteos.publicado_en')
            ->select("SUM(CASE WHEN interacciones.tipo = 'me_gusta' THEN 1 ELSE 0 END) AS me_gusta", false)
            ->select("SUM(CASE WHEN interacciones.tipo = 'vista' THEN 1 ELSE 0 END) AS vistas", false)
            ->join('interacciones', 'interacciones.posteo_id = posteos.id', 'left')
            ->where('posteos.fecha_borrado', null)
            ->groupBy('posteos.id')
            ->orderBy('posteos.publicado_en', 'DESC')
            ->orderBy('posteos.id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->vistaAdmin('admin/posteos/listado', [
            'posteos' => $posteos,
            'titulo'  => 'Posteos del blog',
            'menu'    => 'posteos',
        ]);
    }

    public function nuevo()
    {
        return $this->vistaAdmin('admin/posteos/formulario', [
            'posteo'   => null,
            'carreras' => $this->listaCarreras(),
            'imagenes' => $this->listaImagenes(),
            'autores'  => $this->listaAutores(),
            'titulo'   => 'Nuevo posteo',
            'menu'     => 'posteos',
            'editor'   => 'quill',
        ]);
    }

    public function editar($id)
    {
        $posteo = (new PosteosModel())->find($id);

        if ($posteo === null) {
            return redirect()->to(base_url('admin/posteos'))->with('error', 'No se encontró el posteo.');
        }

        return $this->vistaAdmin('admin/posteos/formulario', [
            'posteo'   => $posteo,
            'carreras' => $this->listaCarreras(),
            'imagenes' => $this->listaImagenes(),
            'autores'  => $this->listaAutores(),
            'titulo'   => 'Editar posteo',
            'menu'     => 'posteos',
            'editor'   => 'quill',
        ]);
    }

    public function insertar()
    {
        return $this->guardar(null);
    }

    public function actualizar()
    {
        return $this->guardar((int) $this->request->getPost('id'));
    }

    public function borrar($id)
    {
        $posteos = new PosteosModel();

        if ($posteos->find($id) === null) {
            return $this->respuestaAccion(false, 'No se encontró el posteo.', 'admin/posteos');
        }

        // Sus "me gusta" y lecturas se borran en cascada (clave foránea).
        $posteos->delete($id);

        return $this->respuestaAccion(true, 'El posteo se movió a la Papelera.', 'admin/posteos');
    }

    /**
     * Valida y guarda un posteo nuevo ($id null) o uno existente.
     */
    private function guardar($id)
    {
        $posteos = new PosteosModel();

        if ($id !== null && $posteos->find($id) === null) {
            return redirect()->to(base_url('admin/posteos'))->with('error', 'No se encontró el posteo.');
        }

        $reglas = [
            'titulo'       => ['label' => 'Título', 'rules' => 'required|max_length[250]'],
            // Al editar, el posteo actual no cuenta como slug repetido.
            'slug'         => ['label' => 'Slug', 'rules' => 'required|alpha_dash|max_length[250]|is_unique[posteos.slug,id,' . (int) $id . ']'],
            'extracto'     => ['label' => 'Extracto', 'rules' => 'required|max_length[255]'],
            'contenido'    => ['label' => 'Contenido', 'rules' => 'required|max_length[65000]'],
            'estado'       => ['label' => 'Estado', 'rules' => 'required|in_list[borrador,revision,publicado]'],
            'publicado_en' => ['label' => 'Fecha de publicación', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
            'carrera_id'   => ['label' => 'Carrera relacionada', 'rules' => 'permit_empty|is_natural_no_zero|is_not_unique[carreras.id]'],
            'autor_id'     => ['label' => 'Autor', 'rules' => 'permit_empty|is_natural_no_zero'],
            'imagen_id'    => ['label' => 'Imagen de portada', 'rules' => 'permit_empty|is_natural_no_zero|is_not_unique[media.id]'],
        ];

        $imagenNueva = $this->archivoSubido('imagen_nueva');
        if ($imagenNueva !== null) {
            $reglas['imagen_nueva'] = ['label' => 'Imagen nueva', 'rules' => $this->reglasImagenNueva];
        }

        // El HTML de Quill se limpia antes de guardarlo (solo quedan etiquetas permitidas).
        $contenido = contenido_html_seguro((string) $this->request->getPost('contenido'));
        $autorId   = $this->idONulo($this->request->getPost('autor_id'));

        $errores = $this->validate($reglas) ? [] : $this->validator->getErrors();
        $errores += $this->erroresDeContenido($contenido, $autorId);

        if ($errores !== []) {
            return redirect()->back()->withInput()->with('errores', $errores);
        }

        $datos = [
            'titulo'       => $this->request->getPost('titulo'),
            'slug'         => $this->request->getPost('slug'),
            'extracto'     => $this->request->getPost('extracto'),
            'contenido'    => $contenido,
            'estado'       => $this->request->getPost('estado'),
            'carrera_id'   => $this->idONulo($this->request->getPost('carrera_id')),
            'imagen_id'    => $this->idONulo($this->request->getPost('imagen_id')),
            'autor_id'     => $autorId,
            'publicado_en' => $this->request->getPost('publicado_en') ?: date('Y-m-d'),
        ];

        // Si se subió una imagen desde el formulario, reemplaza a la elegida en el select.
        if ($imagenNueva !== null) {
            $datos['imagen_id'] = $this->guardarArchivo($imagenNueva, $datos['titulo']);

            if ($datos['imagen_id'] === false) {
                return redirect()->back()->withInput()->with('errores', ['imagen_nueva' => 'No se pudo procesar la imagen. Probá con otro archivo.']);
            }
        }

        if ($id === null) {
            $datos['autor_id'] = $datos['autor_id'] ?? session('id_usuario');
            $posteos->insert($datos);

            return redirect()->to(base_url('admin/posteos'))->with('mensaje', 'El posteo se guardó correctamente.');
        }

        $posteos->update($id, $datos);

        return redirect()->to(base_url('admin/posteos'))->with('mensaje', 'El posteo se actualizó correctamente.');
    }

    /**
     * Carreras para el select "Carrera relacionada", como [id => nombre].
     */
    private function listaCarreras()
    {
        $carreras = $this->db->table('carreras')->where('fecha_borrado', null)->orderBy('nombre')->get()->getResultArray();

        return array_column($carreras, 'nombre', 'id');
    }
}
