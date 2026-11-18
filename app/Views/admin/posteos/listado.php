<?php
/**
 * Listado de posteos con totales de "me gusta" y lecturas.
 *
 * @var array $posteos Posteos con me_gusta y vistas.
 */

// Texto y color de la insignia de cada estado.
$estados = [
    'publicado' => ['Publicado', 'insignia-verde'],
    'revision'  => ['En revisión', 'insignia-ambar'],
    'borrador'  => ['Borrador', 'insignia-gris'],
];
?>
<?= view('admin/partes/encabezado', [
    'titulo'      => 'Posteos del blog',
    'descripcion' => 'Artículos del blog. Solo los "Publicados" se ven en el sitio.',
    'nuevo'       => 'admin/posteos/nuevo',
    'textoNuevo'  => 'Nuevo posteo',
    'papelera'    => 'admin/papelera/posteos',
]) ?>

<?php if ($posteos === []): ?>
    <?= view('admin/partes/vacio', [
        'icono'       => 'edit_note',
        'titulo'      => 'Todavía no hay posteos',
        'texto'       => 'Podés guardarlos como borrador y publicarlos cuando estén listos.',
        'accion'      => 'admin/posteos/nuevo',
        'textoAccion' => 'Escribir el primer posteo',
    ]) ?>
<?php else: ?>
    <div class="card card-listado">
        <div class="card-body">
            <table class="table table-hover align-middle tabla-datos" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Publicado</th>
                        <th scope="col" class="text-center" title="Me gusta"><span class="material-symbols-outlined" aria-hidden="true">favorite</span><span class="visually-hidden">Me gusta</span></th>
                        <th scope="col" class="text-center" title="Lecturas"><span class="material-symbols-outlined" aria-hidden="true">visibility</span><span class="visually-hidden">Lecturas</span></th>
                        <th scope="col" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posteos as $posteo): ?>
                        <?php $estado = $estados[$posteo['estado']] ?? $estados['borrador'] ?>
                        <tr>
                            <td class="celda-principal">
                                <a href="<?= base_url('admin/posteos/editar/' . $posteo['id']) ?>"><?= esc($posteo['titulo']) ?></a>
                                <span class="secundario"><?= esc($posteo['extracto']) ?></span>
                            </td>
                            <td data-order="<?= esc($posteo['estado']) ?>"><span class="insignia <?= $estado[1] ?>"><?= $estado[0] ?></span></td>
                            <td class="text-nowrap" data-order="<?= esc($posteo['publicado_en']) ?>"><?= $posteo['publicado_en'] ? formatear_fecha($posteo['publicado_en']) : '<span class="text-secondary">Sin fecha</span>' ?></td>
                            <td class="text-center"><?= (int) $posteo['me_gusta'] ?></td>
                            <td class="text-center"><?= (int) $posteo['vistas'] ?></td>
                            <td class="text-end">
                                <div class="acciones-fila">
                                    <?php if ($posteo['estado'] === 'publicado'): ?>
                                        <a href="<?= base_url('blog/' . $posteo['slug']) ?>" class="btn btn-icono" title="Ver en el sitio" target="_blank" rel="noopener">
                                            <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>
                                            <span class="visually-hidden">Ver <?= esc($posteo['titulo']) ?> en el sitio</span>
                                        </a>
                                    <?php endif ?>
                                    <a href="<?= base_url('admin/posteos/editar/' . $posteo['id']) ?>" class="btn btn-icono" title="Editar">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        <span class="visually-hidden">Editar <?= esc($posteo['titulo']) ?></span>
                                    </a>
                                    <?php if (session('rol') === 'admin'): ?>
                                        <?= view('admin/partes/boton_borrar', ['url' => 'admin/posteos/borrar/' . $posteo['id'], 'nombre' => $posteo['titulo']]) ?>
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
