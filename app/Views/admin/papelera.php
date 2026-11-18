<?php
/**
 * Papelera de un listado del panel: lo borrado, con botones para restaurar o borrar definitivamente.
 *
 * @var string $tipo    Tabla (noticias, posteos, carreras, media o usuarios).
 * @var array  $filas   Filas con fecha_borrado.
 * @var string $columna Columna con el nombre de cada fila.
 * @var string $listado Ruta del listado del panel.
 * @var string $titulo  Título de la página.
 */
?>
<?= view('admin/partes/encabezado', [
    'titulo'      => $titulo,
    'descripcion' => 'Lo borrado queda acá y no se muestra en el sitio. "Restaurar" lo devuelve tal como estaba; "Borrar definitivamente" no se puede deshacer.',
    'volver'      => $listado,
]) ?>

<?php if ($filas === []): ?>
    <?= view('admin/partes/vacio', [
        'icono'  => 'delete',
        'titulo' => 'La papelera está vacía',
        'texto'  => 'Cuando borres algo desde el listado, va a aparecer acá por si querés recuperarlo.',
    ]) ?>
<?php else: ?>
    <div class="card card-listado">
        <div class="card-body">
            <table class="table table-hover align-middle tabla-datos" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Borrado el</th>
                        <th scope="col" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filas as $fila): ?>
                        <tr>
                            <td class="celda-principal">
                                <strong class="fw-semibold"><?= esc($fila[$columna]) ?></strong>
                                <?php if ($tipo === 'usuarios'): ?>
                                    <span class="secundario"><?= esc($fila['email']) ?></span>
                                <?php endif ?>
                            </td>
                            <td class="text-nowrap" data-order="<?= esc($fila['fecha_borrado']) ?>"><?= formatear_fecha($fila['fecha_borrado']) ?></td>
                            <td class="text-end">
                                <div class="acciones-fila align-items-center">
                                    <form action="<?= base_url('admin/papelera/restaurar/' . $tipo . '/' . $fila['id']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <span class="material-symbols-outlined" aria-hidden="true">restore_from_trash</span> Restaurar
                                            <span class="visually-hidden"><?= esc($fila[$columna]) ?></span>
                                        </button>
                                    </form>
                                    <?= view('admin/partes/boton_borrar', [
                                        'url'    => 'admin/papelera/eliminar/' . $tipo . '/' . $fila['id'],
                                        'nombre' => $fila[$columna],
                                        'aviso'  => 'Se borra para siempre y no se puede deshacer.',
                                    ]) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
