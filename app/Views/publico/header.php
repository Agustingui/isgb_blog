<?php
/**
 * Layout público: <head>, barra de navegación y mensajes flash. Abre <main>.
 *
 * @var string $titulo Título de la página.
 * @var string|null $descripcion Meta descripción (opcional).
 * @var string|null $og_tipo Tipo Open Graph: 'website' o 'article' (opcional).
 * @var string|null $og_imagen URL de la imagen para redes (opcional).
 */
$descripcion = resumen($descripcion ?? 'Tecnicaturas con título oficial en el Instituto Superior Gaspar Benavento.', 200);
$og_tipo     = $og_tipo ?? 'website';
$og_imagen   = $og_imagen ?? null;
$rol         = session('rol');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($titulo) ?> | Instituto Superior Gaspar Benavento</title>
    <meta name="description" content="<?= esc($descripcion) ?>">
    <?= csrf_meta() ?>
    <link rel="canonical" href="<?= esc(current_url()) ?>">
    <meta property="og:site_name" content="Instituto Superior Gaspar Benavento">
    <meta property="og:type" content="<?= esc($og_tipo) ?>">
    <meta property="og:title" content="<?= esc($titulo) ?>">
    <meta property="og:description" content="<?= esc($descripcion) ?>">
    <meta property="og:url" content="<?= esc(current_url()) ?>">
    <meta property="og:locale" content="es_AR">
    <?php if ($og_imagen): ?>
        <meta property="og:image" content="<?= esc($og_imagen) ?>">
        <meta name="twitter:card" content="summary_large_image">
    <?php else: ?>
        <meta property="og:image" content="<?= base_url('img/icono-192.png') ?>">
        <meta name="twitter:card" content="summary">
    <?php endif ?>
    <link rel="alternate" type="application/rss+xml" title="Instituto Superior Gaspar Benavento" href="<?= base_url('rss.xml') ?>">
    <link rel="icon" type="image/png" href="<?= recurso('img/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= recurso('img/apple-touch-icon.png') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="<?= recurso('assets/css/publico.css') ?>">
</head>
<body>

<?php // --- Barra de navegación --- ?>
<header class="sticky-top">
    <nav class="navbar navbar-expand-lg barra-isgb" aria-label="Navegación principal">
        <div class="container">
            <a class="navbar-brand marca" href="<?= base_url() ?>">
                <img src="<?= recurso('img/logo.png') ?>" alt="" class="marca-logo" width="44" height="44">
                <span class="marca-texto">
                    <small>Instituto Superior</small>
                    <strong>Gaspar Benavento</strong>
                </span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menu-principal" aria-controls="menu-principal" aria-expanded="false" aria-label="Abrir menú">
                <span class="material-symbols-outlined" aria-hidden="true">menu</span>
            </button>

            <div class="collapse navbar-collapse" id="menu-principal">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('carreras') ?>">Carreras</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('noticias') ?>">Noticias</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('blog') ?>">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
                </ul>

                <form action="<?= base_url('buscar') ?>" method="get" class="d-flex me-lg-3 my-2 my-lg-0" role="search">
                    <input type="search" name="q" class="form-control form-control-sm" placeholder="Buscar..." aria-label="Buscar" minlength="2">
                    <button type="submit" class="btn btn-sm btn-isgb-borde ms-1" aria-label="Buscar"><span class="material-symbols-outlined" aria-hidden="true">search</span></button>
                </form>

                <div class="d-flex gap-2 py-2 py-lg-0">
                    <?php if ($rol): ?>
                        <?php if (in_array($rol, ['admin', 'editor'], true)): ?>
                            <a href="<?= base_url('admin') ?>" class="btn btn-sm btn-isgb-borde">
                                <span class="material-symbols-outlined" aria-hidden="true">dashboard</span> Panel
                            </a>
                        <?php endif ?>
                        <a href="<?= base_url('cuenta') ?>" class="btn btn-sm btn-isgb-borde">
                            <span class="material-symbols-outlined" aria-hidden="true">person</span> Mi cuenta
                        </a>
                        <form action="<?= base_url('salir') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-isgb-borde">
                                <span class="material-symbols-outlined" aria-hidden="true">logout</span> Salir
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn btn-sm btn-isgb-borde">
                            <span class="material-symbols-outlined" aria-hidden="true">login</span> Ingresar
                        </a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<?php // --- Mensajes flash --- ?>
<?php foreach (['error' => 'alert-danger', 'mensaje' => 'alert-success'] as $clave => $clase): ?>
    <?php if (session()->getFlashdata($clave)): ?>
        <div class="container mt-3">
            <div class="alert <?= $clase ?> mb-0" role="alert"><?= esc(session()->getFlashdata($clave)) ?></div>
        </div>
    <?php endif ?>
<?php endforeach ?>

<main id="contenido">
