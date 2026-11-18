<?php
/**
 * Detalle de una noticia (contenido HTML sanitizado o Markdown).
 *
 * @var array $noticia Noticia con autor, imagen_archivo e imagen_alt.
 */
?>
<article class="container detalle py-5">
    <a href="<?= base_url('noticias') ?>" class="volver"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span> Volver a noticias</a>

    <header>
        <p class="mt-4 mb-0"><span class="chip chip-oscuro"><?= etiqueta_categoria($noticia['categoria']) ?></span></p>
        <h1 class="h2 mt-3"><?= esc($noticia['titulo']) ?></h1>
        <p class="lead texto-suave mt-3"><?= esc($noticia['bajada']) ?></p>

        <p class="meta d-flex flex-wrap gap-3 mb-4">
            <span>
                <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                <time datetime="<?= esc($noticia['publicado_en']) ?>"><?= formatear_fecha($noticia['publicado_en']) ?></time>
            </span>
            <span><span class="material-symbols-outlined" aria-hidden="true">schedule</span> <?= minutos_lectura($noticia['contenido']) ?> min de lectura</span>
            <?php if ($noticia['autor']): ?>
                <span><span class="material-symbols-outlined" aria-hidden="true">person</span> <?= esc($noticia['autor']) ?></span>
            <?php endif ?>
        </p>
    </header>

    <?php if ($noticia['imagen_archivo']): ?>
        <img src="<?= base_url('uploads/' . $noticia['imagen_archivo']) ?>" alt="<?= esc($noticia['imagen_alt']) ?>" class="img-fluid rounded mb-4">
    <?php endif ?>

    <div class="contenido-enriquecido"><?= contenido_a_html($noticia['contenido']) ?></div>
</article>
