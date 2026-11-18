<?php
/**
 * Formulario de los datos del instituto, dividido en dos zonas según dónde se ven en el sitio:
 * la portada de la página de inicio y el pie de página (que está en todas las páginas).
 *
 * @var array $configuracion Fila de la tabla configuracion.
 * @var list<array<string,string>> $imagenes Imágenes de la biblioteca (id, alt, archivo).
 */

// Textos originales de la portada: se muestran como ejemplo en los campos vacíos y en la vista previa.
$original = portada_textos([])['originales'];
$boton2Visible = session()->has('_ci_old_input') ? old('portada_boton2_visible') === '1' : (int) ($configuracion['portada_boton2_visible'] ?? 1) === 1;
?>
<?= view('admin/partes/encabezado', [
    'titulo'      => 'Datos del instituto',
    'descripcion' => 'Lo que se muestra en la portada de la página de inicio y en el pie de todas las páginas.',
]) ?>

<?php // --- Índice: lleva a cada zona del formulario --- ?>
<nav class="indice-zonas mb-4" aria-label="Secciones del formulario">
    <a href="#zona-portada">
        <span class="material-symbols-outlined" aria-hidden="true">wallpaper</span>
        <span><strong>Portada del sitio</strong><small>Lo primero que se ve en la página de inicio</small></span>
    </a>
    <a href="#zona-pie">
        <span class="material-symbols-outlined" aria-hidden="true">call_to_action</span>
        <span><strong>Pie de página</strong><small>Contacto y redes, al final de todas las páginas</small></span>
    </a>
</nav>

<form action="<?= base_url('admin/configuracion/actualizar') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

<?php // ======================= ZONA 1: PORTADA DEL SITIO ======================= ?>
    <section class="zona" id="zona-portada" aria-labelledby="zona-portada-titulo" data-portada>
        <header class="zona-encabezado">
            <span class="zona-numero" aria-hidden="true">1</span>
            <div class="flex-grow-1">
                <h2 id="zona-portada-titulo">Portada del sitio</h2>
                <p>Se ve arriba de todo en la página de inicio. Un campo vacío muestra el texto original (el que aparece en gris), así que si no querés cambiar nada, dejalo como está.</p>
            </div>
            <button type="button" class="btn btn-sm btn-suave" data-restaurar-portada>
                <span class="material-symbols-outlined" aria-hidden="true">restart_alt</span> Restaurar textos originales
            </button>
        </header>

        <div class="row g-4">
<?php // --- Columna izquierda: textos, botones e insignia de inscripción --- ?>
            <div class="col-xl-6 d-flex flex-column gap-4">
                <section class="card">
                    <div class="card-header"><h3 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">title</span> Textos y botones</h3></div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="portada_etiqueta">Texto chico de arriba</label>
                                <input type="text" name="portada_etiqueta" id="portada_etiqueta" class="form-control" maxlength="100" placeholder="<?= esc($original['portada_etiqueta']) ?>" value="<?= old('portada_etiqueta', esc($configuracion['portada_etiqueta'] ?? '')) ?>" data-vista="etiqueta">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="portada_titulo">Título</label>
                                <textarea name="portada_titulo" id="portada_titulo" class="form-control" rows="2" maxlength="200" placeholder="<?= esc($original['portada_titulo']) ?>" data-vista="titulo"><?= old('portada_titulo', esc($configuracion['portada_titulo'] ?? '')) ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="portada_texto">Texto</label>
                                <textarea name="portada_texto" id="portada_texto" class="form-control" rows="3" maxlength="400" placeholder="<?= esc($original['portada_texto']) ?>" data-vista="texto"><?= old('portada_texto', esc($configuracion['portada_texto'] ?? '')) ?></textarea>
                            </div>

                            <fieldset class="col-12">
                                <legend class="form-label fs-6 mb-2">Botón principal</legend>
                                <div class="row g-2">
                                    <div class="col-sm-5">
                                        <label class="form-label small text-secondary" for="portada_boton1_texto">Texto</label>
                                        <input type="text" name="portada_boton1_texto" id="portada_boton1_texto" class="form-control" maxlength="60" placeholder="<?= esc($original['portada_boton1_texto']) ?>" value="<?= old('portada_boton1_texto', esc($configuracion['portada_boton1_texto'] ?? '')) ?>" data-vista="boton1">
                                    </div>
                                    <div class="col-sm-7">
                                        <label class="form-label small text-secondary" for="portada_boton1_enlace">Lleva a</label>
                                        <input type="text" name="portada_boton1_enlace" id="portada_boton1_enlace" class="form-control" maxlength="255" placeholder="<?= esc($original['portada_boton1_enlace']) ?>" value="<?= old('portada_boton1_enlace', esc($configuracion['portada_boton1_enlace'] ?? '')) ?>" aria-describedby="ayuda-enlaces">
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="col-12">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <legend class="form-label fs-6 m-0 w-auto float-none">Segundo botón</legend>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="portada_boton2_visible" id="portada_boton2_visible" value="1"<?= $boton2Visible ? ' checked' : '' ?>>
                                        <label class="form-check-label small" for="portada_boton2_visible">Mostrar</label>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-sm-5">
                                        <label class="form-label small text-secondary" for="portada_boton2_texto">Texto</label>
                                        <input type="text" name="portada_boton2_texto" id="portada_boton2_texto" class="form-control" maxlength="60" placeholder="<?= esc($original['portada_boton2_texto']) ?>" value="<?= old('portada_boton2_texto', esc($configuracion['portada_boton2_texto'] ?? '')) ?>" data-vista="boton2">
                                    </div>
                                    <div class="col-sm-7">
                                        <label class="form-label small text-secondary" for="portada_boton2_enlace">Lleva a</label>
                                        <input type="text" name="portada_boton2_enlace" id="portada_boton2_enlace" class="form-control" maxlength="255" placeholder="<?= esc($original['portada_boton2_enlace']) ?>" value="<?= old('portada_boton2_enlace', esc($configuracion['portada_boton2_enlace'] ?? '')) ?>" aria-describedby="ayuda-enlaces">
                                    </div>
                                </div>
                                <div class="form-text" id="ayuda-enlaces">
                                    Puede ser una sección de la portada (<code>#oferta</code>, <code>#noticias</code>, <code>#blog</code>),
                                    una página del sitio (<code>carreras</code>, <code>noticias</code>) o una dirección completa (<code>https://...</code>).
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </section>

                <section class="card flex-grow-1">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h3 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">how_to_reg</span> Insignia de inscripción</h3>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="inscripcion_abierta" id="inscripcion_abierta" value="1"<?= $configuracion['inscripcion_abierta'] == 1 ? ' checked' : '' ?>>
                            <label class="form-check-label small" for="inscripcion_abierta">Mostrar</label>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <label class="form-label" for="inscripcion_texto">Texto de la insignia</label>
                        <input type="text" name="inscripcion_texto" id="inscripcion_texto" class="form-control" maxlength="100" placeholder="Inscripción 2027 abierta" value="<?= old('inscripcion_texto', esc($configuracion['inscripcion_texto'])) ?>" aria-describedby="ayuda-insignia" data-vista="insignia">
                        <div class="form-text" id="ayuda-insignia">Aparece en la esquina de la imagen de la portada mientras esté activada.</div>
                    </div>
                </section>
            </div>

<?php // --- Columna derecha: vista previa (la actualiza admin.js) e imagen --- ?>
            <div class="col-xl-6 d-flex flex-column gap-4">
                <section class="card">
                    <div class="card-header"><h3 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">visibility</span> Vista previa</h3></div>
                    <div class="card-body p-4">
                        <div class="vista-portada" aria-hidden="true">
                            <span class="vp-insignia" data-vista-de="insignia"></span>
                            <span class="vp-etiqueta" data-vista-de="etiqueta"></span>
                            <span class="vp-titulo" data-vista-de="titulo"></span>
                            <span class="vp-texto" data-vista-de="texto"></span>
                            <span class="vp-botones">
                                <span class="vp-boton" data-vista-de="boton1"></span>
                                <span class="vp-boton vp-boton-borde" data-vista-de="boton2"></span>
                            </span>
                        </div>
                        <p class="form-text mb-0">Aproximada: en el sitio la imagen va a la derecha del texto y la insignia, sobre la imagen.</p>
                    </div>
                </section>

                <section class="card flex-grow-1">
                    <div class="card-header"><h3 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">image</span> Imagen</h3></div>
                    <div class="card-body p-4">
                        <div class="row">
                            <?= view('admin/partes/imagen_portada', [
                                'imagenes' => $imagenes,
                                'actual'   => old('portada_imagen_id', $configuracion['portada_imagen_id']),
                                'campo'    => 'portada_imagen_id',
                                'leyenda'  => 'Imagen de la portada',
                                'ayuda'    => 'Conviene una imagen horizontal de al menos 1200 px de ancho. Con "Sin imagen" se ve el recuadro institucional.',
                                'compacto' => true,
                            ]) ?>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>

<?php // ======================= ZONA 2: PIE DE PÁGINA ======================= ?>
    <section class="zona" id="zona-pie" aria-labelledby="zona-pie-titulo">
        <header class="zona-encabezado">
            <span class="zona-numero" aria-hidden="true">2</span>
            <div class="flex-grow-1">
                <h2 id="zona-pie-titulo">Pie de página</h2>
                <p>Se ve al final de todas las páginas del sitio. El email de contacto también aparece en la política de privacidad.</p>
            </div>
        </header>

        <div class="row g-4">
            <div class="col-xl-7">
                <section class="card h-100">
                    <div class="card-header"><h3 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">contact_mail</span> Descripción y contacto</h3></div>
                    <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="descripcion_pie">Descripción corta</label>
                            <input type="text" name="descripcion_pie" id="descripcion_pie" class="form-control" maxlength="255" value="<?= old('descripcion_pie', esc($configuracion['descripcion_pie'])) ?>" aria-describedby="ayuda-descripcion-pie">
                            <div class="form-text" id="ayuda-descripcion-pie">Una o dos líneas debajo del logo del pie.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="direccion">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text"><span class="material-symbols-outlined" aria-hidden="true">location_on</span></span>
                                <input type="text" name="direccion" id="direccion" class="form-control" maxlength="200" value="<?= old('direccion', esc($configuracion['direccion'])) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="telefono">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text"><span class="material-symbols-outlined" aria-hidden="true">call</span></span>
                                <input type="tel" name="telefono" id="telefono" class="form-control" maxlength="60" value="<?= old('telefono', esc($configuracion['telefono'])) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email_contacto">Email de contacto</label>
                            <div class="input-group">
                                <span class="input-group-text"><span class="material-symbols-outlined" aria-hidden="true">mail</span></span>
                                <input type="email" name="email_contacto" id="email_contacto" class="form-control" maxlength="150" value="<?= old('email_contacto', esc($configuracion['email_contacto'])) ?>">
                            </div>
                        </div>
                    </div>
                    </div>
                </section>
            </div>

            <div class="col-xl-5">
                <section class="card h-100">
                    <div class="card-header"><h3 class="card-titulo"><span class="material-symbols-outlined" aria-hidden="true">share</span> Redes sociales</h3></div>
                    <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="instagram">Instagram <span class="opcional">(enlace completo)</span></label>
                            <input type="url" name="instagram" id="instagram" class="form-control" maxlength="200" placeholder="https://instagram.com/..." value="<?= old('instagram', esc($configuracion['instagram'])) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="facebook">Facebook <span class="opcional">(enlace completo)</span></label>
                            <input type="url" name="facebook" id="facebook" class="form-control" maxlength="200" placeholder="https://facebook.com/..." value="<?= old('facebook', esc($configuracion['facebook'])) ?>">
                        </div>
                    </div>
                        <div class="form-text mt-2">Las redes que dejes vacías no se muestran.</div>
                    </div>
                </section>
            </div>
        </div>
    </section>

    <?= view('admin/partes/botones_formulario', ['textoGuardar' => 'Guardar cambios']) ?>
</form>
