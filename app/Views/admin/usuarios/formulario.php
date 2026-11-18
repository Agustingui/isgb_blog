<?php
/**
 * Formulario de alta y edición de usuarios.
 *
 * El rol se elige con tarjetas que explican qué puede hacer cada uno, para
 * no tener que adivinar la diferencia entre "Admin" y "Editor".
 *
 * @var array|null $usuario Usuario a editar, o null para uno nuevo.
 * @var string $titulo Título de la página.
 */
$editando = $usuario !== null;
$rolActual = old('rol', $usuario['rol'] ?? 'alumnado');

$roles = [
    'admin'    => ['Admin', 'Todo el panel: carreras, usuarios, datos del instituto y Papelera.'],
    'editor'   => ['Editor', 'Carga y edita noticias, posteos y archivos. No puede borrar noticias ni posteos.'],
    'alumnado' => ['Alumnado', 'No entra al panel. Puede dar "me gusta" y administrar su cuenta.'],
];
?>
<?= view('admin/partes/encabezado', ['titulo' => $titulo, 'volver' => 'admin/usuarios', 'textoVolver' => 'Usuarios']) ?>

<form action="<?= base_url('admin/usuarios/' . ($editando ? 'actualizar' : 'insertar')) ?>" method="post" autocomplete="off">
    <?= csrf_field() ?>
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">
    <?php endif ?>

    <div class="d-flex flex-column gap-4">
        <section class="card">
            <div class="card-header"><h2 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">badge</span> Datos de la cuenta</h2></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="nombre">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="<?= old('nombre', esc($usuario['nombre'] ?? '')) ?>" maxlength="120" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= old('email', esc($usuario['email'] ?? '')) ?>" maxlength="150" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password"><?= $editando ? 'Contraseña nueva' : 'Contraseña' ?><?= $editando ? ' <span class="opcional">(opcional)</span>' : '' ?></label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" aria-describedby="ayuda-password"<?= $editando ? '' : ' required' ?>>
                            <button type="button" class="btn btn-suave" data-ver-password="#password" aria-pressed="false" title="Mostrar la contraseña">
                                <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                                <span class="visually-hidden">Mostrar la contraseña</span>
                            </button>
                        </div>
                        <div class="form-text" id="ayuda-password"><?= $editando ? 'Dejala vacía si no querés cambiarla.' : 'Entre 8 y 72 caracteres.' ?></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header"><h2 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">admin_panel_settings</span> Rol</h2></div>
            <fieldset class="card-body p-4">
                <legend class="visually-hidden">Rol del usuario</legend>
                <div class="opciones-rol">
                    <?php foreach ($roles as $valor => $rol): ?>
                        <label class="opcion-rol" for="rol-<?= $valor ?>">
                            <input class="form-check-input" type="radio" name="rol" id="rol-<?= $valor ?>" value="<?= $valor ?>"<?= (string) $rolActual === $valor ? ' checked' : '' ?>>
                            <strong><?= $rol[0] ?></strong>
                            <span><?= esc($rol[1]) ?></span>
                        </label>
                    <?php endforeach ?>
                </div>
            </fieldset>
        </section>
    </div>

    <?= view('admin/partes/botones_formulario', ['cancelar' => 'admin/usuarios', 'textoGuardar' => $editando ? 'Guardar cambios' : 'Crear usuario']) ?>
</form>
