<?php
/**
 * Formulario de subida de archivos.
 */
?>
<?= view('admin/partes/encabezado', [
    'titulo'      => 'Subir archivo',
    'descripcion' => 'Queda guardado en Archivos para usarlo en noticias, posteos, carreras o la portada.',
    'volver'      => 'admin/media',
    'textoVolver' => 'Archivos',
]) ?>

<form action="<?= base_url('admin/media/insertar') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <section class="card">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-lg-7">
                    <label class="form-label" for="archivo">Archivo</label>
                    <div class="zona-archivo">
                        <span class="material-symbols-outlined" aria-hidden="true">cloud_upload</span>
                        <p class="mb-0 fw-medium">Elegí una imagen o un PDF</p>
                        <p class="small text-secondary mb-0" id="ayuda-archivo">JPG, PNG, GIF, WEBP o PDF. Máximo 5 MB.</p>
                        <input type="file" name="archivo" id="archivo" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf" aria-describedby="ayuda-archivo" required>
                    </div>
                </div>
                <div class="col-lg-5">
                    <label class="form-label" for="alt">Descripción del archivo</label>
                    <input type="text" name="alt" id="alt" class="form-control" value="<?= old('alt') ?>" maxlength="200" aria-describedby="ayuda-alt" placeholder="Ej: Acto de colación 2026" required>
                    <div class="form-text" id="ayuda-alt">Describí lo que se ve: se usa como texto alternativo para lectores de pantalla y para encontrarlo después en los selectores.</div>
                </div>
            </div>
        </div>
    </section>

    <?= view('admin/partes/botones_formulario', ['cancelar' => 'admin/media', 'textoGuardar' => 'Subir archivo']) ?>
</form>
