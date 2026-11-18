<?php

namespace App\Controllers;

/**
 * Buscador del sitio: carreras, noticias y posteos.
 */
class Buscar extends BaseController
{
    public function index()
    {
        $q        = trim((string) $this->request->getGet('q'));
        $carreras = [];
        $noticias = [];
        $posteos  = [];

        if (mb_strlen($q) >= 2) {
            // El último parámetro de like() (true) hace que no distinga mayúsculas.
            $carreras = $this->db->table('carreras')
                ->where('activa', 1)
                ->where('fecha_borrado', null)
                ->groupStart()
                ->like('nombre', $q, 'both', null, true)
                ->orLike('descripcion', $q, 'both', null, true)
                ->groupEnd()
                ->orderBy('nombre')
                ->limit(10)
                ->get()
                ->getResultArray();

            $noticias = $this->consultaNoticias()
                ->groupStart()
                ->like('noticias.titulo', $q, 'both', null, true)
                ->orLike('noticias.bajada', $q, 'both', null, true)
                ->orLike('noticias.contenido', $q, 'both', null, true)
                ->groupEnd()
                ->orderBy('noticias.publicado_en', 'DESC')
                ->limit(20)
                ->get()
                ->getResultArray();

            $posteos = $this->consultaPosteos()
                ->groupStart()
                ->like('posteos.titulo', $q, 'both', null, true)
                ->orLike('posteos.extracto', $q, 'both', null, true)
                ->orLike('posteos.contenido', $q, 'both', null, true)
                ->groupEnd()
                ->orderBy('posteos.publicado_en', 'DESC')
                ->limit(20)
                ->get()
                ->getResultArray();
        }

        return $this->vistaPublica('publico/buscar', [
            'q'        => $q,
            'carreras' => $carreras,
            'noticias' => $noticias,
            'posteos'  => $posteos,
            'titulo'   => 'Buscar',
        ]);
    }
}
