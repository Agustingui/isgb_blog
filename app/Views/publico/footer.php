<?php
/**
 * Layout público: cierra <main>, pie del sitio y scripts.
 *
 * @var array $instituto Datos del instituto (fila de configuracion, armada en BaseController::vistaPublica()).
 * @var array $carreras_pie Carreras activas para el pie.
 */
?>
</main>

<?php // --- Pie del sitio --- ?>
<footer class="pie" id="contacto">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="marca mb-3">
                    <img src="<?= recurso('img/logo.png') ?>" alt="" class="marca-logo" width="44" height="44">
                    <span class="marca-texto">
                        <small>Instituto Superior</small>
                        <strong>Gaspar Benavento</strong>
                    </span>
                </div>
                <p class="small mb-0"><?= esc($instituto['descripcion_pie']) ?></p>
            </div>

            <nav class="col-lg-3 col-md-6" aria-labelledby="pie-oferta">
                <h2 class="pie-titulo" id="pie-oferta">Oferta académica</h2>
                <ul>
                    <?php foreach ($carreras_pie as $carrera): ?>
                        <li><a href="<?= base_url('carreras/' . $carrera['slug']) ?>"><?= esc($carrera['nombre']) ?></a></li>
                    <?php endforeach ?>
                    <li><a href="<?= base_url('carreras') ?>">Ver todas</a></li>
                </ul>
            </nav>

            <nav class="col-lg-2 col-md-6" aria-labelledby="pie-recursos">
                <h2 class="pie-titulo" id="pie-recursos">Recursos</h2>
                <ul>
                    <li><a href="<?= base_url('noticias') ?>">Noticias</a></li>
                    <li><a href="<?= base_url('blog') ?>">Blog</a></li>
                    <li><a href="<?= base_url('privacidad') ?>">Privacidad</a></li>
                </ul>
            </nav>

            <div class="col-lg-3 col-md-6">
                <h2 class="pie-titulo">Contacto</h2>
                <address class="mb-0">
                    <ul>
                        <?php if ($instituto['direccion']): ?>
                            <li><span class="material-symbols-outlined" aria-hidden="true">location_on</span> <?= esc($instituto['direccion']) ?></li>
                        <?php endif ?>
                        <?php if ($instituto['telefono']): ?>
                            <li><span class="material-symbols-outlined" aria-hidden="true">call</span> <?= esc($instituto['telefono']) ?></li>
                        <?php endif ?>
                        <?php if ($instituto['email_contacto']): ?>
                            <li><span class="material-symbols-outlined" aria-hidden="true">mail</span> <a href="mailto:<?= esc($instituto['email_contacto']) ?>"><?= esc($instituto['email_contacto']) ?></a></li>
                        <?php endif ?>
                        <?php if ($instituto['instagram']): ?>
                            <li><span class="material-symbols-outlined" aria-hidden="true">photo_camera</span> <a href="<?= esc($instituto['instagram']) ?>" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                        <?php endif ?>
                        <?php if ($instituto['facebook']): ?>
                            <li><span class="material-symbols-outlined" aria-hidden="true">public</span> <a href="<?= esc($instituto['facebook']) ?>" target="_blank" rel="noopener noreferrer">Facebook</a></li>
                        <?php endif ?>
                </ul>
                </address>
            </div>
        </div>

        <p class="pie-linea mb-0">© <?= date('Y') ?> Instituto Superior Gaspar Benavento</p>
    </div>
</footer>

<?php // --- Scripts --- ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= recurso('assets/js/comun.js') ?>"></script>
<script src="<?= recurso('assets/js/publico.js') ?>"></script>
</body>
</html>
