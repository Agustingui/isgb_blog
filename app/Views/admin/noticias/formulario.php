<?php
/**
 * Formulario de alta y edición de noticias con el editor Quill.
 *
 * A la izquierda va el texto (lo que más se edita); a la derecha, los datos
 * de publicación y la imagen, como en la mayoría de los gestores de contenido.
 *
 * @var array|null $noticia Noticia a editar, o null para una nueva.
 * @var string $titulo Título de la página.
 * @var array<string,string> $autores Id => nombre.
 * @var list<array<string,string>> $imagenes Imágenes de la biblioteca (id, alt, archivo).
 */
$editando = $noticia !== null;
?>
<?= view('admin/partes/encabezado', ['titulo' => $titulo, 'volver' => 'admin/noticias', 'textoVolver' => 'Noticias']) ?>

<form action="<?= base_url('admin/noticias/' . ($editando ? 'actualizar' : 'insertar')) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= (int) $noticia['id'] ?>">
    <?php endif ?>

    <div class="row g-4">
<?php // --- Columna principal: título, dirección, bajada y contenido --- ?>
        <div class="col-lg-8">
            <section class="card">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label" for="titulo">Título</label>
                        <input type="text" name="titulo" id="titulo" class="form-control form-control-lg" value="<?= old('titulo', esc($noticia['titulo'] ?? '')) ?>" maxlength="250" required data-origen-slug="#slug">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug">Dirección web</label>
                        <div class="input-group">
                            <span class="input-group-text prefijo-url" title="<?= base_url('noticias/') ?>">/noticias/</span>
                            <input type="text" name="slug" id="slug" class="form-control" value="<?= old('slug', esc($noticia['slug'] ?? '')) ?>" maxlength="250" pattern="[A-Za-z0-9_\-]+" aria-describedby="ayuda-slug" required>
                        </div>
                        <div class="form-text" id="ayuda-slug">Se completa sola con el título. Solo letras, números y guiones.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="bajada">Bajada</label>
                        <input type="text" name="bajada" id="bajada" class="form-control" value="<?= old('bajada', esc($noticia['bajada'] ?? '')) ?>" maxlength="255" aria-describedby="ayuda-bajada" required>
                        <div class="form-text" id="ayuda-bajada">Una línea que resume la noticia. Se muestra en el listado y al compartirla.</div>
                    </div>

<?php // --- Editor Quill: el HTML se copia al campo oculto "contenido" (ver admin.js) --- ?>
                    <div>
                        <p class="form-label mb-2" id="editor-etiqueta">Contenido</p>
                        <div id="editor" data-etiqueta="editor-etiqueta" data-subir-imagen="<?= base_url('admin/media/imagen-editor') ?>" data-placeholder="Escribí el contenido de la noticia…"></div>
                        <input type="hidden" name="contenido" id="contenido-hidden" value="<?= old('contenido', esc(contenido_a_html($noticia['contenido'] ?? ''))) ?>">
                        <div id="contenido-error" class="invalid-feedback">El contenido es obligatorio.</div>
                    </div>
                </div>
            </section>
        </div>

<?php // --- Columna lateral: publicación e imagen --- ?>
        <div class="col-lg-4">
            <div class="columna-lateral d-flex flex-column gap-4">
                <section class="card">
                    <div class="card-header"><h2 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">event</span> Publicación</h2></div>
                    <div class="card-body row g-3">
                        <?= view('admin/partes/select', ['nombre' => 'categoria', 'etiqueta' => 'Categoría', 'opciones' => ['institucionales' => 'Institucionales', 'academicas' => 'Académicas', 'ultimo_momento' => 'Último momento'], 'actual' => old('categoria', $noticia['categoria'] ?? 'institucionales'), 'columna' => 'col-12']) ?>

                        <div class="col-12">
                            <label class="form-label" for="publicado_en">Fecha de publicación <span class="opcional">(opcional)</span></label>
                            <input type="date" name="publicado_en" id="publicado_en" class="form-control" value="<?= old('publicado_en', esc($noticia['publicado_en'] ?? '')) ?>">
                        </div>

                        <?= view('admin/partes/select', ['nombre' => 'autor_id', 'etiqueta' => 'Autor', 'opciones' => $autores, 'actual' => old('autor_id', $noticia['autor_id'] ?? session('id_usuario')), 'columna' => 'col-12']) ?>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header"><h2 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">image</span> Imagen de portada</h2></div>
                    <div class="card-body">
                        <?= view('admin/partes/imagen_portada', ['imagenes' => $imagenes, 'actual' => old('imagen_id', $noticia['imagen_id'] ?? ''), 'compacto' => true]) ?>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?= view('admin/partes/botones_formulario', ['cancelar' => 'admin/noticias', 'textoGuardar' => $editando ? 'Guardar cambios' : 'Publicar noticia']) ?>
</form>
