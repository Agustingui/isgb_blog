/**
 * @file Comportamiento del sitio público.
 *
 * - "Me gusta" de los posteos por AJAX.
 * - Registro de lectura de un posteo tras un tiempo con la pestaña visible.
 *
 * Requiere jQuery y comun.js (que agrega el token CSRF a los pedidos AJAX).
 */
$(function () {
    'use strict';

    /**
     * Envía por AJAX los formularios ".js-me-gusta" y actualiza el botón y el contador.
     *
     * Respuesta esperada: { ok: boolean, me_gusta: boolean, total: number, csrf: string }.
     * Si la sesión venció (401) recarga la página; ante cualquier otro error
     * envía el formulario de la forma tradicional, que también funciona sin JavaScript.
     *
     * @returns {void}
     */
    const iniciarMeGusta = () => {
        $(document).on('submit', '.js-me-gusta', function (evento) {
            evento.preventDefault();

            const formulario = this;
            const $form = $(formulario);
            const $boton = $form.find('button[type="submit"]').prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'json'
            })
                .done((respuesta) => {
                    $boton
                        .toggleClass('btn-danger', respuesta.me_gusta)
                        .toggleClass('btn-outline-danger', !respuesta.me_gusta)
                        .attr('aria-pressed', String(respuesta.me_gusta));
                    $('#total-me-gusta').text(respuesta.total);
                })
                .fail((xhr) => {
                    if (xhr.status === 401) {
                        // La sesión venció: al recargar se muestra el botón para ingresar.
                        window.location.reload();
                        return;
                    }

                    // Si falla el pedido AJAX, se envía el formulario de la forma tradicional.
                    formulario.submit();
                })
                .always(() => $boton.prop('disabled', false));
        });
    };

    /**
     * Registra la lectura del posteo después de data-segundos de lectura efectiva.
     *
     * Solo cuenta los segundos en que la pestaña está visible. El elemento
     * #lector existe únicamente si hay sesión y la lectura no estaba registrada.
     * Respuesta esperada: { ok: boolean, total: number, csrf: string }.
     *
     * @returns {void}
     */
    const iniciarLector = () => {
        const $lector = $('#lector');

        if (!$lector.length) {
            return;
        }

        const necesarios = Number.parseInt($lector.data('segundos'), 10);
        let segundos = 0;

        const reloj = window.setInterval(() => {
            if (document.visibilityState !== 'visible') {
                return;
            }

            segundos += 1;

            if (segundos < necesarios) {
                return;
            }

            window.clearInterval(reloj);

            // La lectura es solo estadística: si el pedido falla, no se avisa ni se interrumpe al lector.
            $.ajax({ url: $lector.data('url'), method: 'POST', dataType: 'json' })
                .done((respuesta) => $('#total-vistas').text(respuesta.total));
        }, 1000);
    };

    iniciarMeGusta();
    iniciarLector();
});
