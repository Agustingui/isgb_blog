<?php
/**
 * Detalle de un posteo con "me gusta" y registro de lectura (AJAX en publico.js).
 *
 * @var array $posteo Posteo con autor, imagen_archivo e imagen_alt (contenido HTML de Quill o Markdown viejo).
 * @var bool $me_gusta Si el usuario actual ya marcó "me gusta".
 * @var bool $ya_leido Si la lectura del usuario ya está registrada.
 * @var int $total_me_gusta Total de "me gusta".
 * @var int $total_vistas Total de lecturas.
 */
$autor    = $posteo['autor'] ?: 'Redacción ISGB';
$logueado = (bool) session('rol');
?>
<article class="container detalle py-5">
    <a href="<?= base_url('blog') ?>" class="volver"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span> Volver al blog</a>

    <header>
        <h1 class="h2 mt-3"><?= esc($posteo['titulo']) ?></h1>

        <div class="d-flex align-items-center gap-3 mt-3 mb-4">
            <div class="lh-sm">
                <strong class="small d-block"><?= esc($autor) ?></strong>
                <span class="meta">
                    <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                    <time datetime="<?= esc($posteo['publicado_en']) ?>"><?= formatear_fecha($posteo['publicado_en']) ?></time>
                    &nbsp;
                    <span class="material-symbols-outlined" aria-hidden="true">schedule</span> <?= minutos_lectura($posteo['contenido']) ?> min de lectura
                </span>
            </div>
        </div>
    </header>

    <?php if ($posteo['imagen_archivo']): ?>
        <img src="<?= base_url('uploads/' . $posteo['imagen_archivo']) ?>" alt="<?= esc($posteo['imagen_alt']) ?>" class="img-fluid rounded mb-4">
    <?php endif ?>

    <div class="contenido-enriquecido"><?= contenido_a_html($posteo['contenido']) ?></div>

<?php // --- Reacciones: "me gusta" por AJAX (ver publico.js) --- ?>
    <footer class="reacciones d-flex flex-wrap align-items-center gap-3 mt-5 pt-4 border-top">
        <?php if ($logueado): ?>
            <form action="<?= base_url('blog/me-gusta/' . $posteo['id']) ?>" method="post" class="js-me-gusta">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm <?= $me_gusta ? 'btn-danger' : 'btn-outline-danger' ?>" aria-pressed="<?= $me_gusta ? 'true' : 'false' ?>">
                    <span class="material-symbols-outlined icono-lleno" aria-hidden="true">favorite</span> Me gusta
                </button>
            </form>
        <?php else: ?>
            <a href="<?= base_url('login') ?>" class="btn btn-sm btn-outline-danger">
                <span class="material-symbols-outlined" aria-hidden="true">favorite</span> Ingresá para dar me gusta
            </a>
        <?php endif ?>

        <p class="meta mb-0" aria-live="polite">
            <span id="total-me-gusta"><?= (int) $total_me_gusta ?></span> me gusta ·
            <span id="total-vistas"><?= (int) $total_vistas ?></span> lecturas
        </p>
    </footer>

<?php // --- Disparador del registro de lectura --- ?>
    <?php if ($logueado && ! $ya_leido): ?>
        <div id="lector" hidden data-url="<?= base_url('blog/vista/' . $posteo['id']) ?>" data-segundos="60"></div>
    <?php endif ?>
</article>
