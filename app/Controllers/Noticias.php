<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Noticias en el sitio público: listado con filtro por categoría y detalle.
 */
class Noticias extends BaseController
{
    public function index()
    {
        $porPagina = 9;
        $pagina    = max(1, (int) $this->request->getGet('pagina'));
        $categoria = $this->request->getGet('categoria');

        $consulta = $this->consultaNoticias();

        if (in_array($categoria, ['institucionales', 'academicas', 'ultimo_momento'], true)) {
            $consulta->where('noticias.categoria', $categoria);
        } else {
            $categoria = null;
        }

        // false: cuenta sin borrar los filtros, así se reutilizan para traer la página.
        $total = $consulta->countAllResults(false);

        $noticias = $consulta
            ->orderBy('noticias.publicado_en', 'DESC')
            ->orderBy('noticias.id', 'DESC')
            ->limit($porPagina, ($pagina - 1) * $porPagina)
            ->get()
            ->getResultArray();

        return $this->vistaPublica('noticias/listado', [
            'noticias'      => $noticias,
            'categoria'     => $categoria,
            'pagina'        => $pagina,
            'total_paginas' => max(1, (int) ceil($total / $porPagina)),
            'titulo'        => 'Noticias',
            'descripcion'   => 'Noticias y novedades del Instituto Superior Gaspar Benavento.',
        ]);
    }

    public function ver($slug)
    {
        $noticia = $this->consultaNoticias()
            ->where('noticias.slug', $slug)
            ->get()
            ->getRowArray();

        if ($noticia === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->vistaPublica('noticias/detalle', [
            'noticia'     => $noticia,
            'titulo'      => $noticia['titulo'],
            'descripcion' => $noticia['bajada'],
            'og_tipo'     => 'article',
            'og_imagen'   => $noticia['imagen_archivo'] ? base_url('uploads/' . $noticia['imagen_archivo']) : null,
        ]);
    }
}
