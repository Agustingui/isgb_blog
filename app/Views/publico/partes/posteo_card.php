<?php
/**
 * Parcial: tarjeta de un posteo del blog.
 *
 * @var array $posteo Posteo con autor e imagen_archivo (BaseController::consultaPosteos()).
 * @var int|null $nivel_titulo Nivel del encabezado del título (2 o 3; 3 por defecto).
 */
$autor = $posteo['autor'] ?: 'Redacción ISGB';
$h     = 'h' . ($nivel_titulo ?? 3);
?>
<article class="tarjeta d-flex flex-column overflow-hidden">
    <a href="<?= base_url('blog/' . $posteo['slug']) ?>" class="tarjeta-foto<?= $posteo['imagen_archivo'] ? '' : ' foto-institucional' ?>" tabindex="-1" aria-hidden="true">
        <?php if ($posteo['imagen_archivo']): ?>
            <img src="<?= base_url('uploads/' . $posteo['imagen_archivo']) ?>" alt="" loading="lazy">
        <?php endif ?>
    </a>

    <div class="p-4 d-flex flex-column gap-3 flex-grow-1">
        <<?= $h ?> class="h5 mb-0">
            <a href="<?= base_url('blog/' . $posteo['slug']) ?>" class="text-reset"><?= esc($posteo['titulo']) ?></a>
        </<?= $h ?>>
        <p class="small texto-suave mb-0"><?= esc($posteo['extracto']) ?></p>

        <footer class="d-flex align-items-center gap-2 border-top pt-3 mt-auto">
            <div class="lh-sm">
                <strong class="small d-block"><?= esc($autor) ?></strong>
                <span class="meta">
                    <time datetime="<?= esc($posteo['publicado_en']) ?>"><?= formatear_fecha($posteo['publicado_en']) ?></time>
                    · <?= minutos_lectura($posteo['contenido']) ?> min de lectura
                </span>
            </div>
        </footer>
    </div>
</article>
