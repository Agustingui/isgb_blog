<?php
/**
 * Parcial: elegir o subir una imagen (portada de noticias y posteos, imagen de inicio del sitio).
 *
 * Permite elegir una imagen de la biblioteca o subir una nueva sin salir del
 * formulario (campo "imagen_nueva"); la vista previa la maneja admin.js. El
 * formulario que lo incluye debe usar enctype="multipart/form-data".
 *
 * @var list<array{id: string, alt: string, archivo: string}> $imagenes Imágenes de la biblioteca.
 * @var string|int|null                                       $actual   Id de la imagen elegida.
 * @var string|null                                           $campo    Nombre del select (por defecto "imagen_id").
 * @var string|null                                           $leyenda  Título del bloque (por defecto "Imagen de portada").
 * @var string|null                                           $ayuda    Texto de ayuda adicional (opcional).
 * @var bool|null                                             $compacto true = todo en una columna (columna lateral de noticias y posteos).
 * @var bool|null                                             $ocultarLeyenda true = la leyenda solo la leen los lectores de pantalla (cuando la tarjeta ya tiene ese título).
 */
$actual      = (string) ($actual ?? '');
$campo       = $campo ?? 'imagen_id';
$leyenda     = $leyenda ?? 'Imagen de portada';
$ayuda       = $ayuda ?? '';
$compacto    = $compacto ?? false;
$ocultarLeyenda = $ocultarLeyenda ?? $compacto;
$vistaActual = '';

foreach ($imagenes as $imagen) {
    if ((string) $imagen['id'] === $actual) {
        $vistaActual = base_url('uploads/' . $imagen['archivo']);
    }
}
?>
<fieldset class="col-12" data-imagen-portada>
    <legend class="form-label fs-6 mb-2<?= $ocultarLeyenda ? ' visually-hidden' : '' ?>"><?= esc($leyenda) ?></legend>
    <div class="row g-3">
        <div class="<?= $compacto ? 'col-12' : 'col-md-3 order-md-last' ?>">
            <figure class="vista-imagen mb-0" aria-live="polite">
                <img src="<?= $vistaActual ?>" alt="Vista previa de la imagen"<?= $vistaActual === '' ? ' hidden' : '' ?>>
                <figcaption<?= $vistaActual === '' ? '' : ' hidden' ?>>
                    <span class="text-center">
                        <span class="material-symbols-outlined d-block fs-2 mb-1" aria-hidden="true">add_photo_alternate</span>
                        Sin imagen
                    </span>
                </figcaption>
            </figure>
        </div>
        <div class="<?= $compacto ? 'col-12' : 'col-md-4' ?>">
            <label class="form-label small text-secondary" for="<?= esc($campo) ?>">Elegir una ya subida</label>
            <select name="<?= esc($campo) ?>" id="<?= esc($campo) ?>" class="form-select">
                <option value="" data-src="">Sin imagen</option>
                <?php foreach ($imagenes as $imagen): ?>
                    <option value="<?= esc($imagen['id']) ?>" data-src="<?= base_url('uploads/' . $imagen['archivo']) ?>"<?= (string) $imagen['id'] === $actual ? ' selected' : '' ?>><?= esc($imagen['alt']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="<?= $compacto ? 'col-12' : 'col-md-5' ?>">
            <label class="form-label small text-secondary" for="imagen_nueva">O subir una nueva</label>
            <input type="file" name="imagen_nueva" id="imagen_nueva" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" aria-describedby="ayuda-imagen-nueva">
            <div class="form-text" id="ayuda-imagen-nueva">JPG, PNG, GIF o WEBP de hasta 5 MB. Reemplaza a la elegida y queda guardada en Archivos.<?= $ayuda !== '' ? ' ' . esc($ayuda) : '' ?></div>
        </div>
    </div>
</fieldset>
