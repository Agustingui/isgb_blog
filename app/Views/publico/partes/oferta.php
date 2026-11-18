<?php
/**
 * Parcial: sección "Oferta académica" de la portada.
 *
 * @var array $carreras Carreras a mostrar.
 */
?>
<section id="oferta" class="seccion bg-white" aria-labelledby="oferta-titulo">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <p class="etiqueta-seccion mb-2">Oferta académica</p>
                <h2 class="h3" id="oferta-titulo">Elegí tu carrera</h2>
                <p class="texto-suave texto-angosto mb-0">
                    Tecnicaturas superiores con planes actualizados y prácticas formativas desde el primer año.
                    Todas nuestras carreras se cursan de forma presencial.
                </p>
            </div>
            <a href="<?= base_url('carreras') ?>" class="enlace-flecha">
                Ver todas las carreras <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
            </a>
        </div>

        <?php if ($carreras === []): ?>
            <p class="texto-suave">Todavía no hay carreras cargadas.</p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($carreras as $carrera): ?>
                    <div class="col-md-6 col-lg-4">
                        <?= view('publico/partes/carrera_card', ['carrera' => $carrera]) ?>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
</section>
