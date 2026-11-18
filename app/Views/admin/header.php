<?php
/**
 * Layout del panel: <head>, menú lateral, barra superior y mensajes. Abre <main>.
 *
 * En pantallas chicas el menú es un offcanvas de Bootstrap que se abre con el
 * botón ☰ de la barra superior; desde "md" en adelante se ve fijo a la izquierda.
 *
 * @var string $titulo Título de la página.
 * @var string $menu Clave del ítem activo del menú.
 * @var string|null $editor 'quill' para cargar los estilos del editor.
 */
$menu    = $menu ?? '';
$editor  = $editor ?? null;
$esAdmin = session('rol') === 'admin';

// Ítems del menú: clave => [ruta, ícono, texto]. Los de "Institución" solo los ve un administrador.
$contenido = [
    'noticias' => ['admin/noticias', 'newspaper', 'Noticias'],
    'posteos'  => ['admin/posteos', 'edit_note', 'Posteos'],
    'media'    => ['admin/media', 'perm_media', 'Archivos'],
];
$institucion = [
    'carreras'      => ['admin/carreras', 'school', 'Carreras'],
    'usuarios'      => ['admin/usuarios', 'group', 'Usuarios'],
    'configuracion' => ['admin/configuracion', 'tune', 'Datos del instituto'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($titulo) ?> | Administración ISGB</title>
    <?= csrf_meta() ?>
    <link rel="icon" href="<?= recurso('favicon.ico') ?>" sizes="any">
    <link rel="icon" type="image/png" href="<?= recurso('img/icono-192.png') ?>">
    <link rel="apple-touch-icon" href="<?= recurso('img/apple-touch-icon.png') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
    <?php if ($editor === 'quill'): ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
    <?php endif ?>
    <link rel="stylesheet" href="<?= recurso('assets/css/admin.css') ?>">
</head>
<body>
<a href="#contenido" class="visually-hidden-focusable position-absolute top-0 start-0 m-2 btn btn-primary" style="z-index: 2000;">Saltar al contenido</a>

<?php // --- Menú lateral --- ?>
<div class="admin-contenedor">
    <aside class="admin-menu">
        <div class="offcanvas-md offcanvas-start" tabindex="-1" id="menu-admin" aria-labelledby="menu-admin-titulo">
            <div class="offcanvas-header">
                <a href="<?= base_url('admin') ?>" class="admin-marca" id="menu-admin-titulo">
                    <img src="<?= recurso('img/logo.png') ?>" alt="" class="admin-logo" width="40" height="40">
                    <span>ISGB <small>Administración</small></span>
                </a>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#menu-admin" aria-label="Cerrar menú"></button>
            </div>

            <div class="offcanvas-body">
                <a href="<?= base_url('admin') ?>" class="admin-marca">
                    <img src="<?= recurso('img/logo.png') ?>" alt="" class="admin-logo" width="40" height="40">
                    <span>ISGB <small>Administración</small></span>
                </a>

                <nav aria-label="Menú del panel">
                    <a class="nav-link<?= $menu === 'panel' ? ' activo' : '' ?>" href="<?= base_url('admin') ?>"<?= $menu === 'panel' ? ' aria-current="page"' : '' ?>>
                        <span class="material-symbols-outlined" aria-hidden="true">space_dashboard</span> Inicio
                    </a>

                    <p class="menu-grupo">Contenido</p>
                    <?php foreach ($contenido as $clave => $item): ?>
                        <a class="nav-link<?= $menu === $clave ? ' activo' : '' ?>" href="<?= base_url($item[0]) ?>"<?= $menu === $clave ? ' aria-current="page"' : '' ?>>
                            <span class="material-symbols-outlined" aria-hidden="true"><?= $item[1] ?></span> <?= $item[2] ?>
                        </a>
                    <?php endforeach ?>

                    <?php if ($esAdmin): ?>
                        <p class="menu-grupo">Institución</p>
                        <?php foreach ($institucion as $clave => $item): ?>
                            <a class="nav-link<?= $menu === $clave ? ' activo' : '' ?>" href="<?= base_url($item[0]) ?>"<?= $menu === $clave ? ' aria-current="page"' : '' ?>>
                                <span class="material-symbols-outlined" aria-hidden="true"><?= $item[1] ?></span> <?= $item[2] ?>
                            </a>
                        <?php endforeach ?>
                    <?php endif ?>
                </nav>

                <div class="menu-pie">
                    <a class="nav-link" href="<?= base_url() ?>" target="_blank" rel="noopener">
                        <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span> Ver el sitio
                        <span class="visually-hidden">(se abre en otra pestaña)</span>
                    </a>
                </div>
            </div>
        </div>
    </aside>

<?php // --- Barra superior --- ?>
    <div class="admin-principal">
        <header class="admin-barra">
            <div class="d-flex align-items-center gap-2 min-w-0" style="min-width: 0;">
                <button type="button" class="btn btn-icono d-md-none" data-bs-toggle="offcanvas" data-bs-target="#menu-admin" aria-controls="menu-admin" aria-label="Abrir menú">
                    <span class="material-symbols-outlined" aria-hidden="true">menu</span>
                </button>
                <span class="seccion-actual"><?= esc($titulo) ?></span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="usuario-actual">
                    <span class="avatar" aria-hidden="true"><?= esc(mb_substr((string) session('nombre'), 0, 1)) ?></span>
                    <span class="d-none d-sm-block">
                        <span class="nombre d-block"><?= esc(session('nombre')) ?></span>
                        <span class="rol d-block"><?= esc(session('rol')) ?></span>
                    </span>
                </div>
                <form action="<?= base_url('salir') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-suave" title="Cerrar sesión">
                        <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                        <span class="d-none d-sm-inline">Salir</span>
                        <span class="visually-hidden d-sm-none">Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </header>

<?php // --- Contenido y mensajes --- ?>
        <main class="admin-contenido" id="contenido" tabindex="-1">
            <div id="avisos" aria-live="polite">
                <?php if (session()->getFlashdata('mensaje')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="status">
                        <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>
                        <div><?= esc(session()->getFlashdata('mensaje')) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span class="material-symbols-outlined" aria-hidden="true">error</span>
                        <div><?= esc(session()->getFlashdata('error')) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif ?>

                <?php if (session()->getFlashdata('errores')): ?>
                    <div class="alert alert-danger" role="alert">
                        <span class="material-symbols-outlined" aria-hidden="true">error</span>
                        <div>
                            <strong class="d-block mb-1">Revisá estos datos antes de guardar:</strong>
                            <ul class="mb-0 ps-3">
                                <?php foreach (session()->getFlashdata('errores') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                <?php endif ?>
            </div>
