<?php
/**
 * Listado público de carreras activas.
 *
 * @var array $carreras Carreras activas.
 */
?>
<div class="container py-5">
    <a href="<?= base_url() ?>" class="volver"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span> Volver al inicio</a>

    <p class="etiqueta-seccion mt-4 mb-2">Oferta académica</p>
    <h1 class="h2">Nuestras carreras</h1>
    <p class="texto-suave texto-angosto">
        Tecnicaturas superiores con planes actualizados y prácticas formativas desde el primer año.
        Todas nuestras carreras se cursan de forma presencial.
    </p>

    <?php if ($carreras === []): ?>
        <p class="texto-suave mt-4">Todavía no hay carreras activas cargadas.</p>
    <?php else: ?>
        <div class="row g-4 mt-2">
            <?php foreach ($carreras as $carrera): ?>
                <div class="col-md-6 col-lg-4">
                    <?= view('publico/partes/carrera_card', ['carrera' => $carrera]) ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>
