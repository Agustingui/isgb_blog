<?php
/**
 * Parcial: aviso de listado vacío, con la acción para cargar el primer registro.
 *
 * Se muestra en lugar de una tabla vacía, que no explica qué hacer.
 *
 * @var string      $icono  Nombre del ícono de Material Symbols.
 * @var string      $titulo Título del aviso.
 * @var string      $texto  Explicación breve.
 * @var string|null $accion Ruta del botón (null = sin botón).
 * @var string|null $textoAccion Texto del botón.
 */
$accion      = $accion ?? null;
$textoAccion = $textoAccion ?? 'Crear';
?>
<div class="card">
    <div class="estado-vacio">
        <span class="icono" aria-hidden="true"><span class="material-symbols-outlined"><?= esc($icono) ?></span></span>
        <h2><?= esc($titulo) ?></h2>
        <p><?= esc($texto) ?></p>
        <?php if ($accion !== null): ?>
            <a href="<?= base_url($accion) ?>" class="btn btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">add</span> <?= esc($textoAccion) ?>
            </a>
        <?php endif ?>
    </div>
</div>
