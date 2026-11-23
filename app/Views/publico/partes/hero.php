<?php
/**
 * Parcial: encabezado de la portada con la insignia de inscripción.
 *
 * Los textos y botones se editan en el panel ("Datos del instituto"); los
 * campos vacíos muestran el texto original (ver portada_textos()).
 *
 * @var array $instituto Datos del instituto (incluye portada_archivo y portada_alt si hay imagen de portada).
 */
$portada = portada_textos($instituto);

// Los enlaces a otro sitio (como el campus) se abren en otra pestaña, así no se pierde la portada.
$atributosEnlace = static fn ($enlace) => preg_match('/^https?:\/\//i', $enlace) ? ' target="_blank" rel="noopener"' : '';
?>
<section class="hero" aria-labelledby="hero-titulo">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <p class="etiqueta-seccion text-white-50"><?= esc($portada['portada_etiqueta']) ?></p>
                <h1 class="mt-3" id="hero-titulo"><?= esc($portada['portada_titulo']) ?></h1>
                <p class="lead mt-4"><?= esc($portada['portada_texto']) ?></p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="<?= esc(enlace_portada($portada['portada_boton1_enlace'])) ?>" class="btn btn-blanco px-4 py-2"<?= $atributosEnlace($portada['portada_boton1_enlace']) ?>>
                        <?= esc($portada['portada_boton1_texto']) ?> <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                    </a>
                    <?php if ($portada['portada_boton2_visible']): ?>
                        <a href="<?= esc(enlace_portada($portada['portada_boton2_enlace'])) ?>" class="btn btn-blanco-borde px-4 py-2"<?= $atributosEnlace($portada['portada_boton2_enlace']) ?>><?= esc($portada['portada_boton2_texto']) ?></a>
                    <?php endif ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="hero-imagen">
                    <?php if ($instituto['portada_archivo']): ?>
                        <img src="<?= base_url('uploads/' . $instituto['portada_archivo']) ?>" alt="<?= esc($instituto['portada_alt']) ?>" class="hero-foto">
                    <?php else: ?>
                        <span class="material-symbols-outlined" aria-hidden="true">school</span>
                        <strong>ISGB</strong>
                        <small>Instituto Superior</small>
                    <?php endif ?>
                    <?php if ($instituto['inscripcion_abierta'] && $instituto['inscripcion_texto']): ?>
                        <span class="hero-insignia">
                            <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span> <?= esc($instituto['inscripcion_texto']) ?>
                        </span>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</section>
