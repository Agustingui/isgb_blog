<?php
/**
 * Parcial: noticias de la portada (una destacada y dos secundarias).
 *
 * @var array $noticias Noticias con imagen_archivo.
 */
?>
<section id="noticias" class="seccion bg-white" aria-labelledby="noticias-titulo">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <p class="etiqueta-seccion mb-2">Noticias</p>
                <h2 class="h3" id="noticias-titulo">Actualidad institucional</h2>
            </div>
            <a href="<?= base_url('noticias') ?>" class="enlace-flecha">
                Ver todas las noticias <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
            </a>
        </div>

        <?php if ($noticias === []): ?>
            <p class="texto-suave">Todavía no hay noticias publicadas.</p>
        <?php else: ?>
            <?php $destacada = $noticias[0]; ?>
            <div class="row g-4">
                <div class="col-lg-7">
                    <article class="noticia-destacada d-flex flex-column gap-3">
                        <a href="<?= base_url('noticias/' . $destacada['slug']) ?>" class="tarjeta-foto<?= $destacada['imagen_archivo'] ? '' : ' foto-institucional' ?>" tabindex="-1">
                            <?php if ($destacada['imagen_archivo']): ?>
                                <img src="<?= base_url('uploads/' . $destacada['imagen_archivo']) ?>" alt="">
                            <?php endif ?>
                            <span class="chip chip-oscuro"><?= etiqueta_categoria($destacada['categoria']) ?></span>
                        </a>
                        <h3 class="h3">
                            <a href="<?= base_url('noticias/' . $destacada['slug']) ?>" class="text-reset"><?= esc($destacada['titulo']) ?></a>
                        </h3>
                        <p class="texto-suave mb-0"><?= esc($destacada['bajada']) ?></p>
                        <p class="meta d-flex gap-3 mb-0">
                            <span><span class="material-symbols-outlined" aria-hidden="true">calendar_month</span> <time datetime="<?= esc($destacada['publicado_en']) ?>"><?= formatear_fecha($destacada['publicado_en']) ?></time></span>
                            <span><span class="material-symbols-outlined" aria-hidden="true">schedule</span> <?= minutos_lectura($destacada['contenido']) ?> min de lectura</span>
                        </p>
                    </article>
                </div>

                <div class="col-lg-5 d-flex flex-column gap-4">
                    <?php foreach (array_slice($noticias, 1, 2) as $noticia): ?>
                        <article class="noticia-secundaria d-flex gap-3">
                            <a href="<?= base_url('noticias/' . $noticia['slug']) ?>" class="foto-mini<?= $noticia['imagen_archivo'] ? '' : ' foto-institucional' ?>" tabindex="-1" aria-hidden="true">
                                <?php if ($noticia['imagen_archivo']): ?>
                                    <img src="<?= base_url('uploads/' . $noticia['imagen_archivo']) ?>" alt="" loading="lazy">
                                <?php endif ?>
                            </a>
                            <div class="d-flex flex-column gap-1">
                                <span class="chip align-self-start"><?= etiqueta_categoria($noticia['categoria']) ?></span>
                                <h3 class="h6 mb-0">
                                    <a href="<?= base_url('noticias/' . $noticia['slug']) ?>" class="text-reset"><?= esc($noticia['titulo']) ?></a>
                                </h3>
                                <span class="meta"><span class="material-symbols-outlined" aria-hidden="true">calendar_month</span> <time datetime="<?= esc($noticia['publicado_en']) ?>"><?= formatear_fecha($noticia['publicado_en']) ?></time></span>
                            </div>
                        </article>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</section>
