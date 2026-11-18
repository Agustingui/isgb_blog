<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n" ?>
<?php
/**
 * Feed RSS 2.0. Se renderiza con debug desactivado para no romper el XML.
 *
 * @var list<array{titulo:string,enlace:string,descripcion:string,fecha:?string}> $items Ítems ordenados por fecha.
 */
?>
<rss version="2.0">
    <channel>
        <title>Instituto Superior Gaspar Benavento</title>
        <link><?= esc(base_url()) ?></link>
        <description>Noticias y posteos del instituto</description>
        <language>es-ar</language>
        <?php foreach ($items as $item): ?>
            <item>
                <title><?= esc($item['titulo']) ?></title>
                <link><?= esc($item['enlace']) ?></link>
                <guid><?= esc($item['enlace']) ?></guid>
                <description><?= esc($item['descripcion']) ?></description>
                <?php if ($item['fecha']): ?>
                    <pubDate><?= date(DATE_RSS, strtotime($item['fecha'])) ?></pubDate>
                <?php endif ?>
            </item>
        <?php endforeach ?>
    </channel>
</rss>
