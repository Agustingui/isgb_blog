<?php

namespace App\Controllers;

/**
 * RSS de posteos y noticias, y sitemap para los buscadores.
 */
class Feed extends BaseController
{
    public function rss()
    {
        $posteos = $this->consultaPosteos()->orderBy('posteos.publicado_en', 'DESC')->limit(30)->get()->getResultArray();

        $noticias = $this->consultaNoticias()->orderBy('noticias.publicado_en', 'DESC')->limit(30)->get()->getResultArray();

        $items = [];

        foreach ($posteos as $posteo) {
            $items[] = [
                'titulo'      => $posteo['titulo'],
                'enlace'      => base_url('blog/' . $posteo['slug']),
                'descripcion' => $posteo['extracto'],
                'fecha'       => $posteo['publicado_en'],
            ];
        }

        foreach ($noticias as $noticia) {
            $items[] = [
                'titulo'      => $noticia['titulo'],
                'enlace'      => base_url('noticias/' . $noticia['slug']),
                'descripcion' => $noticia['bajada'],
                'fecha'       => $noticia['publicado_en'],
            ];
        }

        // Más recientes primero, mezclando posteos y noticias.
        usort($items, function ($a, $b) {
            return strcmp((string) $b['fecha'], (string) $a['fecha']);
        });

        // debug false: sin los comentarios de la barra de depuración, que romperían el XML.
        return $this->response
            ->setContentType('application/rss+xml', 'UTF-8')
            ->setBody(view('feed/rss', ['items' => array_slice($items, 0, 30)], ['debug' => false]));
    }

    public function sitemap()
    {
        $urls = [];

        foreach (['', 'carreras', 'noticias', 'blog', 'privacidad'] as $ruta) {
            $urls[] = ['enlace' => base_url($ruta), 'fecha' => null];
        }

        $carreras = $this->db->table('carreras')->where('activa', 1)->where('fecha_borrado', null)->get()->getResultArray();
        foreach ($carreras as $carrera) {
            $urls[] = ['enlace' => base_url('carreras/' . $carrera['slug']), 'fecha' => $carrera['fecha_edicion']];
        }

        foreach ($this->consultaNoticias()->get()->getResultArray() as $noticia) {
            $urls[] = ['enlace' => base_url('noticias/' . $noticia['slug']), 'fecha' => $noticia['publicado_en']];
        }

        foreach ($this->consultaPosteos()->get()->getResultArray() as $posteo) {
            $urls[] = ['enlace' => base_url('blog/' . $posteo['slug']), 'fecha' => $posteo['publicado_en']];
        }

        return $this->response
            ->setContentType('application/xml', 'UTF-8')
            ->setBody(view('feed/sitemap', ['urls' => $urls], ['debug' => false]));
    }
}
