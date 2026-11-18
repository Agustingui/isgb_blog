<?php
/**
 * Listado de carreras.
 *
 * @var array $carreras Todas las carreras.
 */
?>
<?= view('admin/partes/encabezado', [
    'titulo'      => 'Carreras',
    'descripcion' => 'Oferta académica con su plan de estudios. Las inactivas no se muestran en el sitio.',
    'nuevo'       => 'admin/carreras/nuevo',
    'textoNuevo'  => 'Nueva carrera',
    'papelera'    => 'admin/papelera/carreras',
]) ?>

<?php if ($carreras === []): ?>
    <?= view('admin/partes/vacio', [
        'icono'       => 'school',
        'titulo'      => 'Todavía no hay carreras',
        'texto'       => 'Cargá cada carrera con su duración, descripción, plan de estudios y PDF.',
        'accion'      => 'admin/carreras/nuevo',
        'textoAccion' => 'Cargar la primera carrera',
    ]) ?>
<?php else: ?>
    <div class="card card-listado">
        <div class="card-body">
            <table class="table table-hover align-middle tabla-datos" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Duración</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carreras as $carrera): ?>
                        <tr>
                            <td class="celda-principal">
                                <a href="<?= base_url('admin/carreras/editar/' . $carrera['id']) ?>"><?= esc($carrera['nombre']) ?></a>
                                <span class="secundario">/carreras/<?= esc($carrera['slug']) ?></span>
                            </td>
                            <td class="text-nowrap"><?= esc($carrera['duracion']) ?></td>
                            <td data-order="<?= (int) $carrera['activa'] ?>">
                                <?php if ((int) $carrera['activa'] === 1): ?>
                                    <span class="insignia insignia-verde">Activa</span>
                                <?php else: ?>
                                    <span class="insignia insignia-gris">Inactiva</span>
                                <?php endif ?>
                            </td>
                            <td class="text-end">
                                <div class="acciones-fila">
                                    <?php if ((int) $carrera['activa'] === 1): ?>
                                        <a href="<?= base_url('carreras/' . $carrera['slug']) ?>" class="btn btn-icono" title="Ver en el sitio" target="_blank" rel="noopener">
                                            <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>
                                            <span class="visually-hidden">Ver <?= esc($carrera['nombre']) ?> en el sitio</span>
                                        </a>
                                    <?php endif ?>
                                    <a href="<?= base_url('admin/carreras/editar/' . $carrera['id']) ?>" class="btn btn-icono" title="Editar">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        <span class="visually-hidden">Editar <?= esc($carrera['nombre']) ?></span>
                                    </a>
                                    <?= view('admin/partes/boton_borrar', ['url' => 'admin/carreras/borrar/' . $carrera['id'], 'nombre' => $carrera['nombre']]) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
