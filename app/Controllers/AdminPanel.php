<?php

namespace App\Controllers;

/**
 * Página inicial del panel: totales de cada sección y lo último que se cargó.
 */
class AdminPanel extends BaseController
{
    public function index()
    {
        return $this->vistaAdmin('admin/panel', [
            'total_noticias'   => $this->db->table('noticias')->where('fecha_borrado', null)->countAllResults(),
            'total_posteos'    => $this->db->table('posteos')->where('fecha_borrado', null)->countAllResults(),
            'total_media'      => $this->db->table('media')->where('fecha_borrado', null)->countAllResults(),
            'total_carreras'   => $this->db->table('carreras')->where('fecha_borrado', null)->countAllResults(),
            'total_usuarios'   => $this->db->table('usuarios')->where('fecha_borrado', null)->countAllResults(),
            // Posteos que esperan que alguien los revise y los publique.
            'posteos_revision' => $this->db->table('posteos')->where('fecha_borrado', null)->where('estado', 'revision')->countAllResults(),
            'ultimas_noticias' => $this->db->table('noticias')
                ->select('id, titulo, categoria, publicado_en')
                ->where('fecha_borrado', null)
                ->orderBy('id', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray(),
            'ultimos_posteos'  => $this->db->table('posteos')
                ->select('id, titulo, estado, publicado_en')
                ->where('fecha_borrado', null)
                ->orderBy('id', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray(),
            'titulo'           => 'Inicio',
            'menu'             => 'panel',
        ]);
    }
}
