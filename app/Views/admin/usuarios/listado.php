<?php
/**
 * Listado de usuarios.
 *
 * @var array $usuarios Todos los usuarios.
 */

// Color de la insignia de cada rol.
$roles = [
    'admin'    => ['Admin', 'insignia-oscura'],
    'editor'   => ['Editor', 'insignia-celeste'],
    'alumnado' => ['Alumnado', 'insignia-gris'],
];
?>
<?= view('admin/partes/encabezado', [
    'titulo'      => 'Usuarios',
    'descripcion' => 'Cuentas que pueden ingresar al sitio y, según su rol, a este panel.',
    'nuevo'       => 'admin/usuarios/nuevo',
    'textoNuevo'  => 'Nuevo usuario',
    'papelera'    => 'admin/papelera/usuarios',
]) ?>

<div class="card card-listado">
    <div class="card-body">
        <table class="table table-hover align-middle tabla-datos" style="width: 100%;">
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Rol</th>
                    <th scope="col" class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <?php $rol = $roles[$usuario['rol']] ?? [ucfirst($usuario['rol']), 'insignia-gris'] ?>
                    <tr>
                        <td class="celda-principal">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar" aria-hidden="true"><?= esc(mb_substr($usuario['nombre'], 0, 1)) ?></span>
                                <div style="min-width: 0;">
                                    <a href="<?= base_url('admin/usuarios/editar/' . $usuario['id']) ?>"><?= esc($usuario['nombre']) ?></a>
                                    <?php if ((int) $usuario['id'] === (int) session('id_usuario')): ?>
                                        <span class="insignia insignia-celeste ms-1">Vos</span>
                                    <?php endif ?>
                                    <span class="secundario"><?= esc($usuario['email']) ?></span>
                                </div>
                            </div>
                        </td>
                        <td data-order="<?= esc($usuario['rol']) ?>"><span class="insignia <?= $rol[1] ?>"><?= esc($rol[0]) ?></span></td>
                        <td class="text-end">
                            <div class="acciones-fila">
                                <a href="<?= base_url('admin/usuarios/editar/' . $usuario['id']) ?>" class="btn btn-icono" title="Editar">
                                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                    <span class="visually-hidden">Editar <?= esc($usuario['nombre']) ?></span>
                                </a>
                                <?= view('admin/partes/boton_borrar', ['url' => 'admin/usuarios/borrar/' . $usuario['id'], 'nombre' => $usuario['nombre']]) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
