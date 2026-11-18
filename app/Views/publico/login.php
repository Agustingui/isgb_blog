<?php
/**
 * Formulario de ingreso: email y contraseña, Google y enlace al registro de alumnos.
 *
 * @var bool $google   Si se muestra el botón de ingreso con Google.
 * @var bool $registro Si el registro público está abierto (auth.registroAbierto).
 */
?>
<div class="container py-5">
    <section class="caja-login mx-auto" aria-labelledby="login-titulo">
        <h1 class="h3 mb-1" id="login-titulo">Ingresar</h1>
        <p class="texto-suave small mb-4">Entrá con tu usuario del instituto.</p>

        <form action="<?= base_url('login/ingresar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" autocomplete="username" required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
        </form>

        <p class="text-center small mt-3 mb-0"><a href="<?= base_url('login/olvide') ?>">Olvidé mi contraseña</a></p>

        <?php if ($google || $registro): ?>
            <div class="separador-o my-3"><span>o</span></div>
        <?php endif ?>

        <?php if ($google): ?>
            <a href="<?= base_url('login/google') ?>" class="btn btn-isgb-borde w-100 mb-2">
                <span class="material-symbols-outlined" aria-hidden="true">account_circle</span> Continuar con Google
            </a>
        <?php endif ?>

        <?php if ($registro): ?>
            <a href="<?= base_url('registro') ?>" class="btn btn-isgb-borde w-100">
                <span class="material-symbols-outlined" aria-hidden="true">person_add</span> Crear una cuenta de alumno
            </a>
        <?php endif ?>
    </section>
</div>
