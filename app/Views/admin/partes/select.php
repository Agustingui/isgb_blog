<?php
/**
 * Parcial: campo select de Bootstrap con su etiqueta.
 *
 * @var string               $nombre   Atributo name e id del select.
 * @var string               $etiqueta Texto del label.
 * @var array<string,string> $opciones Valor => texto visible.
 * @var string|int|null      $actual   Valor seleccionado.
 * @var string|null          $vacio    Texto de la opción vacía (null = sin opción vacía).
 * @var string               $columna  Clases de la columna de la grilla.
 */
$columna = $columna ?? 'col-md-6';
$vacio   = $vacio ?? null;
?>
<div class="<?= $columna ?>">
    <label class="form-label" for="<?= $nombre ?>"><?= esc($etiqueta) ?></label>
    <select name="<?= $nombre ?>" id="<?= $nombre ?>" class="form-select">
        <?php if ($vacio !== null): ?>
            <option value=""><?= esc($vacio) ?></option>
        <?php endif ?>
        <?php foreach ($opciones as $valor => $texto): ?>
            <option value="<?= esc((string) $valor) ?>"<?= (string) $actual === (string) $valor ? ' selected' : '' ?>><?= esc($texto) ?></option>
        <?php endforeach ?>
    </select>
</div>
