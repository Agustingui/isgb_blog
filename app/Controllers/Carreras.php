<?php

namespace App\Controllers;

use App\Models\MediaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Carreras en el sitio público: listado y detalle con el plan de estudios.
 */
class Carreras extends BaseController
{
    public function index()
    {
        $carreras = $this->db->table('carreras')
            ->where('activa', 1)
            ->where('fecha_borrado', null)
            ->orderBy('nombre')
            ->get()
            ->getResultArray();

        return $this->vistaPublica('carreras/listado', [
            'carreras'    => $carreras,
            'titulo'      => 'Nuestras carreras',
            'descripcion' => 'Tecnicaturas superiores y profesorados con título oficial. Todas las carreras se cursan de forma presencial.',
        ]);
    }

    public function ver($slug)
    {
        $carrera = $this->db->table('carreras')
            ->where('slug', $slug)
            ->where('activa', 1)
            ->where('fecha_borrado', null)
            ->get()
            ->getRowArray();

        if ($carrera === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $materias = $this->db->table('materias')
            ->where('carrera_id', $carrera['id'])
            ->orderBy('anio')
            ->orderBy('id')
            ->get()
            ->getResultArray();

        // Agrupa las materias por año: [1 => ['Materia A', ...], 2 => [...]].
        $planes = [];
        foreach ($materias as $materia) {
            $planes[$materia['anio']][] = $materia['nombre'];
        }

        $media      = new MediaModel();
        $imagen     = $carrera['imagen_id'] ? $media->find($carrera['imagen_id']) : null;
        $pdf        = $carrera['plan_pdf_id'] ? $media->find($carrera['plan_pdf_id']) : null;
        $resolucion = $carrera['resolucion_pdf_id'] ? $media->find($carrera['resolucion_pdf_id']) : null;

        return $this->vistaPublica('carreras/detalle', [
            'carrera'     => $carrera,
            'planes'      => $planes,
            'imagen'      => $imagen,
            'pdf'         => $pdf,
            'resolucion'  => $resolucion,
            'titulo'      => $carrera['nombre'],
            'descripcion' => resumen($carrera['descripcion']),
            'og_imagen'   => $imagen ? base_url('uploads/' . $imagen['archivo']) : null,
        ]);
    }

    /**
     * Muestra el PDF del plan de estudios o de la resolución dentro de una página del sitio.
     *
     * Abierto suelto, el navegador muestra en la pestaña el nombre del archivo y el
     * ícono de la raíz del servidor; así se ven el título y el logo del instituto.
     *
     * @param string $tipo "plan" o "resolucion" (la ruta solo acepta esos dos).
     */
    public function documento($slug, $tipo)
    {
        $carrera = $this->db->table('carreras')
            ->where('slug', $slug)
            ->where('activa', 1)
            ->where('fecha_borrado', null)
            ->get()
            ->getRowArray();

        $campo = $tipo === 'plan' ? 'plan_pdf_id' : 'resolucion_pdf_id';
        $pdf   = $carrera !== null && $carrera[$campo] ? (new MediaModel())->find($carrera[$campo]) : null;

        if ($pdf === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $documento = $tipo === 'plan' ? 'Plan de estudios' : 'Resolución';

        return $this->vistaPublica('carreras/documento', [
            'carrera'     => $carrera,
            'pdf'         => $pdf,
            'documento'   => $documento,
            'titulo'      => $documento . ' - ' . $carrera['nombre'],
            'descripcion' => $documento . ' de la carrera ' . $carrera['nombre'] . ' (PDF).',
        ]);
    }
}
