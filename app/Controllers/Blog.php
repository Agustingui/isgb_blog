<?php

namespace App\Controllers;

use App\Models\InteraccionesModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Blog público: listado, detalle, "me gusta" y registro de lecturas.
 */
class Blog extends BaseController
{
    public function index()
    {
        $porPagina = 9;
        $pagina    = max(1, (int) $this->request->getGet('pagina'));
        $q         = trim((string) $this->request->getGet('q'));
        $mes       = (string) $this->request->getGet('mes');

        $consulta = $this->consultaPosteos();

        if ($q !== '') {
            $consulta->groupStart()
                ->like('posteos.titulo', $q, 'both', null, true)
                ->orLike('posteos.extracto', $q, 'both', null, true)
                ->orLike('posteos.contenido', $q, 'both', null, true)
                ->groupEnd();
        }

        // Filtro por mes (AAAA-MM); cualquier otro valor se ignora.
        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
            $consulta->where('posteos.publicado_en >=', $mes . '-01')
                ->where('posteos.publicado_en <=', date('Y-m-t', strtotime($mes . '-01')));
        } else {
            $mes = null;
        }

        $total = $consulta->countAllResults(false);

        $posteos = $consulta
            ->orderBy('posteos.publicado_en', 'DESC')
            ->orderBy('posteos.id', 'DESC')
            ->limit($porPagina, ($pagina - 1) * $porPagina)
            ->get()
            ->getResultArray();

        // Archivo del blog: cantidad de posteos publicados por mes.
        $fechas = $this->db->table('posteos')->select('publicado_en')->where('estado', 'publicado')->where('fecha_borrado', null)->get()->getResultArray();
        $meses  = [];
        foreach ($fechas as $fila) {
            if ($fila['publicado_en']) {
                $clave = substr($fila['publicado_en'], 0, 7);
                $meses[$clave] = ($meses[$clave] ?? 0) + 1;
            }
        }
        krsort($meses);

        return $this->vistaPublica('blog/listado', [
            'posteos'       => $posteos,
            'meses'         => $meses,
            'mes'           => $mes,
            'q'             => $q,
            'total'         => $total,
            'pagina'        => $pagina,
            'total_paginas' => max(1, (int) ceil($total / $porPagina)),
            'titulo'        => 'Blog',
            'descripcion'   => 'Historias de docentes, estudiantes y egresados del Instituto Superior Gaspar Benavento.',
        ]);
    }

    public function ver($slug)
    {
        $posteo = $this->consultaPosteos()->where('posteos.slug', $slug)->get()->getRowArray();

        if ($posteo === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $idUsuario = (int) session('id_usuario');

        return $this->vistaPublica('blog/detalle', [
            'posteo'         => $posteo,
            'me_gusta'       => $idUsuario > 0 && $this->buscarInteraccion($idUsuario, $posteo['id'], 'me_gusta') !== null,
            'ya_leido'       => $idUsuario > 0 && $this->buscarInteraccion($idUsuario, $posteo['id'], 'vista') !== null,
            'total_me_gusta' => $this->contarInteracciones($posteo['id'], 'me_gusta'),
            'total_vistas'   => $this->contarInteracciones($posteo['id'], 'vista'),
            'titulo'         => $posteo['titulo'],
            'descripcion'    => $posteo['extracto'],
            'og_tipo'        => 'article',
            'og_imagen'      => $posteo['imagen_archivo'] ? base_url('uploads/' . $posteo['imagen_archivo']) : null,
        ]);
    }

    /**
     * Marca o desmarca el "me gusta" del usuario en un posteo (POST, también por AJAX).
     */
    public function meGusta($id)
    {
        $posteo = $this->db->table('posteos')->where('id', $id)->where('estado', 'publicado')->where('fecha_borrado', null)->get()->getRowArray();

        if ($posteo === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $idUsuario     = (int) session('id_usuario');
        $existente     = $this->buscarInteraccion($idUsuario, $id, 'me_gusta');
        $interacciones = new InteraccionesModel();

        if ($existente !== null) {
            $interacciones->delete($existente['id']);
        } else {
            $interacciones->insert(['usuario_id' => $idUsuario, 'posteo_id' => $id, 'tipo' => 'me_gusta']);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'       => true,
                'me_gusta' => $existente === null,
                'total'    => $this->contarInteracciones($id, 'me_gusta'),
                'csrf'     => csrf_hash(),
            ]);
        }

        return redirect()->to(base_url('blog/' . $posteo['slug']));
    }

    /**
     * Registra que el usuario leyó el posteo (POST por AJAX, una sola vez por usuario).
     */
    public function vista($id)
    {
        $existe = $this->db->table('posteos')->where('id', $id)->where('estado', 'publicado')->where('fecha_borrado', null)->countAllResults() > 0;

        if (! $existe) {
            return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'csrf' => csrf_hash()]);
        }

        $idUsuario = (int) session('id_usuario');

        if ($this->buscarInteraccion($idUsuario, $id, 'vista') === null) {
            (new InteraccionesModel())->insert(['usuario_id' => $idUsuario, 'posteo_id' => $id, 'tipo' => 'vista']);
        }

        return $this->response->setJSON([
            'ok'    => true,
            'total' => $this->contarInteracciones($id, 'vista'),
            'csrf'  => csrf_hash(),
        ]);
    }

    private function buscarInteraccion($idUsuario, $idPosteo, $tipo)
    {
        return $this->db->table('interacciones')
            ->where('usuario_id', $idUsuario)
            ->where('posteo_id', $idPosteo)
            ->where('tipo', $tipo)
            ->get()
            ->getRowArray();
    }

    private function contarInteracciones($idPosteo, $tipo)
    {
        return $this->db->table('interacciones')->where('posteo_id', $idPosteo)->where('tipo', $tipo)->countAllResults();
    }
}
