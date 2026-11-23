<?php

namespace App\Controllers;

use App\Models\MediaModel;

/**
 * Panel: biblioteca de archivos (imágenes y PDF) que usan noticias, posteos, carreras y la portada.
 */
class AdminMedia extends BaseController
{
    // Tipos de archivo permitidos en la biblioteca, hasta 5 MB.
    private $reglasArchivo = 'uploaded[archivo]|max_size[archivo,5120]|ext_in[archivo,jpg,jpeg,png,gif,webp,pdf]'
        . '|mime_in[archivo,image/jpeg,image/png,image/gif,image/webp,application/pdf]';

    public function index()
    {
        $archivos = $this->db->table('media')->where('fecha_borrado', null)->orderBy('id', 'DESC')->get()->getResultArray();

        // Peso y resolución se leen del archivo en disco (no se guardan en la base).
        // Si el archivo no está en esta compu (uploads/ no se sube al repo), quedan en null.
        foreach ($archivos as &$archivo) {
            $ruta    = FCPATH . 'uploads/' . basename($archivo['archivo']);
            $existe  = is_file($ruta);
            $medidas = $existe && ! es_pdf($archivo['archivo']) ? @getimagesize($ruta) : false;

            $archivo['peso']  = $existe ? filesize($ruta) : null;
            $archivo['ancho'] = $medidas ? $medidas[0] : null;
            $archivo['alto']  = $medidas ? $medidas[1] : null;
        }
        unset($archivo);

        return $this->vistaAdmin('admin/media/listado', [
            'archivos' => $archivos,
            'titulo'   => 'Archivos',
            'menu'     => 'media',
        ]);
    }

    public function nuevo()
    {
        return $this->vistaAdmin('admin/media/nuevo', [
            'titulo' => 'Subir archivo',
            'menu'   => 'media',
        ]);
    }

    public function insertar()
    {
        $reglas = [
            'alt'     => ['label' => 'Descripción del archivo', 'rules' => 'required|max_length[200]'],
            'archivo' => ['label' => 'Archivo', 'rules' => $this->reglasArchivo],
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $id = $this->guardarArchivo($this->request->getFile('archivo'), (string) $this->request->getPost('alt'));

        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'No se pudo procesar la imagen. Probá con otro archivo.');
        }

        return redirect()->to(base_url('admin/media'))->with('mensaje', 'El archivo se subió correctamente.');
    }

    public function editar($id)
    {
        $archivo = (new MediaModel())->find($id);

        if ($archivo === null) {
            return redirect()->to(base_url('admin/media'))->with('error', 'No se encontró el archivo.');
        }

        return $this->vistaAdmin('admin/media/editar', [
            'archivo' => $archivo,
            'titulo'  => 'Editar archivo',
            'menu'    => 'media',
        ]);
    }

    /**
     * Cambia la descripción y, si se subió uno, reemplaza el archivo.
     *
     * Al reemplazar se conserva el id, así las noticias, posteos, carreras y la
     * portada que lo usan muestran el archivo nuevo sin tener que editarlos.
     */
    public function actualizar()
    {
        $media  = new MediaModel();
        $id     = (int) $this->request->getPost('id');
        $actual = $media->find($id);

        if ($actual === null) {
            return redirect()->to(base_url('admin/media'))->with('error', 'No se encontró el archivo.');
        }

        $nuevo  = $this->archivoSubido('archivo');
        $reglas = ['alt' => ['label' => 'Descripción del archivo', 'rules' => 'required|max_length[200]']];

        if ($nuevo !== null) {
            $reglas['archivo'] = ['label' => 'Archivo nuevo', 'rules' => $this->reglasArchivo];
        }

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $datos = ['alt' => trim((string) $this->request->getPost('alt'))];

        if ($nuevo !== null) {
            // Tiene que ser del mismo tipo: por ejemplo, el plan de una carrera espera un PDF.
            $actualEsPdf = es_pdf($actual['archivo']);

            if ($actualEsPdf !== ($nuevo->getMimeType() === 'application/pdf')) {
                $tipo = $actualEsPdf ? 'PDF' : 'imagen';

                return redirect()->back()->withInput()->with('errores', ['archivo' => 'El archivo nuevo tiene que ser del mismo tipo que el actual (' . $tipo . ').']);
            }

            $datos['archivo'] = $this->moverArchivo($nuevo);

            if ($datos['archivo'] === false) {
                return redirect()->back()->withInput()->with('errores', ['archivo' => 'No se pudo procesar la imagen. Probá con otro archivo.']);
            }
        }

        $media->update($id, $datos);

        // El archivo viejo se borra recién después de guardar el nuevo.
        if ($nuevo !== null) {
            $this->borrarArchivo($actual['archivo']);
        }

        return redirect()->to(base_url('admin/media'))->with('mensaje', 'El archivo se actualizó correctamente.');
    }

    /**
     * Sube una imagen desde el botón "Imagen" del editor (AJAX) y devuelve su dirección.
     *
     * La imagen queda en la biblioteca como cualquier otro archivo; la descripción
     * es el nombre del archivo y se puede cambiar después en "Archivos".
     */
    public function imagenEditor()
    {
        if (! $this->validate(['imagen_nueva' => ['label' => 'Imagen', 'rules' => $this->reglasImagenNueva]])) {
            return $this->response->setStatusCode(422)
                ->setJSON(['ok' => false, 'mensaje' => implode(' ', $this->validator->getErrors()), 'csrf' => csrf_hash()]);
        }

        $archivo = $this->request->getFile('imagen_nueva');
        $id      = $this->guardarArchivo($archivo, pathinfo($archivo->getClientName(), PATHINFO_FILENAME));

        if ($id === false) {
            return $this->response->setStatusCode(422)
                ->setJSON(['ok' => false, 'mensaje' => 'No se pudo procesar la imagen. Probá con otro archivo.', 'csrf' => csrf_hash()]);
        }

        $media = (new MediaModel())->find($id);

        return $this->response->setJSON([
            'ok'   => true,
            'url'  => base_url('uploads/' . $media['archivo']),
            'csrf' => csrf_hash(),
        ]);
    }

    /**
     * Manda el archivo a la Papelera. Mientras esté ahí, donde se usaba se muestra "sin imagen".
     */
    public function borrar($id)
    {
        $media   = new MediaModel();
        $archivo = $media->find($id);

        if ($archivo === null) {
            return $this->respuestaAccion(false, 'No se encontró el archivo.', 'admin/media');
        }

        // Va a la Papelera: el archivo queda en el disco hasta que se borre definitivamente.
        $media->delete($id);

        return $this->respuestaAccion(true, 'El archivo se movió a la Papelera.', 'admin/media');
    }
}
