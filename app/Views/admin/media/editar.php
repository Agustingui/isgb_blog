<?php
/**
 * Formulario para cambiar la descripción de un archivo o reemplazarlo por otro del mismo tipo.
 *
 * @var array $archivo Fila de la tabla media.
 */
$esPdf  = es_pdf($archivo['archivo']);
$url    = base_url('uploads/' . $archivo['archivo']);
$acepta = $esPdf ? '.pdf,application/pdf' : '.jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp';
?>
<?= view('admin/partes/encabezado', ['titulo' => 'Editar archivo', 'volver' => 'admin/media', 'textoVolver' => 'Archivos']) ?>

<form action="<?= base_url('admin/media/actualizar') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) $archivo['id'] ?>">

    <section class="card">
        <div class="card-body p-4">
            <div class="row g-4">
<?php // --- Archivo actual --- ?>
                <div class="col-md-5 col-lg-4">
                    <p class="form-label mb-2">Archivo actual</p>
                    <?php if ($esPdf): ?>
                        <a href="<?= $url ?>" target="_blank" rel="noopener" class="archivo-actual-pdf">
                            <span class="material-symbols-outlined fs-1 text-danger" aria-hidden="true">picture_as_pdf</span>
                            <span><?= esc($archivo['archivo']) ?></span>
                        </a>
                    <?php else: ?>
                        <figure class="vista-imagen mb-2">
                            <img src="<?= $url ?>" alt="<?= esc($archivo['alt']) ?>">
                        </figure>
                        <a href="<?= $url ?>" target="_blank" rel="noopener" class="small">
                            <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span> Ver en tamaño completo
                        </a>
                    <?php endif ?>
                </div>

<?php // --- Datos editables --- ?>
                <div class="col-md-7 col-lg-8">
                    <div class="mb-4">
                        <label class="form-label" for="alt">Descripción del archivo</label>
                        <input type="text" name="alt" id="alt" class="form-control" value="<?= old('alt', esc($archivo['alt'])) ?>" maxlength="200" aria-describedby="ayuda-alt" required>
                        <div class="form-text" id="ayuda-alt">Se usa como texto alternativo y para encontrarlo en los selectores.</div>
                    </div>

                    <div>
                        <label class="form-label" for="archivo">Reemplazar por otro archivo <span class="opcional">(opcional)</span></label>
                        <input type="file" name="archivo" id="archivo" class="form-control" accept="<?= $acepta ?>" aria-describedby="ayuda-archivo">
                        <div class="form-text" id="ayuda-archivo">
                            <?= $esPdf ? 'Un PDF' : 'Una imagen JPG, PNG, GIF o WEBP' ?> de hasta 5 MB.
                        </div>
                        <div class="alert alert-info small d-flex gap-2 mt-3 mb-0">
                            <span class="material-symbols-outlined" aria-hidden="true">info</span>
                            <span>Las noticias, posteos, carreras y la portada que usan este archivo van a mostrar el nuevo automáticamente.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?= view('admin/partes/botones_formulario', ['cancelar' => 'admin/media', 'textoGuardar' => 'Guardar cambios']) ?>
</form>
