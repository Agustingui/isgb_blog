<?php

namespace App\Controllers;

use App\Models\UsuariosModel;

/**
 * Ingreso y registro de alumnos con Google (OAuth 2.0 / OpenID Connect, flujo con código).
 *
 * Necesita google.clientId y google.clientSecret en el .env (ver README.md).
 *
 * - "state" (guardado en la sesión) evita que otro sitio inicie un ingreso en nombre del usuario (CSRF).
 * - "nonce" (también en la sesión) evita que se reutilice un id_token viejo.
 * - El id_token lo verifica Google en su endpoint tokeninfo (firma y vencimiento); acá se
 *   comprueba que sea para esta aplicación (aud), que lo haya emitido Google (iss) y que
 *   el email esté verificado. No se usa ningún dato que mande el navegador salvo el código.
 * - Las cuentas de administrador y editor no pueden ingresar con Google: si alguien
 *   controlara ese email en Google no podría tomar una cuenta del panel.
 * - Con google.dominio (por ejemplo, el dominio de Google Workspace de la institución)
 *   solo se aceptan cuentas de ese dominio, según el dato "hd" firmado por Google.
 * - Las cuentas nuevas se crean solo si el registro está abierto (auth.registroAbierto)
 *   o si hay un dominio institucional configurado; si no, solo ingresan alumnos existentes.
 */
class LoginGoogle extends BaseController
{
    /**
     * Redirige a la pantalla de Google para elegir la cuenta.
     */
    public function ingresar()
    {
        if (env('google.clientId') == '' || env('google.clientSecret') == '') {
            return redirect()->to(base_url('login'))->with('error', 'El ingreso con Google todavía no está configurado.');
        }

        // Google exige HTTPS en producción para la dirección de vuelta; además protege el código.
        if (ENVIRONMENT === 'production' && ! $this->request->isSecure()) {
            return redirect()->to(base_url('login'))->with('error', 'El ingreso con Google requiere una conexión segura (https).');
        }

        $state = bin2hex(random_bytes(16));
        $nonce = bin2hex(random_bytes(16));
        session()->set(['google_state' => $state, 'google_nonce' => $nonce]);

        $parametros = [
            'client_id'     => env('google.clientId'),
            'redirect_uri'  => base_url('login/google/volver'),
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'nonce'         => $nonce,
            'prompt'        => 'select_account',
        ];

        // "hd" solo hace que Google muestre primero las cuentas del dominio; la comprobación real está en volver().
        $dominio = strtolower(trim((string) env('google.dominio', '')));
        if ($dominio !== '') {
            $parametros['hd'] = $dominio;
        }

        return redirect()->to('https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($parametros));
    }

    /**
     * Recibe la respuesta de Google, verifica la identidad e inicia la sesión.
     */
    public function volver()
    {
        $code          = (string) $this->request->getGet('code');
        $state         = (string) $this->request->getGet('state');
        $stateGuardado = (string) session('google_state');
        $nonceGuardado = (string) session('google_nonce');
        session()->remove(['google_state', 'google_nonce']);

        // La persona canceló en la pantalla de Google.
        if ($this->request->getGet('error') !== null) {
            return redirect()->to(base_url('login'))->with('error', 'Se canceló el ingreso con Google.');
        }

        if ($code === '' || $stateGuardado === '' || ! hash_equals($stateGuardado, $state)) {
            return redirect()->to(base_url('login'))->with('error', 'No se pudo validar el ingreso con Google. Probá de nuevo.');
        }

        try {
            $cliente = service('curlrequest');

            // 1) Canjear el código por los tokens (pedido directo a Google con la clave secreta).
            $respuesta = $cliente->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'code'          => $code,
                    'client_id'     => env('google.clientId'),
                    'client_secret' => env('google.clientSecret'),
                    'redirect_uri'  => base_url('login/google/volver'),
                    'grant_type'    => 'authorization_code',
                ],
                'timeout'     => 10,
                'http_errors' => false,
            ]);
            $tokens = json_decode($respuesta->getBody(), true);

            if (empty($tokens['id_token'])) {
                return redirect()->to(base_url('login'))->with('error', 'Google no aceptó el ingreso. Probá de nuevo.');
            }

            // 2) Google verifica la firma y el vencimiento del id_token y devuelve sus datos.
            $respuesta = $cliente->get('https://oauth2.googleapis.com/tokeninfo', [
                'query'       => ['id_token' => $tokens['id_token']],
                'timeout'     => 10,
                'http_errors' => false,
            ]);
            $datos = $respuesta->getStatusCode() === 200 ? json_decode($respuesta->getBody(), true) : null;
        } catch (\Throwable $e) {
            log_message('error', 'Error al conectar con Google: ' . $e->getMessage());

            return redirect()->to(base_url('login'))->with('error', 'No pudimos conectarnos con Google. Probá más tarde.');
        }

        // 3) El token tiene que ser para esta aplicación, emitido por Google, vigente y del mismo pedido.
        $valido = is_array($datos)
            && ($datos['aud'] ?? '') === env('google.clientId')
            && in_array($datos['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)
            && (int) ($datos['exp'] ?? 0) > time()
            && $nonceGuardado !== ''
            && hash_equals($nonceGuardado, (string) ($datos['nonce'] ?? ''));

        if (! $valido) {
            log_message('warning', 'id_token de Google rechazado.');

            return redirect()->to(base_url('login'))->with('error', 'No se pudo validar el ingreso con Google. Probá de nuevo.');
        }

        // tokeninfo devuelve email_verified como texto ("true"); se aceptan las dos formas.
        if (empty($datos['email']) || ! in_array($datos['email_verified'] ?? '', ['true', true], true)) {
            return redirect()->to(base_url('login'))->with('error', 'Tu cuenta de Google no tiene el email verificado.');
        }

        // Dominio institucional: "hd" lo agrega Google solo a las cuentas de Google Workspace de ese dominio.
        $dominio = strtolower(trim((string) env('google.dominio', '')));
        if ($dominio !== '' && strtolower((string) ($datos['hd'] ?? '')) !== $dominio) {
            return redirect()->to(base_url('login'))->with('error', 'Ingresá con tu cuenta institucional de Google (@' . $dominio . ').');
        }

        $email   = strtolower($datos['email']);
        $usuario = $this->db->table('usuarios')->where('email', $email)->get()->getRowArray();

        // Una cuenta que está en la Papelera no puede ingresar (y tampoco se crea otra con ese email).
        if ($usuario !== null && $usuario['fecha_borrado'] !== null) {
            return redirect()->to(base_url('login'))->with('error', 'Esta cuenta está desactivada. Consultá en la secretaría del instituto.');
        }

        if ($usuario !== null && in_array($usuario['rol'], ['admin', 'editor'], true)) {
            return redirect()->to(base_url('login'))->with('error', 'Las cuentas del panel ingresan con email y contraseña.');
        }

        // Con el registro cerrado y sin dominio institucional, Google no crea cuentas nuevas.
        if ($usuario === null && $dominio === '' && ! env('auth.registroAbierto', true)) {
            return redirect()->to(base_url('login'))->with('error', 'No hay una cuenta de alumno con ese email. Consultá en la secretaría del instituto.');
        }

        // Primera vez: se crea la cuenta de alumno. La contraseña es un valor al azar que nadie
        // conoce (no se guarda nada de Google); si quiere una, usa "Olvidé mi contraseña".
        if ($usuario === null) {
            $usuarios = new UsuariosModel();
            $id       = $usuarios->insert([
                'nombre'   => mb_substr(trim($datos['name'] ?? '') ?: $email, 0, 120),
                'email'    => $email,
                'password' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
                'rol'      => 'alumnado',
            ]);
            $usuario = $usuarios->find($id);
        }

        return $this->iniciarSesion($usuario);
    }
}
