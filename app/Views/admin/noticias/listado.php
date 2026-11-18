<?php
/**
 * Listado de noticias (DataTables, borrado por AJAX solo para admin).
 *
 * @var array $noticias Todas las noticias.
 */

// Color de la insignia de cada categoría.
$colores = ['institucionales' => 'insignia-celeste', 'academicas' => 'insignia-verde', 'ultimo_momento' => 'insignia-ambar'];
?>
<?= view('admin/partes/encabezado', [
    'titulo'      => 'Noticias',
    'descripcion' => 'Novedades del instituto que se publican en la sección Noticias del sitio.',
    'nuevo'       => 'admin/noticias/nuevo',
    'textoNuevo'  => 'Nueva noticia',
    'papelera'    => 'admin/papelera/noticias',
]) ?>

<?php if ($noticias === []): ?>
    <?= view('admin/partes/vacio', [
        'icono'       => 'newspaper',
        'titulo'      => 'Todavía no hay noticias',
        'texto'       => 'Las noticias aparecen en el sitio ordenadas por fecha de publicación.',
        'accion'      => 'admin/noticias/nuevo',
        'textoAccion' => 'Cargar la primera noticia',
    ]) ?>
<?php else: ?>
    <div class="card card-listado">
        <div class="card-body">
            <table class="table table-hover align-middle tabla-datos" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Categoría</th>
                        <th scope="col">Publicada</th>
                        <th scope="col" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($noticias as $noticia): ?>
                        <tr>
                            <td class="celda-principal">
                                <a href="<?= base_url('admin/noticias/editar/' . $noticia['id']) ?>"><?= esc($noticia['titulo']) ?></a>
                                <span class="secundario"><?= esc($noticia['bajada']) ?></span>
                            </td>
                            <td><span class="insignia <?= $colores[$noticia['categoria']] ?? 'insignia-gris' ?>"><?= etiqueta_categoria($noticia['categoria']) ?></span></td>
                            <td class="text-nowrap" data-order="<?= esc($noticia['publicado_en']) ?>"><?= $noticia['publicado_en'] ? formatear_fecha($noticia['publicado_en']) : '<span class="text-secondary">Sin fecha</span>' ?></td>
                            <td class="text-end">
                                <div class="acciones-fila">
                                    <a href="<?= base_url('noticias/' . $noticia['slug']) ?>" class="btn btn-icono" title="Ver en el sitio" target="_blank" rel="noopener">
                                        <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>
                                        <span class="visually-hidden">Ver <?= esc($noticia['titulo']) ?> en el sitio</span>
                                    </a>
                                    <a href="<?= base_url('admin/noticias/editar/' . $noticia['id']) ?>" class="btn btn-icono" title="Editar">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        <span class="visually-hidden">Editar <?= esc($noticia['titulo']) ?></span>
                                    </a>
                                    <?php if (session('rol') === 'admin'): ?>
                                        <?= view('admin/partes/boton_borrar', ['url' => 'admin/noticias/borrar/' . $noticia['id'], 'nombre' => $noticia['titulo']]) ?>
                                    <?php endif ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
