<?php
/**
 * Formulario para pedir el enlace de recuperación de contraseña.
 */
?>
<div class="container py-5">
    <section class="caja-login mx-auto" aria-labelledby="olvide-titulo">
        <h1 class="h3 mb-1" id="olvide-titulo">Recuperar contraseña</h1>
        <p class="texto-suave small mb-4">Ingresá tu email y te mandamos un enlace para crear una contraseña nueva.</p>

        <form action="<?= base_url('login/olvide') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label class="form-label" for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" autocomplete="email" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary w-100">Enviar enlace</button>
        </form>

        <p class="text-center small mt-3 mb-0"><a href="<?= base_url('login') ?>">Volver a ingresar</a></p>
    </section>
</div>
