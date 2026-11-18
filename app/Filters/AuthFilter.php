<?php

namespace App\Filters;

use App\Models\UsuariosModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtro "auth": solo deja pasar a usuarios con sesión y con un rol permitido.
 *
 * En las rutas se usa como "auth" (admin y editor) o "auth:rol1,rol2".
 * En cada pedido se lee el usuario de la base, así un usuario borrado o con
 * otro rol pierde el acceso al instante.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $usuario = null;

        if (session('id_usuario')) {
            $usuario = (new UsuariosModel())->find(session('id_usuario'));
        }

        if ($usuario === null) {
            session()->remove(['id_usuario', 'nombre', 'email', 'rol']);

            return $this->rechazar($request, 401, 'login', 'Ingresá con tu usuario para continuar.');
        }

        // Datos al día por si un administrador los cambió durante la sesión.
        session()->set(['nombre' => $usuario['nombre'], 'email' => $usuario['email'], 'rol' => $usuario['rol']]);

        $permitidos = $arguments ?? ['admin', 'editor'];

        if (! in_array($usuario['rol'], $permitidos)) {
            return $this->rechazar($request, 403, '', 'No tenés permiso para entrar ahí.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    /**
     * En pedidos AJAX responde JSON con el código de error; en los demás redirige con el mensaje.
     */
    private function rechazar($request, $estado, $destino, $mensaje)
    {
        if ($request->isAJAX()) {
            return service('response')
                ->setStatusCode($estado)
                ->setJSON(['ok' => false, 'mensaje' => $mensaje, 'csrf' => csrf_hash()]);
        }

        return redirect()->to(base_url($destino))->with('error', $mensaje);
    }
}
