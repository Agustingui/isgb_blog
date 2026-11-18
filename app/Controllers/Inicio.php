<?php

namespace App\Controllers;

/**
 * Portada del sitio.
 */
class Inicio extends BaseController
{
    public function index()
    {
        $carreras = $this->db->table('carreras')
            ->where('activa', 1)
            ->where('fecha_borrado', null)
            ->orderBy('nombre')
            ->limit(6)
            ->get()
            ->getResultArray();

        $noticias = $this->consultaNoticias()
            ->orderBy('noticias.publicado_en', 'DESC')
            ->orderBy('noticias.id', 'DESC')
            ->limit(3)
            ->get()
            ->getResultArray();

        $posteos = $this->consultaPosteos()
            ->orderBy('posteos.publicado_en', 'DESC')
            ->orderBy('posteos.id', 'DESC')
            ->limit(9)
            ->get()
            ->getResultArray();

        return $this->vistaPublica('publico/inicio', [
            'titulo'   => 'Instituto Superior Gaspar Benavento',
            'carreras' => $carreras,
            'noticias' => $noticias,
            'posteos'  => $posteos,
        ]);
    }
}
