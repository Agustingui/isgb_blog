<?php
/**
 * Parcial: posteos recientes de la portada.
 *
 * @var array $posteos Posteos con autor e imagen_archivo.
 */
?>
<section id="blog" class="seccion seccion-celeste" aria-labelledby="blog-titulo">
    <div class="container">
        <div class="mb-4">
            <p class="etiqueta-seccion mb-2">Blog</p>
            <h2 class="h3" id="blog-titulo">Posteos recientes</h2>
            <p class="texto-suave texto-angosto mb-0">
                Historias de docentes, estudiantes y egresados: la vida del instituto contada por su comunidad.
            </p>
        </div>

        <?php if ($posteos === []): ?>
            <p class="texto-suave">Todavía no hay posteos publicados.</p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($posteos as $posteo): ?>
                    <div class="col-md-6 col-lg-4">
                        <?= view('publico/partes/posteo_card', ['posteo' => $posteo, 'nivel_titulo' => 3]) ?>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <p class="text-center mt-4 mb-0">
            <a href="<?= base_url('blog') ?>" class="enlace-flecha">
                Ver todos los posteos <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
            </a>
        </p>
    </div>
</section>
