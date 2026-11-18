<?php
/**
 * PDF de una carrera (plan de estudios o resolución) dentro de una página del sitio.
 *
 * @var array  $carrera   Fila de la tabla carreras.
 * @var array  $pdf       Archivo de la tabla media.
 * @var string $documento "Plan de estudios" o "Resolución".
 */
$enlace = base_url('uploads/' . $pdf['archivo']);
?>
<article class="container py-5">
    <a href="<?= base_url('carreras/' . $carrera['slug']) ?>" class="volver"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span> Volver a <?= esc($carrera['nombre']) ?></a>

    <header class="d-flex flex-wrap justify-content-between align-items-end gap-3 mt-4 mb-3">
        <div>
            <p class="meta fw-semibold mb-1"><?= esc($carrera['nombre']) ?></p>
            <h1 class="h2 mb-0"><?= esc($documento) ?></h1>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= $enlace ?>" class="btn btn-isgb-borde" download="<?= esc($documento . ' - ' . $carrera['nombre']) ?>.pdf">
                <span class="material-symbols-outlined" aria-hidden="true">download</span> Descargar
            </a>
            <a href="<?= $enlace ?>" class="btn btn-isgb-borde" target="_blank" rel="noopener">
                <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span> Abrir solo el PDF
            </a>
        </div>
    </header>

    <?php // Algunos celulares no muestran PDF dentro de la página: para eso están los botones de arriba. ?>
    <iframe src="<?= $enlace ?>" class="visor-pdf" title="<?= esc($documento . ' - ' . $carrera['nombre']) ?>"></iframe>
</article>
