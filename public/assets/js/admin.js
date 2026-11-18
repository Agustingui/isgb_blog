/**
 * @file Comportamiento del panel de administración.
 *
 * - Tablas con DataTables 2.
 * - Borrado de registros por AJAX desde el modal de confirmación.
 * - Generación automática del slug a partir del título.
 * - Editor de noticias y posteos con Quill 2.
 * - Vista previa de la imagen de portada (elegida o subida en el formulario).
 * - Botón para mostrar u ocultar la contraseña.
 * - Vista previa de los textos de la portada del sitio.
 *
 * Requiere jQuery, Bootstrap 5 y comun.js. DataTables y Quill se cargan
 * solo en las páginas que los usan; cada módulo verifica que existan.
 */
$(function () {
    'use strict';

    /** Traducción oficial de DataTables al español (Argentina). */
    const IDIOMA_DATATABLES = 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/es-AR.json';

    /**
     * Muestra un aviso de Bootstrap en la zona de mensajes del panel (#avisos).
     *
     * @param {'success'|'danger'} tipo Variante de color del aviso.
     * @param {string} mensaje Texto del aviso (se inserta como texto, no como HTML).
     * @returns {void}
     */
    const mostrarAviso = (tipo, mensaje) => {
        const $aviso = $('<div>', { class: `alert alert-${tipo} alert-dismissible fade show`, role: tipo === 'danger' ? 'alert' : 'status' })
            .text(mensaje)
            .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>');

        $('#avisos').empty().append($aviso);
    };

    /**
     * Inicializa DataTables (API de DataTables 2) en cada tabla con la clase "tabla-datos".
     *
     * @returns {void}
     */
    const iniciarTablas = () => {
        $('.tabla-datos').each(function () {
            new DataTable(this, {
                language: { url: IDIOMA_DATATABLES },
                pageLength: 10,
                // Respeta el orden que ya trae el servidor en lugar de ordenar por la primera columna.
                order: [],
                // La última columna (botones de acción) no se ordena ni se usa en la búsqueda.
                columnDefs: [{ targets: -1, orderable: false, searchable: false }]
            });
        });
    };

    /**
     * Conecta los botones ".btn-borrar" con el modal de confirmación y borra por AJAX.
     *
     * Cada botón indica la URL de borrado en data-url, el nombre del registro
     * en data-nombre y el aviso (Papelera o borrado definitivo) en data-aviso. Si el servidor confirma, se quita la fila de la tabla con
     * la API de DataTables sin recargar la página; si falla, se muestra el
     * mensaje de error que devolvió el servidor.
     *
     * @returns {void}
     */
    const iniciarBorrado = () => {
        const elementoModal = document.getElementById('modal-borrar');

        if (!elementoModal) {
            return;
        }

        const modal = bootstrap.Modal.getOrCreateInstance(elementoModal);
        const $form = $('#form-borrar');
        let $filaPendiente = null;

        // Delegación de eventos: funciona también con filas que DataTables redibuja o pagina.
        $(document).on('click', '.btn-borrar', function () {
            const $boton = $(this);

            $filaPendiente = $boton.closest('tr');
            $form.attr('action', $boton.data('url'));
            $('#nombre-borrar').text($boton.data('nombre'));
            $('#aviso-borrar').text($boton.data('aviso') ?? '');
            modal.show();
        });

        $form.on('submit', function (evento) {
            evento.preventDefault();

            const $enviar = $form.find('[type="submit"]').prop('disabled', true);

            // POST con el formulario serializado (incluye el campo CSRF).
            // Respuesta esperada: { ok: boolean, mensaje: string, csrf: string }.
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'json'
            })
                .done((respuesta) => {
                    const $tabla = $filaPendiente?.closest('table');

                    if ($tabla?.length && DataTable.isDataTable($tabla[0])) {
                        // draw(false) redibuja sin volver a la primera página.
                        $tabla.DataTable().row($filaPendiente).remove().draw(false);
                    }

                    mostrarAviso('success', respuesta.mensaje);
                })
                .fail((xhr) => {
                    mostrarAviso('danger', xhr.responseJSON?.mensaje ?? 'No se pudo borrar. Recargá la página y probá de nuevo.');
                })
                .always(() => {
                    $enviar.prop('disabled', false);
                    $filaPendiente = null;
                    modal.hide();
                });
        });
    };

    /**
     * Completa el slug a partir del campo indicado en data-origen-slug.
     *
     * Quita tildes y caracteres especiales y usa guiones. Si la persona edita
     * el slug a mano, o el slug ya tenía valor al cargar, deja de completarse solo.
     *
     * @returns {void}
     */
    const iniciarSlugs = () => {
        $('input[name="slug"]').each(function () {
            $(this).data('manual', this.value !== '');
        });

        $(document).on('input', 'input[name="slug"]', function () {
            $(this).data('manual', true);
        });

        $(document).on('input', '[data-origen-slug]', function () {
            const $destino = $($(this).data('origen-slug'));

            if ($destino.data('manual') === true) {
                return;
            }

            $destino.val(
                this.value
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[̀-ͯ]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '')
            );
        });
    };

    /**
     * Inicializa el editor Quill (noticias y posteos) y lo sincroniza con el campo oculto "contenido".
     *
     * El texto de ayuda del editor vacío se toma de data-placeholder.
     *
     * Se guarda quill.root.innerHTML y no getSemanticHTML() porque en Quill
     * 2.0.3 ese método convierte todos los espacios en &nbsp;. El servidor
     * sanitiza el HTML recibido (contenido_html_seguro) y entiende el formato
     * de Quill: listas (li con data-list), bloques de código, videos y las
     * clases de alineación y tipografía.
     *
     * @returns {void}
     */
    const iniciarQuill = () => {
        const contenedor = document.getElementById('editor');
        const campo = document.getElementById('contenido-hidden');

        if (!contenedor || !campo || typeof Quill === 'undefined') {
            return;
        }

        // Enlaces escritos sin "https://" (www.ejemplo.com) o emails: se completan para
        // que el servidor no los descarte al guardar (solo acepta http, https, mailto, / y #).
        const Enlace = Quill.import('formats/link');
        const sanitizarEnlace = Enlace.sanitize;
        Enlace.sanitize = (url) => {
            let valor = String(url).trim();

            if (/^[^\s@/]+@[^\s@/]+\.[^\s@/]+$/.test(valor)) {
                valor = 'mailto:' + valor;
            } else if (valor !== '' && !/^(https?:|mailto:|\/|#)/i.test(valor)) {
                valor = 'https://' + valor;
            }

            return sanitizarEnlace.call(Enlace, valor);
        };

        /**
         * Botón "Imagen": sube el archivo a la biblioteca (AdminMedia::imagenEditor) y lo inserta.
         *
         * Reemplaza al comportamiento de Quill, que guarda la imagen dentro del
         * texto en base64 y lo vuelve enorme.
         *
         * @returns {void}
         */
        const subirImagen = () => {
            const entrada = $('<input type="file" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp">');

            entrada.on('change', () => {
                const datos = new FormData();
                datos.append('imagen_nueva', entrada[0].files[0]);

                $.ajax({
                    url: contenedor.dataset.subirImagen,
                    method: 'POST',
                    data: datos,
                    processData: false,
                    contentType: false,
                    dataType: 'json'
                }).done((respuesta) => {
                    // La imagen va en su propio renglón, así se puede alinear sin mover el texto.
                    let indice = (quill.getSelection(true) || { index: quill.getLength() - 1 }).index;

                    if (quill.getLine(indice)[1] > 0) {
                        quill.insertText(indice, '\n', 'user');
                        indice += 1;
                    }

                    quill.insertEmbed(indice, 'image', respuesta.url, 'user');

                    if (quill.getLine(indice)[0].length() > 2) {
                        quill.insertText(indice + 1, '\n', 'user');
                    }

                    quill.setSelection(indice + 2, 0);
                }).fail((xhr) => {
                    alert((xhr.responseJSON && xhr.responseJSON.mensaje) || 'No se pudo subir la imagen.');
                });
            });

            entrada.trigger('click');
        };

        /**
         * Botón "Enlace": pide la dirección con el cuadro de Quill, que arranca vacío.
         *
         * Sin texto seleccionado avisa en lugar de no hacer nada (lo que hace Quill).
         *
         * @param {boolean} activo false si se apretó sobre un enlace para quitarlo.
         * @returns {void}
         */
        const enlace = (activo) => {
            if (!activo) {
                quill.format('link', false, 'user');
                return;
            }

            const seleccion = quill.getSelection(true);

            if (!seleccion || seleccion.length === 0) {
                alert('Primero seleccioná el texto que querés convertir en enlace.');
                return;
            }

            quill.theme.tooltip.edit('link', '');
        };

        const quill = new Quill(contenedor, {
            theme: 'snow',
            placeholder: contenedor.dataset.placeholder ?? '',
            modules: {
                // Solo se ofrecen los formatos que el sanitizador del servidor conserva.
                toolbar: {
                    container: [
                        [{ header: [1, 2, 3, false] }, { font: [] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ list: 'ordered' }, { list: 'bullet' }, { align: [] }],
                        ['blockquote', 'code-block', 'link', 'image', 'video'],
                        ['clean']
                    ],
                    handlers: { image: subirImagen, link: enlace }
                }
            }
        });

        // Ejemplos que muestra el cuadro de enlace y de video cuando está vacío.
        quill.theme.tooltip.textbox.dataset.link = 'https://www.ejemplo.com';
        quill.theme.tooltip.textbox.dataset.video = 'https://www.youtube.com/watch?v=…';

        // Imágenes del texto: al hacer clic en una aparece una barra para elegir el
        // tamaño (atributo width) y la posición (alineación del párrafo de la imagen).
        const icono = (nombre, texto) => `<span class="material-symbols-outlined" aria-hidden="true">${nombre}</span><span class="visually-hidden">${texto}</span>`;
        const $barraImagen = $(
            '<div class="barra-imagen" role="toolbar" aria-label="Opciones de la imagen" hidden>' +
                '<button type="button" data-ancho="25%" title="Chica">25%</button>' +
                '<button type="button" data-ancho="50%" title="Mediana">50%</button>' +
                '<button type="button" data-ancho="75%" title="Grande">75%</button>' +
                '<button type="button" data-ancho="100%" title="Todo el ancho">100%</button>' +
                '<button type="button" data-ancho="" title="Tamaño original">Original</button>' +
                '<span class="separador" aria-hidden="true"></span>' +
                `<button type="button" data-alinear="" title="A la izquierda">${icono('format_align_left', 'A la izquierda')}</button>` +
                `<button type="button" data-alinear="center" title="Al centro">${icono('format_align_center', 'Al centro')}</button>` +
                `<button type="button" data-alinear="right" title="A la derecha">${icono('format_align_right', 'A la derecha')}</button>` +
            '</div>'
        ).appendTo(contenedor);
        let imagenElegida = null;

        const ocultarBarraImagen = () => {
            imagenElegida = null;
            $barraImagen.prop('hidden', true);
        };

        $(quill.root).on('click', 'img', function () {
            const caja = contenedor.getBoundingClientRect();
            const foto = this.getBoundingClientRect();

            imagenElegida = this;
            $barraImagen.css({ top: foto.top - caja.top + 8, left: foto.left - caja.left + 8 }).prop('hidden', false);
        });

        // mousedown sin acción: así el clic en la barra no le quita el cursor al editor.
        $barraImagen.on('mousedown', (evento) => evento.preventDefault());

        $barraImagen.on('click', 'button', function () {
            if (!imagenElegida) {
                return;
            }

            const indice = quill.getIndex(Quill.find(imagenElegida));

            if (this.dataset.ancho !== undefined) {
                quill.formatText(indice, 1, 'width', this.dataset.ancho || false, 'user');
            } else {
                quill.formatLine(indice, 1, 'align', this.dataset.alinear || false, 'user');
            }

            ocultarBarraImagen();
        });

        // Se oculta al hacer clic en otro lado o al escribir.
        $(document).on('click', (evento) => {
            if (!$(evento.target).closest('.barra-imagen, .ql-editor img').length) {
                ocultarBarraImagen();
            }
        });
        quill.on('text-change', ocultarBarraImagen);

        // Accesibilidad: el área editable se anuncia como campo de texto con su etiqueta.
        quill.root.setAttribute('role', 'textbox');
        quill.root.setAttribute('aria-multiline', 'true');
        quill.root.setAttribute('aria-labelledby', contenedor.dataset.etiqueta);

        // Carga el contenido guardado (o el reenviado tras un error de validación)
        // en modo "silent" para no disparar text-change.
        if (campo.value) {
            quill.clipboard.dangerouslyPasteHTML(campo.value, 'silent');
        }

        /**
         * Indica si el editor tiene texto visible (Quill siempre deja un salto de línea final).
         *
         * @returns {boolean}
         */
        const tieneTexto = () => quill.getText().trim() !== '';

        /**
         * Copia el HTML del editor al campo oculto y quita la marca de error si ya hay texto.
         *
         * El mensaje #contenido-error lo muestra Bootstrap cuando #editor tiene la
         * clase .is-invalid (regla ".is-invalid ~ .invalid-feedback").
         *
         * @returns {void}
         */
        const sincronizar = () => {
            const conTexto = tieneTexto();

            campo.value = conTexto ? quill.root.innerHTML : '';

            if (conTexto) {
                contenedor.classList.remove('is-invalid');
            }
        };

        quill.on('text-change', sincronizar);

        // Validación del lado del cliente: el servidor vuelve a validar (BaseController::erroresDeContenido()).
        $(contenedor).closest('form').on('submit', (evento) => {
            sincronizar();

            if (!tieneTexto()) {
                evento.preventDefault();
                contenedor.classList.add('is-invalid');
                quill.focus();
            }
        });
    };

    /**
     * Muestra la vista previa de la imagen de portada en los bloques [data-imagen-portada].
     *
     * Si se eligió un archivo nuevo se muestra ese (con una URL temporal del
     * navegador); si no, la imagen elegida en el select (atributo data-src).
     *
     * @returns {void}
     */
    const iniciarImagenPortada = () => {
        $('[data-imagen-portada]').each(function () {
            const $bloque = $(this);
            const $select = $bloque.find('select');
            const archivo = $bloque.find('input[type="file"]')[0];
            const $imagen = $bloque.find('.vista-imagen img');
            const $vacio = $bloque.find('.vista-imagen figcaption');
            let urlTemporal = null;

            /**
             * Muestra la imagen indicada o el recuadro "Sin imagen".
             *
             * @param {string} src URL de la imagen, o cadena vacía.
             * @returns {void}
             */
            const mostrar = (src) => {
                $imagen.attr('src', src).prop('hidden', !src);
                $vacio.prop('hidden', Boolean(src));
            };

            /** Actualiza la vista previa según el archivo elegido o la opción del select. */
            const actualizar = () => {
                if (urlTemporal) {
                    URL.revokeObjectURL(urlTemporal);
                    urlTemporal = null;
                }

                const nuevo = archivo.files[0];

                if (nuevo) {
                    urlTemporal = URL.createObjectURL(nuevo);
                    mostrar(urlTemporal);
                    return;
                }

                mostrar($select.find(':selected').data('src') ?? '');
            };

            $select.on('change', actualizar);
            $(archivo).on('change', actualizar);
        });
    };

    /**
     * Botones que muestran u ocultan una contraseña (data-ver-password="#id-del-campo").
     *
     * @returns {void}
     */
    const iniciarVerPassword = () => {
        $(document).on('click', '[data-ver-password]', function () {
            const $boton = $(this);
            const $campo = $($boton.data('ver-password'));
            const mostrar = $campo.attr('type') === 'password';
            const texto = mostrar ? 'Ocultar la contraseña' : 'Mostrar la contraseña';

            $campo.attr('type', mostrar ? 'text' : 'password');
            $boton.attr({ 'aria-pressed': String(mostrar), title: texto });
            $boton.find('.material-symbols-outlined').text(mostrar ? 'visibility_off' : 'visibility');
            $boton.find('.visually-hidden').text(texto);
        });
    };

    /**
     * Vista previa de la portada del sitio en "Datos del instituto".
     *
     * Cada campo con data-vista="clave" se copia al elemento data-vista-de="clave";
     * si el campo está vacío se muestra su placeholder, que es el texto original
     * (igual que en el sitio). "Restaurar textos originales" vacía los campos.
     *
     * @returns {void}
     */
    const iniciarVistaPortada = () => {
        const $bloque = $('[data-portada]');

        if (!$bloque.length) {
            return;
        }

        const $mostrarBoton2 = $bloque.find('#portada_boton2_visible');

        const actualizar = () => {
            $bloque.find('[data-vista]').each(function () {
                const texto = this.value.trim() || this.placeholder;
                $bloque.find(`[data-vista-de="${$(this).data('vista')}"]`).text(texto);
            });
            $bloque.find('[data-vista-de="boton2"]').prop('hidden', !$mostrarBoton2.prop('checked'));
            $bloque.find('[data-vista-de="insignia"]').prop('hidden', !$bloque.find('#inscripcion_abierta').prop('checked'));
        };

        $bloque.on('input change', 'input, textarea', actualizar);

        $bloque.on('click', '[data-restaurar-portada]', () => {
            $bloque.find('[name^="portada_"]').filter('input[type="text"], textarea').val('');
            $mostrarBoton2.prop('checked', true);
            actualizar();
        });

        actualizar();
    };

    iniciarTablas();
    iniciarBorrado();
    iniciarSlugs();
    iniciarQuill();
    iniciarImagenPortada();
    iniciarVerPassword();
    iniciarVistaPortada();
});
