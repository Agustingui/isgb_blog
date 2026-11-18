/**
 * @file Código que comparten el sitio público y el panel: el token CSRF en los pedidos AJAX.
 *
 * CodeIgniter cambia el token en cada POST, así que cada respuesta JSON trae
 * el nuevo en "csrf" y hay que copiarlo a la <meta> y a los formularios.
 */

/**
 * Devuelve el token CSRF vigente (lo escribe csrf_meta() en el <head>).
 *
 * @returns {string}
 */
function tokenCsrf() {
    return $('meta[name="X-CSRF-TOKEN"]').attr('content') || '';
}

/**
 * Guarda el token nuevo en la <meta> y en los campos ocultos que tenían el anterior.
 *
 * @param {string|undefined} nuevo Token recibido del servidor.
 */
function actualizarTokenCsrf(nuevo) {
    const anterior = tokenCsrf();

    if (!nuevo || nuevo === anterior) {
        return;
    }

    $('meta[name="X-CSRF-TOKEN"]').attr('content', nuevo);
    $('input[type="hidden"]').filter(function () {
        return this.value === anterior;
    }).val(nuevo);
}

$(function () {
    // Los pedidos AJAX al propio sitio se marcan como AJAX y llevan el token CSRF.
    // A otros sitios (como el idioma de DataTables) no se les agrega nada: el
    // token no debe salir del sitio y esas cabeceras harían fallar el pedido.
    $.ajaxSetup({
        beforeSend: function (xhr, opciones) {
            if (!opciones.crossDomain) {
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('X-CSRF-TOKEN', tokenCsrf());
            }
        }
    });

    // Al terminar cualquier pedido, se guarda el token nuevo si vino en la respuesta.
    $(document).ajaxComplete(function (evento, xhr) {
        actualizarTokenCsrf(xhr.responseJSON && xhr.responseJSON.csrf);
    });
});
