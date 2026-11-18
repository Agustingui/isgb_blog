<?php
/**
 * Resultados del buscador: carreras, noticias y posteos.
 *
 * @var string $q        Texto buscado.
 * @var array  $carreras Carreras encontradas.
 * @var array  $noticias Noticias encontradas.
 * @var array  $posteos  Posteos encontrados.
 */
?>
<div class="container py-5">
    <a href="<?= base_url() ?>" class="volver"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span> Volver al inicio</a>

    <h1 class="h2 mt-4">Resultados de la búsqueda</h1>

    <form action="<?= base_url('buscar') ?>" method="get" class="d-flex gap-2 my-3 buscador-pagina" role="search">
        <label class="visually-hidden" for="buscar-q">Buscar</label>
        <input type="search" name="q" id="buscar-q" class="form-control" value="<?= esc($q) ?>" placeholder="Buscar carreras, noticias y posteos" minlength="2" required>
        <button type="submit" class="btn btn-primary" aria-label="Buscar"><span class="material-symbols-outlined" aria-hidden="true">search</span></button>
    </form>

    <?php if (mb_strlen($q) < 2): ?>
        <p class="texto-suave">Escribí al menos 2 letras para buscar.</p>
    <?php elseif ($carreras === [] && $noticias === [] && $posteos === []): ?>
        <p class="texto-suave">No encontramos nada para “<?= esc($q) ?>”. Probá con otra palabra.</p>
    <?php endif ?>

    <?php if ($carreras !== []): ?>
        <section aria-labelledby="resultados-carreras">
            <h2 class="h5 mt-4" id="resultados-carreras">Carreras (<?= count($carreras) ?>)</h2>
            <ul class="list-unstyled resultados">
                <?php foreach ($carreras as $carrera): ?>
                    <li>
                        <a href="<?= base_url('carreras/' . $carrera['slug']) ?>" class="fw-semibold"><?= esc($carrera['nombre']) ?></a>
                        <p class="small texto-suave mb-0"><?= esc(resumen($carrera['descripcion'])) ?></p>
                    </li>
                <?php endforeach ?>
            </ul>
        </section>
    <?php endif ?>

    <?php if ($noticias !== []): ?>
        <section aria-labelledby="resultados-noticias">
            <h2 class="h5 mt-4" id="resultados-noticias">Noticias (<?= count($noticias) ?>)</h2>
            <ul class="list-unstyled resultados">
                <?php foreach ($noticias as $noticia): ?>
                    <li>
                        <a href="<?= base_url('noticias/' . $noticia['slug']) ?>" class="fw-semibold"><?= esc($noticia['titulo']) ?></a>
                        <span class="meta"> · <time datetime="<?= esc($noticia['publicado_en']) ?>"><?= formatear_fecha($noticia['publicado_en']) ?></time></span>
                        <p class="small texto-suave mb-0"><?= esc(resumen($noticia['bajada'])) ?></p>
                    </li>
                <?php endforeach ?>
            </ul>
        </section>
    <?php endif ?>

    <?php if ($posteos !== []): ?>
        <section aria-labelledby="resultados-posteos">
            <h2 class="h5 mt-4" id="resultados-posteos">Posteos del blog (<?= count($posteos) ?>)</h2>
            <ul class="list-unstyled resultados">
                <?php foreach ($posteos as $posteo): ?>
                    <li>
                        <a href="<?= base_url('blog/' . $posteo['slug']) ?>" class="fw-semibold"><?= esc($posteo['titulo']) ?></a>
                        <span class="meta"> · <time datetime="<?= esc($posteo['publicado_en']) ?>"><?= formatear_fecha($posteo['publicado_en']) ?></time></span>
                        <p class="small texto-suave mb-0"><?= esc(resumen($posteo['extracto'])) ?></p>
                    </li>
                <?php endforeach ?>
            </ul>
        </section>
    <?php endif ?>
</div>
