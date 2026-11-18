<?php
/**
 * "Mi cuenta": datos, cambio de contraseña y borrado de la cuenta.
 *
 * @var array $usuario Fila del usuario actual.
 */
?>
<div class="container py-5 detalle">
    <h1 class="h2 mb-4">Mi cuenta</h1>

    <section class="tarjeta p-4 mb-4" aria-labelledby="cuenta-datos">
        <h2 class="h5" id="cuenta-datos">Mis datos</h2>
        <form action="<?= base_url('cuenta/actualizar') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="<?= esc($usuario['nombre']) ?>" maxlength="120" autocomplete="name" required>
            </div>
            <p class="small texto-suave">Email: <?= esc($usuario['email']) ?> · Rol: <?= esc($usuario['rol']) ?></p>
            <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
        </form>
    </section>

    <section class="tarjeta p-4 mb-4" aria-labelledby="cuenta-password">
        <h2 class="h5" id="cuenta-password">Cambiar contraseña</h2>
        <form action="<?= base_url('cuenta/password') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="password_actual">Contraseña actual</label>
                <input type="password" name="password_actual" id="password_actual" class="form-control" autocomplete="current-password" aria-describedby="ayuda-password-actual" required>
                <div class="form-text" id="ayuda-password-actual">Si ingresaste siempre con Google y nunca creaste una contraseña, usá "Olvidé mi contraseña" en la pantalla de ingreso.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password_nuevo">Contraseña nueva</label>
                <input type="password" name="password_nuevo" id="password_nuevo" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="confirmar">Repetí la contraseña nueva</label>
                <input type="password" name="confirmar" id="confirmar" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Cambiar contraseña</button>
        </form>
    </section>

    <section class="tarjeta p-4 border-danger" aria-labelledby="cuenta-borrar">
        <h2 class="h5 text-danger" id="cuenta-borrar">Borrar mi cuenta</h2>
        <p class="small texto-suave">
            Se borran tu usuario y tus reacciones. No se puede deshacer.
            Más información en la <a href="<?= base_url('privacidad') ?>">política de privacidad</a>.
        </p>
        <form action="<?= base_url('cuenta/borrar') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="confirmacion">Para confirmar, escribí la palabra BORRAR</label>
                <input type="text" name="confirmacion" id="confirmacion" class="form-control" autocomplete="off" pattern="BORRAR" required>
            </div>
            <button type="submit" class="btn btn-danger btn-sm">Borrar mi cuenta</button>
        </form>
    </section>
</div>
