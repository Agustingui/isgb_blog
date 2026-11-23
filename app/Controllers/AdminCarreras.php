<?php

namespace App\Controllers;

use App\Models\CarrerasModel;

/**
 * Panel: alta, edición y borrado de carreras con su plan de estudios (solo administradores).
 *
 * El plan se carga en 6 cuadros de texto ("anio1" a "anio6"), con una materia por línea.
 */
class AdminCarreras extends BaseController
{
    public function index()
    {
        return $this->vistaAdmin('admin/carreras/listado', [
            'carreras' => $this->db->table('carreras')->where('fecha_borrado', null)->orderBy('nombre')->get()->getResultArray(),
            'titulo'   => 'Carreras',
            'menu'     => 'carreras',
        ]);
    }

    public function nuevo()
    {
        return $this->vistaAdmin('admin/carreras/formulario', [
            'carrera'  => null,
            'planes'   => [],
            'imagenes' => $this->listaImagenes(),
            'pdfs'     => $this->listaPdfs(),
            'titulo'   => 'Nueva carrera',
            'menu'     => 'carreras',
            'ordenar'  => true,
        ]);
    }

    public function editar($id)
    {
        $carrera = (new CarrerasModel())->find($id);

        if ($carrera === null) {
            return redirect()->to(base_url('admin/carreras'))->with('error', 'No se encontró la carrera.');
        }

        $materias = $this->db->table('materias')
            ->where('carrera_id', $id)
            ->orderBy('anio')
            ->orderBy('id')
            ->get()
            ->getResultArray();

        // Agrupa las materias por año: [1 => ['Materia A', ...], 2 => [...]].
        $planes = [];
        foreach ($materias as $materia) {
            $planes[$materia['anio']][] = $materia['nombre'];
        }

        return $this->vistaAdmin('admin/carreras/formulario', [
            'carrera'  => $carrera,
            'planes'   => $planes,
            'imagenes' => $this->listaImagenes(),
            'pdfs'     => $this->listaPdfs(),
            'titulo'   => 'Editar carrera',
            'menu'     => 'carreras',
            'ordenar'  => true,
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
        $carreras = new CarrerasModel();

        if ($carreras->find($id) === null) {
            return $this->respuestaAccion(false, 'No se encontró la carrera.', 'admin/carreras');
        }

        // Va a la Papelera: sus materias se conservan para cuando se restaure.
        $carreras->delete($id);

        return $this->respuestaAccion(true, 'La carrera se movió a la Papelera.', 'admin/carreras');
    }

    /**
     * Valida y guarda una carrera nueva ($id null) o una existente, junto con su plan de estudios.
     */
    private function guardar($id)
    {
        $carreras = new CarrerasModel();

        if ($id !== null && $carreras->find($id) === null) {
            return redirect()->to(base_url('admin/carreras'))->with('error', 'No se encontró la carrera.');
        }

        $reglas = [
            'nombre'      => ['label' => 'Nombre', 'rules' => 'required|max_length[200]'],
            // Al editar, la carrera actual no cuenta como slug repetido.
            'slug'        => ['label' => 'Slug', 'rules' => 'required|alpha_dash|max_length[200]|is_unique[carreras.slug,id,' . (int) $id . ']'],
            'duracion'    => ['label' => 'Duración', 'rules' => 'required|max_length[50]'],
            'descripcion' => ['label' => 'Descripción', 'rules' => 'required|max_length[65000]'],
            'imagen_id'         => ['label' => 'Imagen', 'rules' => 'permit_empty|is_natural_no_zero|is_not_unique[media.id]'],
            'plan_pdf_id'       => ['label' => 'PDF del plan de estudios', 'rules' => 'permit_empty|is_natural_no_zero|is_not_unique[media.id]'],
            'resolucion_pdf_id' => ['label' => 'PDF de la resolución', 'rules' => 'permit_empty|is_natural_no_zero|is_not_unique[media.id]'],
        ];

        // Archivos subidos desde el mismo formulario (cada uno es opcional).
        $imagenNueva     = $this->archivoSubido('imagen_nueva');
        $planNuevo       = $this->archivoSubido('plan_pdf_nuevo');
        $resolucionNueva = $this->archivoSubido('resolucion_pdf_nuevo');

        if ($imagenNueva !== null) {
            $reglas['imagen_nueva'] = ['label' => 'Imagen nueva', 'rules' => $this->reglasImagenNueva];
        }
        if ($planNuevo !== null) {
            $reglas['plan_pdf_nuevo'] = ['label' => 'PDF del plan de estudios', 'rules' => 'uploaded[plan_pdf_nuevo]|max_size[plan_pdf_nuevo,10240]|ext_in[plan_pdf_nuevo,pdf]|mime_in[plan_pdf_nuevo,application/pdf]'];
        }
        if ($resolucionNueva !== null) {
            $reglas['resolucion_pdf_nuevo'] = ['label' => 'PDF de la resolución', 'rules' => 'uploaded[resolucion_pdf_nuevo]|max_size[resolucion_pdf_nuevo,10240]|ext_in[resolucion_pdf_nuevo,pdf]|mime_in[resolucion_pdf_nuevo,application/pdf]'];
        }

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $nombre = trim((string) $this->request->getPost('nombre'));

        $datos = [
            'nombre'            => $nombre,
            'slug'              => $this->request->getPost('slug'),
            'duracion'          => $this->request->getPost('duracion'),
            'descripcion'       => $this->request->getPost('descripcion'),
            'activa'            => $this->request->getPost('activa') ? 1 : 0,
            'imagen_id'         => $this->idONulo($this->request->getPost('imagen_id')),
            'plan_pdf_id'       => $this->idONulo($this->request->getPost('plan_pdf_id')),
            'resolucion_pdf_id' => $this->idONulo($this->request->getPost('resolucion_pdf_id')),
        ];

        // Lo subido reemplaza a lo elegido en el select y queda guardado en la biblioteca.
        if ($imagenNueva !== null) {
            $datos['imagen_id'] = $this->guardarArchivo($imagenNueva, $nombre);

            if ($datos['imagen_id'] === false) {
                return redirect()->back()->withInput()->with('errores', ['imagen_nueva' => 'No se pudo procesar la imagen. Probá con otro archivo.']);
            }
        }
        if ($planNuevo !== null) {
            $datos['plan_pdf_id'] = $this->guardarArchivo($planNuevo, 'Plan de estudios - ' . $nombre);
        }
        if ($resolucionNueva !== null) {
            $datos['resolucion_pdf_id'] = $this->guardarArchivo($resolucionNueva, 'Resolución - ' . $nombre);
        }

        $mensaje = $id === null ? 'La carrera se guardó correctamente.' : 'La carrera se actualizó correctamente.';

        // La carrera y su plan se guardan juntos: si algo falla, no queda nada a medias.
        $this->db->transStart();

        if ($id === null) {
            $id = $carreras->insert($datos);
        } else {
            $carreras->update($id, $datos);
        }

        // El plan se reemplaza completo: se borran las materias y se cargan las del formulario.
        $this->db->table('materias')->where('carrera_id', $id)->delete();

        $materias = [];
        for ($anio = 1; $anio <= 6; $anio++) {
            $lineas = explode("\n", (string) $this->request->getPost('anio' . $anio));

            foreach ($lineas as $linea) {
                if (trim($linea) !== '') {
                    $materias[] = ['carrera_id' => $id, 'anio' => $anio, 'nombre' => trim($linea)];
                }
            }
        }

        if ($materias !== []) {
            $this->db->table('materias')->insertBatch($materias);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'No se pudo guardar la carrera. Probá de nuevo.');
        }

        return redirect()->to(base_url('admin/carreras'))->with('mensaje', $mensaje);
    }

    /**
     * PDF de la biblioteca para elegir el plan de estudios y la resolución (id, alt, archivo).
     */
    private function listaPdfs()
    {
        return $this->db->table('media')->like('archivo', '.pdf', 'before')->where('fecha_borrado', null)->orderBy('alt')->get()->getResultArray();
    }
}
