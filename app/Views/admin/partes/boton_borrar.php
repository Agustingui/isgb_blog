<?php
/**
 * Parcial: botón que abre el modal de borrado (ver iniciarBorrado() en admin.js).
 *
 * @var string      $url    Ruta de borrado relativa a base_url().
 * @var string      $nombre Nombre del registro para el mensaje de confirmación.
 * @var string|null $aviso  Texto que se agrega a la pregunta (por defecto, que queda en la Papelera).
 */
$aviso = $aviso ?? 'Va a quedar en la Papelera, desde donde se puede restaurar.';
?>
<button type="button" class="btn btn-icono btn-borrar" title="Borrar"
    data-url="<?= base_url($url) ?>"
    data-nombre="<?= esc($nombre) ?>"
    data-aviso="<?= esc($aviso) ?>">
    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
    <span class="visually-hidden">Borrar <?= esc($nombre) ?></span>
</button>
