<?php
/**
 * Parcial: título de la página del panel, con enlace para volver y botones de acción.
 *
 * "Volver" va arriba del título, como migas de pan: así el botón principal
 * de la derecha queda solo para la acción de la página (crear algo nuevo).
 *
 * @var string      $titulo      Título de la página (h1).
 * @var string|null $descripcion Una línea que explica la página (opcional).
 * @var string|null $volver      Ruta del listado para el enlace "Volver" (null = sin enlace).
 * @var string|null $textoVolver Texto del enlace "Volver" (por defecto "Volver al listado").
 * @var string|null $nuevo       Ruta del formulario de alta (null = sin botón).
 * @var string|null $textoNuevo  Texto del botón de alta.
 * @var string|null $papelera    Ruta de la Papelera de ese listado (solo se muestra a administradores).
 */
$descripcion = $descripcion ?? null;
$volver      = $volver ?? null;
$textoVolver = $textoVolver ?? 'Volver al listado';
$nuevo       = $nuevo ?? null;
$textoNuevo  = $textoNuevo ?? 'Nuevo';
$papelera    = session('rol') === 'admin' ? ($papelera ?? null) : null;
?>
<div class="encabezado-pagina">
    <div>
        <?php if ($volver !== null): ?>
            <a href="<?= base_url($volver) ?>" class="enlace-volver">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span> <?= esc($textoVolver) ?>
            </a>
        <?php endif ?>
        <h1><?= esc($titulo) ?></h1>
        <?php if ($descripcion !== null): ?>
            <p class="descripcion"><?= esc($descripcion) ?></p>
        <?php endif ?>
    </div>

    <?php if ($papelera !== null || $nuevo !== null): ?>
        <div class="d-flex flex-wrap gap-2">
            <?php if ($papelera !== null): ?>
                <a href="<?= base_url($papelera) ?>" class="btn btn-suave">
                    <span class="material-symbols-outlined" aria-hidden="true">delete</span> Papelera
                </a>
            <?php endif ?>
            <?php if ($nuevo !== null): ?>
                <a href="<?= base_url($nuevo) ?>" class="btn btn-primary">
                    <span class="material-symbols-outlined" aria-hidden="true">add</span> <?= esc($textoNuevo) ?>
                </a>
            <?php endif ?>
        </div>
    <?php endif ?>
</div>
