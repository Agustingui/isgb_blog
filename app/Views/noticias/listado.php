<?php
/**
 * Listado de noticias con filtro por categoría y paginación.
 *
 * @var array $noticias Noticias de la página actual.
 * @var string|null $categoria Categoría filtrada.
 * @var int $pagina Página actual.
 * @var int $total_paginas Total de páginas.
 */
?>
<div class="container py-5">
    <a href="<?= base_url() ?>" class="volver"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span> Volver al inicio</a>

    <p class="etiqueta-seccion mt-4 mb-2">Noticias</p>
    <h1 class="h2">Actualidad institucional</h1>

    <nav class="d-flex flex-wrap gap-2 mt-3" aria-label="Filtrar por categoría">
        <a href="<?= base_url('noticias') ?>" class="filtro-tag<?= $categoria === null ? ' activo' : '' ?>">Todas</a>
        <a href="<?= base_url('noticias?categoria=institucionales') ?>" class="filtro-tag<?= $categoria === 'institucionales' ? ' activo' : '' ?>">Institucionales</a>
        <a href="<?= base_url('noticias?categoria=academicas') ?>" class="filtro-tag<?= $categoria === 'academicas' ? ' activo' : '' ?>">Académicas</a>
        <a href="<?= base_url('noticias?categoria=ultimo_momento') ?>" class="filtro-tag<?= $categoria === 'ultimo_momento' ? ' activo' : '' ?>">Último momento</a>
    </nav>

    <?php if ($noticias === []): ?>
        <p class="texto-suave mt-4">Todavía no hay noticias publicadas.</p>
    <?php else: ?>
        <div class="row g-4 mt-2">
            <?php foreach ($noticias as $noticia): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="tarjeta d-flex flex-column overflow-hidden">
                        <a href="<?= base_url('noticias/' . $noticia['slug']) ?>" class="tarjeta-foto<?= $noticia['imagen_archivo'] ? '' : ' foto-institucional' ?>" tabindex="-1" aria-hidden="true">
                            <?php if ($noticia['imagen_archivo']): ?>
                                <img src="<?= base_url('uploads/' . $noticia['imagen_archivo']) ?>" alt="" loading="lazy">
                            <?php endif ?>
                        </a>

                        <div class="p-4 d-flex flex-column gap-2 flex-grow-1">
                            <span class="chip align-self-start"><?= etiqueta_categoria($noticia['categoria']) ?></span>
                            <h2 class="h5 mb-0">
                                <a href="<?= base_url('noticias/' . $noticia['slug']) ?>" class="text-reset"><?= esc($noticia['titulo']) ?></a>
                            </h2>
                            <p class="small texto-suave mb-0"><?= esc($noticia['bajada']) ?></p>
                            <span class="meta mt-auto">
                                <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                                <time datetime="<?= esc($noticia['publicado_en']) ?>"><?= formatear_fecha($noticia['publicado_en']) ?></time>
                            </span>
                        </div>
                    </article>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <?= view('publico/partes/paginacion', ['pagina' => $pagina, 'total_paginas' => $total_paginas]) ?>
</div>
