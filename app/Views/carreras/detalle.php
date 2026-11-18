<?php
/**
 * Detalle de una carrera con su plan de estudios.
 *
 * @var array $carrera Fila de la tabla carreras.
 * @var array<int,list<string>> $planes Año => materias.
 * @var array|null $imagen Imagen de la carrera.
 * @var array|null $pdf PDF del plan de estudios.
 * @var array|null $resolucion PDF de la resolución de la carrera.
 */
?>
<article class="container detalle-carrera py-5">
    <a href="<?= base_url('carreras') ?>" class="volver"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span> Volver a las carreras</a>

    <header>
        <p class="meta fw-semibold mt-4 mb-1">
            <span class="material-symbols-outlined" aria-hidden="true">schedule</span> <?= esc($carrera['duracion']) ?> · Presencial
        </p>
        <h1 class="h2"><?= esc($carrera['nombre']) ?></h1>
    </header>

    <?php if ($imagen): ?>
        <img src="<?= base_url('uploads/' . $imagen['archivo']) ?>" alt="<?= esc($imagen['alt']) ?>" class="img-fluid rounded my-4">
    <?php endif ?>

    <p class="lead texto-suave mt-4"><?= esc($carrera['descripcion']) ?></p>

    <?php if ($pdf || $resolucion): ?>
        <div class="d-flex flex-wrap gap-4 mt-3">
            <?php if ($pdf): ?>
                <a href="<?= base_url('carreras/' . $carrera['slug'] . '/plan') ?>" class="enlace-flecha">
                    <span class="material-symbols-outlined" aria-hidden="true">description</span> Plan de estudios (PDF)
                </a>
            <?php endif ?>
            <?php if ($resolucion): ?>
                <a href="<?= base_url('carreras/' . $carrera['slug'] . '/resolucion') ?>" class="enlace-flecha">
                    <span class="material-symbols-outlined" aria-hidden="true">gavel</span> Resolución (PDF)
                </a>
            <?php endif ?>
        </div>
    <?php endif ?>

    <?php if ($planes !== []): ?>
        <section class="mt-5" aria-labelledby="plan-titulo">
            <h2 class="h4 mb-3" id="plan-titulo">Plan de estudios</h2>

            <div class="row g-4">
                <?php foreach ($planes as $anio => $materias): ?>
                    <div class="col-sm-6 col-lg-3">
                        <div class="anio-plan">
                            <h3>Año <?= (int) $anio ?></h3>
                            <ul class="list-unstyled mt-3 mb-0">
                                <?php foreach ($materias as $materia): ?>
                                    <li><?= esc($materia) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </section>
    <?php endif ?>

    <aside class="caja-contacto mt-5">
        <p class="small texto-suave mb-0">
            ¿Te interesa esta carrera? <a href="#contacto" class="fw-semibold">Contactanos</a>
            para conocer fechas de inscripción y requisitos.
        </p>
    </aside>
</article>
