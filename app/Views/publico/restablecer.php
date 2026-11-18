<?php
/**
 * Formulario para elegir una contraseña nueva.
 *
 * @var string $token Token del enlace recibido por email.
 */
?>
<div class="container py-5">
    <section class="caja-login mx-auto" aria-labelledby="restablecer-titulo">
        <h1 class="h3 mb-1" id="restablecer-titulo">Nueva contraseña</h1>
        <p class="texto-suave small mb-4">Elegí una contraseña de al menos 8 caracteres.</p>

        <form action="<?= base_url('login/restablecer') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= esc($token) ?>">

            <div class="mb-3">
                <label class="form-label" for="password">Contraseña nueva</label>
                <input type="password" name="password" id="password" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label" for="confirmar">Repetí la contraseña</label>
                <input type="password" name="confirmar" id="confirmar" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Guardar contraseña</button>
        </form>
    </section>
</div>
