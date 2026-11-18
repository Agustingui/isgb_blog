<?php

namespace App\Controllers;

use App\Models\NoticiasModel;

/**
 * Panel: alta, edición y borrado de noticias (contenido con el editor Quill).
 * El borrado es solo para administradores (ver app/Config/Routes.php).
 */
class AdminNoticias extends BaseController
{
    public function index()
    {
        $noticias = $this->db->table('noticias')
            ->where('fecha_borrado', null)
            ->orderBy('publicado_en', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->vistaAdmin('admin/noticias/listado', [
            'noticias' => $noticias,
            'titulo'   => 'Noticias',
            'menu'     => 'noticias',
        ]);
    }

    public function nuevo()
    {
        return $this->vistaAdmin('admin/noticias/formulario', [
            'noticia'  => null,
            'imagenes' => $this->listaImagenes(),
            'autores'  => $this->listaAutores(),
            'titulo'   => 'Nueva noticia',
            'menu'     => 'noticias',
            'editor'   => 'quill',
        ]);
    }

    public function editar($id)
    {
        $noticia = (new NoticiasModel())->find($id);

        if ($noticia === null) {
            return redirect()->to(base_url('admin/noticias'))->with('error', 'No se encontró la noticia.');
        }

        return $this->vistaAdmin('admin/noticias/formulario', [
            'noticia'  => $noticia,
            'imagenes' => $this->listaImagenes(),
            'autores'  => $this->listaAutores(),
            'titulo'   => 'Editar noticia',
            'menu'     => 'noticias',
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
        $noticias = new NoticiasModel();

        if ($noticias->find($id) === null) {
            return $this->respuestaAccion(false, 'No se encontró la noticia.', 'admin/noticias');
        }

        $noticias->delete($id);

        return $this->respuestaAccion(true, 'La noticia se movió a la Papelera.', 'admin/noticias');
    }

    /**
     * Valida y guarda una noticia nueva ($id null) o una existente.
     */
    private function guardar($id)
    {
        $noticias = new NoticiasModel();

        if ($id !== null && $noticias->find($id) === null) {
            return redirect()->to(base_url('admin/noticias'))->with('error', 'No se encontró la noticia.');
        }

        $reglas = [
            'titulo'       => ['label' => 'Título', 'rules' => 'required|max_length[250]'],
            // Al editar, la noticia actual no cuenta como slug repetido.
            'slug'         => ['label' => 'Slug', 'rules' => 'required|alpha_dash|max_length[250]|is_unique[noticias.slug,id,' . (int) $id . ']'],
            'bajada'       => ['label' => 'Bajada', 'rules' => 'required|max_length[255]'],
            'contenido'    => ['label' => 'Contenido', 'rules' => 'required|max_length[65000]'],
            'categoria'    => ['label' => 'Categoría', 'rules' => 'required|in_list[institucionales,academicas,ultimo_momento]'],
            'publicado_en' => ['label' => 'Fecha de publicación', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
            'autor_id'     => ['label' => 'Autor', 'rules' => 'permit_empty|is_natural_no_zero'],
            'imagen_id'    => ['label' => 'Imagen', 'rules' => 'permit_empty|is_natural_no_zero|is_not_unique[media.id]'],
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
            'bajada'       => $this->request->getPost('bajada'),
            'contenido'    => $contenido,
            'categoria'    => $this->request->getPost('categoria'),
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
            $noticias->insert($datos);

            return redirect()->to(base_url('admin/noticias'))->with('mensaje', 'La noticia se guardó correctamente.');
        }

        $noticias->update($id, $datos);

        return redirect()->to(base_url('admin/noticias'))->with('mensaje', 'La noticia se actualizó correctamente.');
    }
}
