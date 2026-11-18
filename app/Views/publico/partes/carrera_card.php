<?php
/**
 * Parcial: tarjeta de una carrera.
 *
 * @var array $carrera Fila de la tabla carreras.
 */
?>
<article class="tarjeta p-4 d-flex flex-column gap-3">
    <h3 class="h5 mb-0"><?= esc($carrera['nombre']) ?></h3>
    <p class="meta fw-semibold mb-0">
        <span class="material-symbols-outlined" aria-hidden="true">schedule</span> <?= esc($carrera['duracion']) ?>
    </p>
    <p class="small texto-suave mb-0"><?= esc($carrera['descripcion']) ?></p>
    <a href="<?= base_url('carreras/' . $carrera['slug']) ?>" class="enlace-flecha mt-auto">
        Ver plan de estudios <span class="visually-hidden">de <?= esc($carrera['nombre']) ?></span>
        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
    </a>
</article>
