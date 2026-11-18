<?php
/**
 * Formulario público para crear una cuenta de alumno.
 *
 * @var bool $google Si se muestra el botón de ingreso con Google.
 */
$errores = session()->getFlashdata('errores') ?? [];
?>
<div class="container py-5">
    <section class="caja-login mx-auto" aria-labelledby="registro-titulo">
        <h1 class="h3 mb-1" id="registro-titulo">Crear cuenta de alumno</h1>
        <p class="texto-suave small mb-4">Con tu cuenta podés dar “me gusta” en el blog.</p>

        <?php if ($errores !== []): ?>
            <div class="alert alert-danger small" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errores as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <form action="<?= base_url('registro') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label" for="nombre">Nombre y apellido</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="<?= old('nombre') ?>" maxlength="120" autocomplete="name" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= old('email') ?>" maxlength="150" autocomplete="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" aria-describedby="ayuda-password" required>
                <div class="form-text" id="ayuda-password">Entre 8 y 72 caracteres.</div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="confirmar">Repetí la contraseña</label>
                <input type="password" name="confirmar" id="confirmar" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Crear cuenta de alumno</button>
        </form>

        <?php if ($google): ?>
            <div class="separador-o my-3"><span>o</span></div>
            <a href="<?= base_url('login/google') ?>" class="btn btn-isgb-borde w-100">
                <span class="material-symbols-outlined" aria-hidden="true">account_circle</span> Continuar con Google
            </a>
        <?php endif ?>

        <p class="text-center small mt-3 mb-0">¿Ya tenés cuenta? <a href="<?= base_url('login') ?>">Iniciá sesión</a></p>
        <p class="text-center small texto-suave mt-2 mb-0">Al crear la cuenta aceptás la <a href="<?= base_url('privacidad') ?>">política de privacidad</a>.</p>
    </section>
</div>
