<?php
/**
 * Listado de archivos subidos con miniatura, peso y resolución.
 *
 * @var array $archivos Filas de la tabla media, más peso (bytes), ancho y alto (px), que
 *                      AdminMedia::index() lee del archivo; son null si el archivo no está o es un PDF.
 */
?>
<?= view('admin/partes/encabezado', [
    'titulo'      => 'Archivos',
    'descripcion' => 'Imágenes y PDF que se usan en noticias, posteos, carreras y la portada.',
    'nuevo'       => 'admin/media/nuevo',
    'textoNuevo'  => 'Subir archivo',
    'papelera'    => 'admin/papelera/media',
]) ?>

<?php if ($archivos === []): ?>
    <?= view('admin/partes/vacio', [
        'icono'       => 'perm_media',
        'titulo'      => 'Todavía no hay archivos',
        'texto'       => 'También podés subir imágenes directamente desde el formulario de una noticia o un posteo.',
        'accion'      => 'admin/media/nuevo',
        'textoAccion' => 'Subir el primer archivo',
    ]) ?>
<?php else: ?>
    <div class="card card-listado">
        <div class="card-body">
            <table class="table table-hover align-middle tabla-datos" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col" style="width: 90px;">Vista</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Peso</th>
                        <th scope="col">Resolución</th>
                        <th scope="col" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($archivos as $archivo): ?>
                        <?php $esPdf = es_pdf($archivo['archivo']) ?>
                        <tr>
                            <td>
                                <a href="<?= base_url('uploads/' . $archivo['archivo']) ?>" target="_blank" rel="noopener" title="Abrir el archivo">
                                    <?php if ($esPdf): ?>
                                        <span class="miniatura-pdf"><span class="material-symbols-outlined" aria-hidden="true">picture_as_pdf</span></span>
                                    <?php else: ?>
                                        <img src="<?= base_url('uploads/' . $archivo['archivo']) ?>" alt="" class="miniatura" loading="lazy">
                                    <?php endif ?>
                                    <span class="visually-hidden">Abrir <?= esc($archivo['alt']) ?></span>
                                </a>
                            </td>
                            <td class="celda-principal">
                                <a href="<?= base_url('admin/media/editar/' . $archivo['id']) ?>"><?= esc($archivo['alt']) ?></a>
                                <span class="secundario"><?= esc($archivo['archivo']) ?></span>
                            </td>
                            <td>
                                <?php if ($esPdf): ?>
                                    <span class="insignia insignia-ambar">PDF</span>
                                <?php else: ?>
                                    <span class="insignia insignia-celeste">Imagen</span>
                                <?php endif ?>
                            </td>
                            <?php // data-order: DataTables ordena por bytes y por píxeles, no por el texto. ?>
                            <td class="text-nowrap" data-order="<?= (int) $archivo['peso'] ?>">
                                <?php if ($archivo['peso'] === null): ?>
                                    <span class="text-secondary" title="El archivo no está en public/uploads de esta compu">Sin archivo</span>
                                <?php else: ?>
                                    <?= peso_legible($archivo['peso']) ?>
                                <?php endif ?>
                            </td>
                            <td class="text-nowrap" data-order="<?= (int) $archivo['ancho'] * (int) $archivo['alto'] ?>">
                                <?php if ($archivo['ancho'] !== null): ?>
                                    <?= (int) $archivo['ancho'] ?> × <?= (int) $archivo['alto'] ?> px
                                <?php else: ?>
                                    <span class="text-secondary">—</span>
                                <?php endif ?>
                            </td>
                            <td class="text-end">
                                <div class="acciones-fila">
                                    <a href="<?= base_url('uploads/' . $archivo['archivo']) ?>" class="btn btn-icono" title="Abrir el archivo" target="_blank" rel="noopener">
                                        <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>
                                        <span class="visually-hidden">Abrir <?= esc($archivo['alt']) ?></span>
                                    </a>
                                    <a href="<?= base_url('admin/media/editar/' . $archivo['id']) ?>" class="btn btn-icono" title="Editar">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        <span class="visually-hidden">Editar <?= esc($archivo['alt']) ?></span>
                                    </a>
                                    <?= view('admin/partes/boton_borrar', ['url' => 'admin/media/borrar/' . $archivo['id'], 'nombre' => $archivo['alt']]) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
