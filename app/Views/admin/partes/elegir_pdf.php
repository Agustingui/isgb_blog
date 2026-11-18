<?php
/**
 * Parcial: elegir un PDF de la biblioteca o subir uno nuevo (plan de estudios y resolución de las carreras).
 *
 * El formulario que lo incluye debe usar enctype="multipart/form-data".
 *
 * @var string                                                 $campo   Nombre del select (por ejemplo "plan_pdf_id").
 * @var string                                                 $archivo Nombre del campo para subir uno nuevo (por ejemplo "plan_pdf_nuevo").
 * @var string                                                 $leyenda Título del bloque.
 * @var list<array{id: string, alt: string, archivo: string}> $pdfs    PDF de la biblioteca.
 * @var string|int|null                                        $actual  Id del PDF elegido.
 */
$actual    = (string) ($actual ?? '');
$enlaceVer = '';

foreach ($pdfs as $pdf) {
    if ((string) $pdf['id'] === $actual) {
        $enlaceVer = base_url('uploads/' . $pdf['archivo']);
    }
}
?>
<fieldset class="col-md-6">
    <legend class="form-label fs-6 mb-2"><?= esc($leyenda) ?></legend>
    <label class="form-label small text-secondary" for="<?= esc($campo) ?>">Elegir uno ya subido</label>
    <select name="<?= esc($campo) ?>" id="<?= esc($campo) ?>" class="form-select">
        <option value="">Sin PDF</option>
        <?php foreach ($pdfs as $pdf): ?>
            <option value="<?= esc($pdf['id']) ?>"<?= (string) $pdf['id'] === $actual ? ' selected' : '' ?>><?= esc($pdf['alt']) ?></option>
        <?php endforeach ?>
    </select>
    <?php if ($enlaceVer !== ''): ?>
        <a href="<?= $enlaceVer ?>" target="_blank" rel="noopener" class="small d-block mt-1">Ver el PDF actual</a>
    <?php endif ?>

    <label class="form-label small text-secondary mt-2" for="<?= esc($archivo) ?>">O subir uno nuevo</label>
    <input type="file" name="<?= esc($archivo) ?>" id="<?= esc($archivo) ?>" class="form-control" accept=".pdf,application/pdf" aria-describedby="ayuda-<?= esc($archivo) ?>">
    <div class="form-text" id="ayuda-<?= esc($archivo) ?>">PDF de hasta 10 MB. Reemplaza al elegido y queda guardado en Archivos.</div>
</fieldset>
