<?php
/**
 * Parcial: barra con Guardar y Cancelar al pie de los formularios.
 *
 * Es "sticky": en formularios largos queda visible al pie de la pantalla,
 * así no hay que bajar hasta el final para guardar.
 *
 * @var string|null $cancelar    Ruta del botón Cancelar (sin botón si no se indica).
 * @var string|null $textoGuardar Texto del botón principal (por defecto "Guardar").
 */
$textoGuardar = $textoGuardar ?? 'Guardar';
?>
<div class="barra-acciones">
    <button type="submit" class="btn btn-primary px-4">
        <span class="material-symbols-outlined" aria-hidden="true">check</span> <?= esc($textoGuardar) ?>
    </button>
    <?php if (isset($cancelar)): ?>
        <a href="<?= base_url($cancelar) ?>" class="btn btn-suave">Cancelar</a>
    <?php endif ?>
    <span class="ayuda">Los cambios se aplican al guardar.</span>
</div>
