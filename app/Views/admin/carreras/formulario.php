<?php
/**
 * Formulario de alta y edición de carreras con su plan de estudios.
 *
 * Dividido en tarjetas (datos, imagen y documentos, plan de estudios) para
 * que un formulario tan largo se pueda recorrer de un vistazo.
 *
 * @var array|null $carrera Carrera a editar, o null para una nueva.
 * @var array<int,list<string>> $planes Año => materias (en el textarea, una por línea).
 * @var string $titulo Título de la página.
 * @var list<array<string,string>> $imagenes Imágenes de la biblioteca (id, alt, archivo).
 * @var list<array<string,string>> $pdfs PDF de la biblioteca (id, alt, archivo).
 */
$editando = $carrera !== null;

// Si el formulario volvió con errores, se respeta lo que se había marcado.
if (session()->has('_ci_old_input')) {
    $activa = old('activa') === '1';
} else {
    $activa = ! $editando || $carrera['activa'] == 1;
}
?>
<?= view('admin/partes/encabezado', ['titulo' => $titulo, 'volver' => 'admin/carreras', 'textoVolver' => 'Carreras']) ?>

<form action="<?= base_url('admin/carreras/' . ($editando ? 'actualizar' : 'insertar')) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= (int) $carrera['id'] ?>">
    <?php endif ?>

    <div class="d-flex flex-column gap-4">
<?php // --- Datos de la carrera --- ?>
        <section class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h2 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">school</span> Datos de la carrera</h2>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" name="activa" id="activa" value="1"<?= $activa ? ' checked' : '' ?>>
                    <label class="form-check-label small" for="activa">Activa (se muestra en el sitio)</label>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="nombre">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="<?= old('nombre', esc($carrera['nombre'] ?? '')) ?>" maxlength="200" required data-origen-slug="#slug">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="duracion">Duración</label>
                        <input type="text" name="duracion" id="duracion" class="form-control" value="<?= old('duracion', esc($carrera['duracion'] ?? '')) ?>" maxlength="50" placeholder="Ej: 3 años" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="slug">Dirección web</label>
                        <div class="input-group">
                            <span class="input-group-text prefijo-url" title="<?= base_url('carreras/') ?>">/carreras/</span>
                            <input type="text" name="slug" id="slug" class="form-control" value="<?= old('slug', esc($carrera['slug'] ?? '')) ?>" maxlength="200" pattern="[A-Za-z0-9_\-]+" aria-describedby="ayuda-slug" required>
                        </div>
                        <div class="form-text" id="ayuda-slug">Se completa sola con el nombre. Solo letras, números y guiones. Ej: desarrollo-de-software</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="descripcion">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="4" class="form-control" required><?= old('descripcion', esc($carrera['descripcion'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>
        </section>

<?php // --- Imagen y documentos PDF --- ?>
        <section class="card">
            <div class="card-header">
                <h2 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">attach_file</span> Imagen y documentos <span class="opcional fw-normal text-secondary small">(opcional)</span></h2>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <?= view('admin/partes/imagen_portada', ['imagenes' => $imagenes, 'actual' => old('imagen_id', $carrera['imagen_id'] ?? ''), 'leyenda' => 'Imagen de la carrera']) ?>

                    <?= view('admin/partes/elegir_pdf', ['campo' => 'plan_pdf_id', 'archivo' => 'plan_pdf_nuevo', 'leyenda' => 'PDF del plan de estudios', 'pdfs' => $pdfs, 'actual' => old('plan_pdf_id', $carrera['plan_pdf_id'] ?? '')]) ?>
                    <?= view('admin/partes/elegir_pdf', ['campo' => 'resolucion_pdf_id', 'archivo' => 'resolucion_pdf_nuevo', 'leyenda' => 'PDF de la resolución', 'pdfs' => $pdfs, 'actual' => old('resolucion_pdf_id', $carrera['resolucion_pdf_id'] ?? '')]) ?>
                </div>
            </div>
        </section>

<?php
// --- Plan de estudios: un textarea por año ---
// admin.js (iniciarPlanEstudios) cambia cada textarea por una lista de materias que se
// ordenan arrastrando, y copia la lista al textarea, que es lo que se envía (una materia por línea).
?>
        <section class="card">
            <div class="card-header">
                <h2 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">list_alt</span> Plan de estudios</h2>
            </div>
            <fieldset class="card-body p-4">
                <legend class="visually-hidden">Materias de cada año</legend>
                <p class="form-text mt-0 mb-3" data-ayuda-plan>Una materia por línea. Los años que dejes vacíos no se muestran.</p>
                <div class="row g-3">
                    <?php for ($anio = 1; $anio <= 6; $anio++): ?>
                        <div class="col-md-6 col-xl-4" data-plan-anio>
                            <label class="form-label" for="anio<?= $anio ?>"><?= $anio ?>.º año</label>
                            <textarea name="anio<?= $anio ?>" id="anio<?= $anio ?>" rows="5" class="form-control"><?= old('anio' . $anio, esc(implode("\n", $planes[$anio] ?? []))) ?></textarea>
                        </div>
                    <?php endfor ?>
                </div>
            </fieldset>
        </section>
    </div>

    <?= view('admin/partes/botones_formulario', ['cancelar' => 'admin/carreras', 'textoGuardar' => $editando ? 'Guardar cambios' : 'Guardar carrera']) ?>
</form>
