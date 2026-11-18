<?php

/**
 * Helper "isgb": funciones para mostrar datos en las vistas (fechas, textos y contenido HTML).
 *
 * Se carga en todos los controladores desde BaseController::$helpers.
 * Ninguna función consulta la base de datos.
 */

if (! function_exists('recurso')) {
    /**
     * Dirección de un archivo de public (CSS, JS, logo, favicon) con su fecha de modificación (?v=...).
     *
     * Cuando el archivo cambia, cambia la dirección y el navegador baja la versión
     * nueva en lugar de usar la copia vieja que tenía guardada.
     */
    function recurso($ruta)
    {
        return base_url($ruta) . '?v=' . filemtime(FCPATH . $ruta);
    }
}

if (! function_exists('formatear_fecha')) {
    /**
     * Fecha en español, por ejemplo "5 de agosto de 2026". Devuelve '' si la fecha falta o no es válida.
     */
    function formatear_fecha($fecha)
    {
        $tiempo = $fecha ? strtotime($fecha) : false;

        if ($tiempo === false) {
            return '';
        }

        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        return date('j', $tiempo) . ' de ' . $meses[date('n', $tiempo) - 1] . ' de ' . date('Y', $tiempo);
    }
}

if (! function_exists('mes_en_letras')) {
    /**
     * Mes "AAAA-MM" en texto, por ejemplo "julio de 2026".
     */
    function mes_en_letras($anioMes)
    {
        $meses  = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $partes = explode('-', $anioMes);

        return $meses[(int) $partes[1] - 1] . ' de ' . $partes[0];
    }
}

if (! function_exists('minutos_lectura')) {
    /**
     * Minutos de lectura estimados (200 palabras por minuto, mínimo 1). Ignora las etiquetas HTML.
     */
    function minutos_lectura($texto)
    {
        $palabras = preg_split('/\s+/', trim(strip_tags((string) $texto)), -1, PREG_SPLIT_NO_EMPTY);

        return max(1, (int) round(count($palabras) / 200));
    }
}

if (! function_exists('etiqueta_categoria')) {
    /**
     * Nombre visible de una categoría de noticia (ya escapado para HTML).
     */
    function etiqueta_categoria($categoria)
    {
        $etiquetas = [
            'institucionales' => 'Institucionales',
            'academicas'      => 'Académicas',
            'ultimo_momento'  => 'Último momento',
        ];

        return $etiquetas[$categoria] ?? esc($categoria);
    }
}

if (! function_exists('resumen')) {
    /**
     * Texto sin etiquetas HTML, recortado a un largo máximo con "…" al final.
     */
    function resumen($texto, $largo = 160)
    {
        $limpio = trim(preg_replace('/\s+/', ' ', strip_tags((string) $texto)));

        if (mb_strlen($limpio) <= $largo) {
            return $limpio;
        }

        return mb_substr($limpio, 0, $largo - 1) . '…';
    }
}

if (! function_exists('es_pdf')) {
    /**
     * Indica si un nombre de archivo termina en .pdf.
     */
    function es_pdf($archivo)
    {
        return strtolower(pathinfo($archivo, PATHINFO_EXTENSION)) === 'pdf';
    }
}

if (! function_exists('url_pagina')) {
    /**
     * URL actual con otro número de página, conservando los demás filtros (?q=, ?mes=, ...).
     */
    function url_pagina($pagina)
    {
        $parametros = service('request')->getGet();
        $parametros['pagina'] = $pagina;

        return current_url() . '?' . http_build_query($parametros);
    }
}

if (! function_exists('contenido_a_html')) {
    /**
     * HTML seguro del contenido de una noticia o un posteo.
     *
     * Los contenidos cargados con Quill son HTML y se sanitizan; los más viejos
     * están en Markdown y se convierten.
     */
    function contenido_a_html($contenido)
    {
        $contenido = (string) $contenido;

        if (preg_match('/<\s*\/?\s*(p|br|h[1-3]|ol|ul|li|strong|b|em|i|u|s|blockquote|a|pre|img|iframe)\b/i', $contenido)) {
            return contenido_html_seguro($contenido);
        }

        return markdown_a_html($contenido);
    }
}

if (! function_exists('contenido_html_seguro')) {
    /**
     * Limpia el HTML del editor Quill para que no pueda ejecutar código en el sitio.
     *
     * Solo deja lo que genera el editor: párrafos, títulos, listas, negrita,
     * cursiva, citas, enlaces, imágenes, bloques de código, videos de YouTube o
     * Vimeo y las clases de Quill para alineación y tipografía.
     */
    function contenido_html_seguro($contenido)
    {
        // El <meta charset> hace que DOMDocument lea bien las tildes y la ñ.
        $documento = new DOMDocument();
        libxml_use_internal_errors(true);
        $documento->loadHTML(
            '<html><head><meta charset="utf-8"></head><body><div id="contenido">' . $contenido . '</div></body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $raiz = $documento->getElementById('contenido');
        limpiar_nodo_html($raiz);

        $html = '';
        foreach ($raiz->childNodes as $hijo) {
            $html .= $documento->saveHTML($hijo);
        }

        return $html;
    }
}

if (! function_exists('limpiar_nodo_html')) {
    /**
     * Recorre los hijos de un nodo HTML y deja solo lo permitido (usada por contenido_html_seguro).
     */
    function limpiar_nodo_html($nodo)
    {
        $permitidas = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'h1', 'h2', 'h3', 'blockquote', 'ol', 'ul', 'li', 'a', 'span', 'pre', 'img'];
        $peligrosas = ['script', 'style', 'object', 'embed', 'svg', 'math', 'template'];
        $documento  = $nodo->ownerDocument;

        // Se copia la lista porque se van a quitar nodos mientras se recorre.
        foreach (iterator_to_array($nodo->childNodes) as $hijo) {
            // El texto se conserva; comentarios y otros nodos se quitan.
            if (! $hijo instanceof DOMElement) {
                if ($hijo->nodeType !== XML_TEXT_NODE) {
                    $nodo->removeChild($hijo);
                }
                continue;
            }

            $etiqueta = strtolower($hijo->tagName);

            // Etiquetas peligrosas: se borran con todo su contenido.
            if (in_array($etiqueta, $peligrosas)) {
                $nodo->removeChild($hijo);
                continue;
            }

            // Bloque de código de Quill: un <div> por línea dentro de otro <div>. Se guarda como <pre> con solo el texto.
            if ($etiqueta === 'div' && str_contains($hijo->getAttribute('class'), 'ql-code-block-container')) {
                $lineas = [];
                foreach ($hijo->childNodes as $linea) {
                    $lineas[] = $linea->textContent;
                }
                $pre = $documento->createElement('pre');
                $pre->appendChild($documento->createTextNode(implode("\n", $lineas)));
                $nodo->replaceChild($pre, $hijo);
                continue;
            }

            // Videos: solo los reproductores de YouTube o Vimeo; cualquier otro iframe se borra.
            if ($etiqueta === 'iframe') {
                $src = trim($hijo->getAttribute('src'));
                if (preg_match('#^https://(www\.youtube(-nocookie)?\.com/embed/[\w-]+|player\.vimeo\.com/video/\d+/?)(\?[\w=&;.%-]*)?$#', $src)) {
                    $video = $documento->createElement('iframe');
                    $video->setAttribute('class', 'ql-video');
                    $video->setAttribute('src', $src);
                    $video->setAttribute('title', 'Video');
                    $video->setAttribute('allowfullscreen', 'true');
                    $video->setAttribute('loading', 'lazy');
                    $nodo->replaceChild($video, $hijo);
                } else {
                    $nodo->removeChild($hijo);
                }
                continue;
            }

            // Clases de Quill que se conservan: alineación y tipografía.
            $clases = [];
            foreach (preg_split('/\s+/', $hijo->getAttribute('class')) as $clase) {
                if (preg_match('/^ql-(align-(center|right|justify)|font-(serif|monospace))$/', $clase)) {
                    $clases[] = $clase;
                }
            }

            // Etiquetas no permitidas (div, ...) y <span> sin tipografía: se quitan pero se conserva su contenido.
            if (! in_array($etiqueta, $permitidas) || ($etiqueta === 'span' && $clases === [])) {
                limpiar_nodo_html($hijo);
                while ($hijo->firstChild) {
                    $nodo->insertBefore($hijo->firstChild, $hijo);
                }
                $nodo->removeChild($hijo);
                continue;
            }

            // Se quitan todos los atributos y se vuelven a poner solo los permitidos.
            $href     = trim($hijo->getAttribute('href'));
            $dataList = $hijo->getAttribute('data-list');
            $src      = trim($hijo->getAttribute('src'));
            $alt      = $hijo->getAttribute('alt');
            $ancho    = trim($hijo->getAttribute('width'));

            while ($hijo->attributes->length > 0) {
                $hijo->removeAttributeNode($hijo->attributes->item(0));
            }

            if ($clases !== []) {
                $hijo->setAttribute('class', implode(' ', $clases));
            }

            // Imágenes: solo direcciones http(s) o del sitio (nunca "data:" ni "javascript:").
            if ($etiqueta === 'img') {
                if (! preg_match('/^(https?:\/\/|\/(?!\/))/i', $src)) {
                    $nodo->removeChild($hijo);
                    continue;
                }
                $hijo->setAttribute('src', $src);
                $hijo->setAttribute('alt', $alt);

                // Tamaño elegido en el editor: un porcentaje (25%, 50%...) o píxeles.
                if (preg_match('/^(\d{1,4}|\d{1,3}%)$/', $ancho)) {
                    $hijo->setAttribute('width', $ancho);
                }
            }

            // Direcciones escritas sin "https://", como www.ejemplo.com o ejemplo.com.ar/pagina.
            if (preg_match('/^[a-z0-9-]+(\.[a-z0-9-]+)+([\/?#]\S*)?$/i', $href)) {
                $href = 'https://' . $href;
            }

            // Enlaces: solo http(s), mailto, anclas o rutas del sitio (nunca "javascript:" ni "//otro-sitio").
            // Los de otros sitios se abren en otra pestaña, igual que en el editor.
            if ($etiqueta === 'a' && preg_match('/^(https?:\/\/|mailto:|\/(?!\/)|#)/i', $href)) {
                $hijo->setAttribute('href', $href);

                if (preg_match('/^https?:\/\//i', $href)) {
                    $hijo->setAttribute('target', '_blank');
                    $hijo->setAttribute('rel', 'noopener noreferrer');
                }
            }

            // Quill marca así las listas con viñetas o numeradas.
            if ($etiqueta === 'li' && in_array($dataList, ['bullet', 'ordered'])) {
                $hijo->setAttribute('data-list', $dataList);
            }

            limpiar_nodo_html($hijo);
        }
    }
}

if (! function_exists('markdown_a_html')) {
    /**
     * Convierte en HTML el Markdown de los contenidos viejos (antes de usar Quill).
     *
     * Primero escapa todo el texto, así no puede traer etiquetas propias, y
     * después arma títulos (#, ##, ###), listas (- o 1.) y párrafos.
     */
    function markdown_a_html($texto)
    {
        $texto   = htmlspecialchars(str_replace("\r", '', (string) $texto), ENT_QUOTES, 'UTF-8');
        $html    = '';
        $parrafo = ''; // Texto del párrafo que se está armando.
        $lista   = ''; // 'ul' u 'ol' si hay una lista abierta.

        $lineas   = explode("\n", $texto);
        $lineas[] = ''; // Una línea vacía al final cierra lo que haya quedado abierto.

        foreach ($lineas as $linea) {
            $linea     = trim($linea);
            $tipoLista = '';

            if (preg_match('/^[-*]\s+(.+)$/', $linea, $item)) {
                $tipoLista = 'ul';
            } elseif (preg_match('/^\d+\.\s+(.+)$/', $linea, $item)) {
                $tipoLista = 'ol';
            }

            $esTitulo = preg_match('/^(#{1,3})\s+(.+)$/', $linea, $titulo);
            $esTexto  = $linea !== '' && $tipoLista === '' && ! $esTitulo;

            // Cierra el párrafo si la línea no lo continúa.
            if (! $esTexto && $parrafo !== '') {
                $html .= '<p>' . markdown_inline($parrafo) . '</p>';
                $parrafo = '';
            }

            // Cierra la lista si la línea no es otro ítem de la misma lista.
            if ($lista !== '' && $tipoLista !== $lista) {
                $html .= '</' . $lista . '>';
                $lista = '';
            }

            if ($tipoLista !== '') {
                if ($lista === '') {
                    $html .= '<' . $tipoLista . '>';
                    $lista = $tipoLista;
                }
                $html .= '<li>' . markdown_inline($item[1]) . '</li>';
            } elseif ($esTitulo) {
                $nivel = strlen($titulo[1]);
                $html .= '<h' . $nivel . '>' . markdown_inline($titulo[2]) . '</h' . $nivel . '>';
            } elseif ($esTexto) {
                $parrafo = $parrafo === '' ? $linea : $parrafo . ' ' . $linea;
            }
        }

        return $html;
    }
}

if (! function_exists('markdown_inline')) {
    /**
     * Formato dentro de una línea de Markdown: `código`, **negrita**, *cursiva* y [enlaces](url).
     *
     * Solo acepta enlaces http(s), mailto, anclas o rutas del sitio.
     */
    function markdown_inline($texto)
    {
        $texto = preg_replace('/`([^`]+)`/', '<code>$1</code>', $texto);
        $texto = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $texto);
        $texto = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $texto);

        return preg_replace('/\[([^\]]+)\]\(((?:https?:\/\/|\/(?!\/)|#|mailto:)[^)\s]*)\)/', '<a href="$2">$1</a>', $texto);
    }
}

if (! function_exists('portada_textos')) {
    /**
     * Textos y botones de la portada del sitio, con el texto original para cada campo que esté vacío.
     *
     * Lo usan la portada (publico/partes/hero) y el formulario del panel, que
     * muestra estos mismos textos originales como ejemplo en cada campo.
     *
     * @param array $instituto Fila de configuracion (puede faltar alguna columna).
     */
    function portada_textos($instituto)
    {
        $originales = [
            'portada_etiqueta'      => 'Gestión estatal · Título oficial',
            'portada_titulo'        => 'Formá tu futuro profesional en el Instituto Superior Gaspar Benavento',
            'portada_texto'         => 'Tecnicaturas con título oficial y prácticas profesionalizantes desde el primer año y una comunidad que acompaña cada etapa de tu formación.',
            'portada_boton1_texto'  => 'Ver oferta académica',
            'portada_boton1_enlace' => '#oferta',
            'portada_boton2_texto'  => 'Ver noticias',
            'portada_boton2_enlace' => '#noticias',
        ];

        $textos = [];

        foreach ($originales as $campo => $original) {
            $valor           = trim((string) ($instituto[$campo] ?? ''));
            $textos[$campo]  = $valor === '' ? $original : $valor;
        }

        $textos['portada_boton2_visible'] = (int) ($instituto['portada_boton2_visible'] ?? 1) === 1;
        $textos['originales']             = $originales;

        return $textos;
    }
}

if (! function_exists('enlace_portada')) {
    /**
     * Dirección de un botón de la portada: "#ancla" y http(s) quedan igual; una ruta del sitio ("carreras") pasa por base_url().
     */
    function enlace_portada($enlace)
    {
        if (str_starts_with($enlace, '#') || preg_match('/^https?:\/\//i', $enlace)) {
            return $enlace;
        }

        return base_url(ltrim($enlace, '/'));
    }
}
