<?php
/**
 * Formulario de alta y edición de posteos con el editor Quill.
 *
 * A la izquierda va el texto; a la derecha, el estado, los datos de
 * publicación y la imagen (mismo esquema que el formulario de noticias).
 *
 * @var array|null $posteo Posteo a editar, o null para uno nuevo.
 * @var string $titulo Título de la página.
 * @var array<string,string> $carreras Id => nombre.
 * @var array<string,string> $autores Id => nombre.
 * @var list<array<string,string>> $imagenes Imágenes de la biblioteca (id, alt, archivo).
 */
$editando = $posteo !== null;
?>
<?= view('admin/partes/encabezado', ['titulo' => $titulo, 'volver' => 'admin/posteos', 'textoVolver' => 'Posteos del blog']) ?>

<form action="<?= base_url('admin/posteos/' . ($editando ? 'actualizar' : 'insertar')) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= (int) $posteo['id'] ?>">
    <?php endif ?>

    <div class="row g-4">
<?php // --- Columna principal: título, dirección, extracto y contenido --- ?>
        <div class="col-lg-8">
            <section class="card">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label" for="titulo">Título</label>
                        <input type="text" name="titulo" id="titulo" class="form-control form-control-lg" value="<?= old('titulo', esc($posteo['titulo'] ?? '')) ?>" maxlength="250" required data-origen-slug="#slug">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug">Dirección web</label>
                        <div class="input-group">
                            <span class="input-group-text prefijo-url" title="<?= base_url('blog/') ?>">/blog/</span>
                            <input type="text" name="slug" id="slug" class="form-control" value="<?= old('slug', esc($posteo['slug'] ?? '')) ?>" maxlength="250" pattern="[A-Za-z0-9_\-]+" aria-describedby="ayuda-slug" required>
                        </div>
                        <div class="form-text" id="ayuda-slug">Se completa sola con el título. Solo letras, números y guiones.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="extracto">Extracto</label>
                        <input type="text" name="extracto" id="extracto" class="form-control" value="<?= old('extracto', esc($posteo['extracto'] ?? '')) ?>" maxlength="255" aria-describedby="ayuda-extracto" required>
                        <div class="form-text" id="ayuda-extracto">Una línea para la tarjeta del blog.</div>
                    </div>

<?php // --- Editor Quill: el HTML se copia al campo oculto "contenido" (ver admin.js) --- ?>
                    <div>
                        <p class="form-label mb-2" id="editor-etiqueta">Contenido</p>
                        <div id="editor" data-etiqueta="editor-etiqueta" data-subir-imagen="<?= base_url('admin/media/imagen-editor') ?>" data-placeholder="Escribí el contenido del posteo…"></div>
                        <input type="hidden" name="contenido" id="contenido-hidden" value="<?= old('contenido', esc(contenido_a_html($posteo['contenido'] ?? ''))) ?>">
                        <div id="contenido-error" class="invalid-feedback">El contenido es obligatorio.</div>
                    </div>
                </div>
            </section>
        </div>

<?php // --- Columna lateral: estado, publicación e imagen --- ?>
        <div class="col-lg-4">
            <div class="columna-lateral d-flex flex-column gap-4">
                <section class="card">
                    <div class="card-header"><h2 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">event</span> Publicación</h2></div>
                    <div class="card-body row g-3">
                        <div class="col-12">
                            <?= view('admin/partes/select', ['nombre' => 'estado', 'etiqueta' => 'Estado', 'opciones' => ['borrador' => 'Borrador', 'revision' => 'En revisión', 'publicado' => 'Publicado'], 'actual' => old('estado', $posteo['estado'] ?? 'borrador'), 'columna' => '']) ?>
                            <div class="form-text">Solo los posteos "Publicados" se ven en el sitio.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="publicado_en">Fecha de publicación <span class="opcional">(opcional)</span></label>
                            <input type="date" name="publicado_en" id="publicado_en" class="form-control" value="<?= old('publicado_en', esc($posteo['publicado_en'] ?? '')) ?>">
                        </div>

                        <?= view('admin/partes/select', ['nombre' => 'carrera_id', 'etiqueta' => 'Carrera relacionada', 'opciones' => $carreras, 'actual' => old('carrera_id', $posteo['carrera_id'] ?? ''), 'vacio' => 'Ninguna', 'columna' => 'col-12']) ?>
                        <?= view('admin/partes/select', ['nombre' => 'autor_id', 'etiqueta' => 'Autor', 'opciones' => $autores, 'actual' => old('autor_id', $posteo['autor_id'] ?? session('id_usuario')), 'columna' => 'col-12']) ?>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header"><h2 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">image</span> Imagen de portada</h2></div>
                    <div class="card-body">
                        <?= view('admin/partes/imagen_portada', ['imagenes' => $imagenes, 'actual' => old('imagen_id', $posteo['imagen_id'] ?? ''), 'compacto' => true]) ?>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?= view('admin/partes/botones_formulario', ['cancelar' => 'admin/posteos', 'textoGuardar' => $editando ? 'Guardar cambios' : 'Guardar posteo']) ?>
</form>
