<?php
/**
 * Inicio del panel: accesos rápidos, totales de cada sección y lo último que se cargó.
 *
 * @var int   $total_noticias
 * @var int   $total_posteos
 * @var int   $total_media
 * @var int   $total_carreras
 * @var int   $total_usuarios
 * @var int   $posteos_revision Posteos con estado "revision".
 * @var array $ultimas_noticias Las 5 últimas noticias cargadas (id, titulo, categoria, publicado_en).
 * @var array $ultimos_posteos  Los 5 últimos posteos cargados (id, titulo, estado, publicado_en).
 */
$esAdmin = session('rol') === 'admin';

// Texto y color de la insignia de cada estado de un posteo.
$estados = [
    'publicado' => ['Publicado', 'insignia-verde'],
    'revision'  => ['En revisión', 'insignia-ambar'],
    'borrador'  => ['Borrador', 'insignia-gris'],
];
?>
<div class="encabezado-pagina">
    <div>
        <h1>Hola, <?= esc(explode(' ', (string) session('nombre'))[0]) ?></h1>
        <p class="descripcion">Desde acá cargás y editás lo que se publica en el sitio del instituto.</p>
    </div>
    <div class="acciones-rapidas">
        <a href="<?= base_url('admin/noticias/nuevo') ?>" class="btn btn-primary">
            <span class="material-symbols-outlined" aria-hidden="true">add</span> Nueva noticia
        </a>
        <a href="<?= base_url('admin/posteos/nuevo') ?>" class="btn btn-suave">
            <span class="material-symbols-outlined" aria-hidden="true">edit_note</span> Nuevo posteo
        </a>
        <a href="<?= base_url('admin/media/nuevo') ?>" class="btn btn-suave">
            <span class="material-symbols-outlined" aria-hidden="true">upload</span> Subir archivo
        </a>
    </div>
</div>

<?php // --- Totales: cada tarjeta lleva a su listado --- ?>
<div class="row g-3 mb-4">
    <div class="col-sm-6 <?= $esAdmin ? 'col-xl' : 'col-lg-4' ?>">
        <a href="<?= base_url('admin/noticias') ?>" class="tarjeta-dato">
            <span class="icono" aria-hidden="true"><span class="material-symbols-outlined">newspaper</span></span>
            <span><strong><?= $total_noticias ?></strong><span class="etiqueta">Noticias</span></span>
        </a>
    </div>
    <div class="col-sm-6 <?= $esAdmin ? 'col-xl' : 'col-lg-4' ?>">
        <a href="<?= base_url('admin/posteos') ?>" class="tarjeta-dato">
            <span class="icono" aria-hidden="true"><span class="material-symbols-outlined">edit_note</span></span>
            <span>
                <strong><?= $total_posteos ?></strong><span class="etiqueta">Posteos</span>
                <?php if ($posteos_revision > 0): ?>
                    <span class="nota"><?= $posteos_revision ?> en revisión</span>
                <?php endif ?>
            </span>
        </a>
    </div>
    <div class="col-sm-6 <?= $esAdmin ? 'col-xl' : 'col-lg-4' ?>">
        <a href="<?= base_url('admin/media') ?>" class="tarjeta-dato">
            <span class="icono" aria-hidden="true"><span class="material-symbols-outlined">perm_media</span></span>
            <span><strong><?= $total_media ?></strong><span class="etiqueta">Archivos</span></span>
        </a>
    </div>
    <?php if ($esAdmin): ?>
        <div class="col-sm-6 col-xl">
            <a href="<?= base_url('admin/carreras') ?>" class="tarjeta-dato">
                <span class="icono" aria-hidden="true"><span class="material-symbols-outlined">school</span></span>
                <span><strong><?= $total_carreras ?></strong><span class="etiqueta">Carreras</span></span>
            </a>
        </div>
        <div class="col-sm-6 col-xl">
            <a href="<?= base_url('admin/usuarios') ?>" class="tarjeta-dato">
                <span class="icono" aria-hidden="true"><span class="material-symbols-outlined">group</span></span>
                <span><strong><?= $total_usuarios ?></strong><span class="etiqueta">Usuarios</span></span>
            </a>
        </div>
    <?php endif ?>
</div>

<?php // --- Lo último que se cargó --- ?>
<div class="row g-3">
    <div class="col-lg-6">
        <section class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">newspaper</span> Últimas noticias</h2>
                <a href="<?= base_url('admin/noticias') ?>" class="small">Ver todas</a>
            </div>
            <ul class="lista-reciente">
                <?php foreach ($ultimas_noticias as $noticia): ?>
                    <li>
                        <a href="<?= base_url('admin/noticias/editar/' . $noticia['id']) ?>">
                            <span class="text-truncate">
                                <span class="titulo"><?= esc($noticia['titulo']) ?></span>
                                <span class="meta"><?= etiqueta_categoria($noticia['categoria']) ?><?= $noticia['publicado_en'] ? ' · ' . formatear_fecha($noticia['publicado_en']) : '' ?></span>
                            </span>
                            <span class="material-symbols-outlined text-secondary" aria-hidden="true">chevron_right</span>
                        </a>
                    </li>
                <?php endforeach ?>
                <?php if ($ultimas_noticias === []): ?>
                    <li class="vacia">Todavía no hay noticias. <a href="<?= base_url('admin/noticias/nuevo') ?>">Cargá la primera</a>.</li>
                <?php endif ?>
            </ul>
        </section>
    </div>
    <div class="col-lg-6">
        <section class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">edit_note</span> Posteos recientes</h2>
                <a href="<?= base_url('admin/posteos') ?>" class="small">Ver todos</a>
            </div>
            <ul class="lista-reciente">
                <?php foreach ($ultimos_posteos as $posteo): ?>
                    <?php $estado = $estados[$posteo['estado']] ?? $estados['borrador'] ?>
                    <li>
                        <a href="<?= base_url('admin/posteos/editar/' . $posteo['id']) ?>">
                            <span class="text-truncate">
                                <span class="titulo"><?= esc($posteo['titulo']) ?></span>
                                <span class="meta"><?= $posteo['publicado_en'] ? formatear_fecha($posteo['publicado_en']) : 'Sin fecha' ?></span>
                            </span>
                            <span class="insignia <?= $estado[1] ?>"><?= $estado[0] ?></span>
                        </a>
                    </li>
                <?php endforeach ?>
                <?php if ($ultimos_posteos === []): ?>
                    <li class="vacia">Todavía no hay posteos. <a href="<?= base_url('admin/posteos/nuevo') ?>">Escribí el primero</a>.</li>
                <?php endif ?>
            </ul>
        </section>
    </div>
</div>
