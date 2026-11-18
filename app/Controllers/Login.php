<?php

namespace App\Controllers;

use App\Models\UsuariosModel;

/**
 * Ingreso con email y contraseña, salida y recuperación de contraseña.
 */
class Login extends BaseController
{
    public function index()
    {
        if (session('rol') === 'admin' || session('rol') === 'editor') {
            return redirect()->to(base_url('admin'));
        }

        return $this->vistaPublica('publico/login', [
            'google'   => env('google.clientId') != '',
            'registro' => (bool) env('auth.registroAbierto', true),
            'titulo'   => 'Ingresar',
        ]);
    }

    public function ingresar()
    {
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        // Máximo 5 intentos cada 5 minutos por IP y email.
        $limite = service('throttler');
        if ($limite->check('login_' . md5($this->request->getIPAddress() . $email), 5, 300) === false) {
            $minutos = (int) ceil($limite->getTokenTime() / 60);

            return redirect()->to(base_url('login'))->with('error', 'Hiciste demasiados intentos. Esperá ' . $minutos . ' minuto(s) y probá de nuevo.');
        }

        $usuario = null;
        if ($this->validate(['email' => 'required|valid_email', 'password' => 'required'])) {
            $usuario = $this->db->table('usuarios')->where('email', $email)->where('fecha_borrado', null)->get()->getRowArray();
        }

        // El mismo mensaje para email inexistente y contraseña incorrecta, para no revelar qué cuentas existen.
        if ($usuario === null || ! password_verify($password, $usuario['password'])) {
            return redirect()->to(base_url('login'))->with('error', 'El email o la contraseña no son correctos.');
        }

        return $this->iniciarSesion($usuario);
    }

    public function salir()
    {
        session()->destroy();

        return redirect()->to(base_url());
    }

    public function olvide()
    {
        return $this->vistaPublica('publico/olvide', ['titulo' => 'Recuperar contraseña']);
    }

    /**
     * Envía por email un enlace para crear una contraseña nueva (válido 1 hora).
     */
    public function enviarOlvide()
    {
        // Máximo 3 pedidos cada 10 minutos por IP.
        if (service('throttler')->check('olvide_' . md5($this->request->getIPAddress()), 3, 600) === false) {
            return redirect()->to(base_url('login/olvide'))->with('error', 'Pediste demasiados enlaces seguidos. Esperá unos minutos.');
        }

        if (! $this->validate(['email' => 'required|valid_email|max_length[150]'])) {
            return redirect()->to(base_url('login/olvide'))->with('error', 'Ingresá un email válido.');
        }

        $usuario = $this->db->table('usuarios')->where('email', $this->request->getPost('email'))->where('fecha_borrado', null)->get()->getRowArray();

        if ($usuario !== null) {
            // En la base se guarda solo el hash del token; el token en claro viaja en el email.
            $token = bin2hex(random_bytes(32));

            (new UsuariosModel())->update($usuario['id'], [
                'token_reset'        => hash('sha256', $token),
                'token_reset_expira' => date('Y-m-d H:i:s', time() + 3600),
            ]);

            $enlace = base_url('login/restablecer/' . $token);
            $enviado = false;

            // El remitente y el servidor de correo los configura la institución en el .env
            // (email.fromEmail, email.SMTPHost, ...). Sin remitente no se intenta enviar.
            $configuracion = config('Email');

            if ($configuracion->fromEmail !== '') {
                $email = service('email');
                $email->setFrom($configuracion->fromEmail, $configuracion->fromName ?: 'Instituto Superior Gaspar Benavento');
                $email->setTo($usuario['email']);
                $email->setSubject('Restablecer tu contraseña');
                $email->setMailType('html'); // la plantilla emails/restablecer es HTML
                $email->setMessage(view('emails/restablecer', ['nombre' => $usuario['nombre'], 'enlace' => $enlace]));

                $enviado = $email->send(false);

                if (! $enviado) {
                    log_message('error', 'No se pudo enviar el mail de restablecimiento al usuario ' . $usuario['id']);
                }
            } else {
                log_message('error', 'Recuperación de contraseña sin enviar: falta configurar email.fromEmail en el .env.');
            }

            // Solo en desarrollo (localhost sin servidor de correo): el enlace queda en writable/logs
            // para poder probar la recuperación. En producción nunca se escribe el token en el log.
            if (! $enviado && ENVIRONMENT === 'development') {
                log_message('notice', 'Enlace de recuperación para ' . $usuario['email'] . ' (solo desarrollo): ' . $enlace);
            }
        }

        // Mismo mensaje exista o no el email.
        return redirect()->to(base_url('login'))->with('mensaje', 'Si el email está registrado, te enviamos un enlace para crear una contraseña nueva. Dura 1 hora.');
    }

    public function restablecer($token)
    {
        if ($this->buscarPorToken($token) === null) {
            return redirect()->to(base_url('login/olvide'))->with('error', 'El enlace venció o no es válido. Pedí uno nuevo.');
        }

        return $this->vistaPublica('publico/restablecer', [
            'token'  => $token,
            'titulo' => 'Nueva contraseña',
        ]);
    }

    public function guardarRestablecer()
    {
        $token   = (string) $this->request->getPost('token');
        $usuario = $this->buscarPorToken($token);

        if ($usuario === null) {
            return redirect()->to(base_url('login/olvide'))->with('error', 'El enlace venció o no es válido. Pedí uno nuevo.');
        }

        if (! $this->validate(['password' => 'required|min_length[8]|max_length[72]', 'confirmar' => 'required|matches[password]'])) {
            return redirect()->to(base_url('login/restablecer/' . $token))
                ->with('error', 'La contraseña tiene que tener entre 8 y 72 caracteres y las dos tienen que coincidir.');
        }

        (new UsuariosModel())->update($usuario['id'], [
            'password'           => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'token_reset'        => null,
            'token_reset_expira' => null,
        ]);

        return redirect()->to(base_url('login'))->with('mensaje', 'Listo, ya podés ingresar con tu contraseña nueva.');
    }

    /**
     * Busca el usuario dueño de un token de recuperación que todavía no venció.
     */
    private function buscarPorToken($token)
    {
        if ($token === '') {
            return null;
        }

        return $this->db->table('usuarios')
            ->where('token_reset', hash('sha256', $token))
            ->where('token_reset_expira >', date('Y-m-d H:i:s'))
            ->where('fecha_borrado', null)
            ->get()
            ->getRowArray();
    }
}
