<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n" ?>
<?php
/**
 * Sitemap XML. Se renderiza con debug desactivado para no romper el XML.
 *
 * @var list<array{enlace:string,fecha:?string}> $urls URLs públicas.
 */
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($urls as $url): ?>
        <url>
            <loc><?= esc($url['enlace']) ?></loc>
            <?php if ($url['fecha']): ?>
                <lastmod><?= date('Y-m-d', strtotime($url['fecha'])) ?></lastmod>
            <?php endif ?>
        </url>
    <?php endforeach ?>
</urlset>
