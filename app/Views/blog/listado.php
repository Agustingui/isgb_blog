<?php
/**
 * Listado del blog con búsqueda, archivo por mes y paginación.
 *
 * @var array $posteos Posteos de la página actual.
 * @var array<string,int> $meses AAAA-MM => cantidad de posteos.
 * @var string|null $mes Mes filtrado.
 * @var string $q Texto buscado.
 * @var int $total Total de posteos encontrados.
 * @var int $pagina Página actual.
 * @var int $total_paginas Total de páginas.
 */
?>
<div class="container py-5">
    <a href="<?= base_url() ?>" class="volver"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span> Volver al inicio</a>

    <p class="etiqueta-seccion mt-4 mb-2">Blog</p>
    <h1 class="h2">Todos los posteos</h1>

    <div class="row g-4 mt-1">
        <aside class="col-lg-3" aria-label="Filtros del blog">
            <form action="<?= base_url('blog') ?>" method="get" class="mb-4" role="search">
                <label class="form-label small fw-semibold" for="q">Buscar en el blog</label>
                <div class="input-group">
                    <input type="search" name="q" id="q" class="form-control" value="<?= esc($q) ?>" placeholder="Palabra clave">
                    <button type="submit" class="btn btn-primary" aria-label="Buscar"><span class="material-symbols-outlined" aria-hidden="true">search</span></button>
                </div>
            </form>

            <?php if ($meses !== []): ?>
                <nav aria-labelledby="archivo-titulo">
                    <h2 class="h6" id="archivo-titulo">Archivo</h2>
                    <ul class="list-unstyled small">
                        <?php foreach ($meses as $clave => $cantidad): ?>
                            <li class="mb-1">
                                <a href="<?= base_url('blog?mes=' . $clave) ?>"<?= $mes === $clave ? ' class="fw-bold" aria-current="page"' : '' ?>>
                                    <?= esc(mes_en_letras($clave)) ?>
                                </a>
                                <span class="texto-suave">(<?= (int) $cantidad ?>)</span>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </nav>
            <?php endif ?>
        </aside>

        <section class="col-lg-9" aria-label="Resultados">
            <p class="meta">
                <?= (int) $total ?> posteo(s)
                <?php if ($q !== ''): ?> para “<?= esc($q) ?>”<?php endif ?>
                <?php if ($mes): ?> de <strong><?= esc(mes_en_letras($mes)) ?></strong><?php endif ?>
            </p>

            <?php if ($posteos === []): ?>
                <p class="texto-suave">No encontramos posteos con esos filtros.</p>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($posteos as $posteo): ?>
                        <div class="col-md-6 col-xl-4">
                            <?= view('publico/partes/posteo_card', ['posteo' => $posteo, 'nivel_titulo' => 2]) ?>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <?= view('publico/partes/paginacion', ['pagina' => $pagina, 'total_paginas' => $total_paginas]) ?>
        </section>
    </div>
</div>
