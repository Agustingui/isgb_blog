<?php

namespace App\Controllers;

use App\Models\UsuariosModel;

/**
 * "Mi cuenta": cada usuario edita su nombre, cambia su contraseña o borra su cuenta.
 */
class Cuenta extends BaseController
{
    public function index()
    {
        return $this->vistaPublica('publico/cuenta', [
            'usuario' => (new UsuariosModel())->find(session('id_usuario')),
            'titulo'  => 'Mi cuenta',
        ]);
    }

    public function actualizar()
    {
        if (! $this->validate(['nombre' => 'required|max_length[120]'])) {
            return redirect()->to(base_url('cuenta'))->with('error', 'Escribí tu nombre (hasta 120 caracteres).');
        }

        $nombre = (string) $this->request->getPost('nombre');

        (new UsuariosModel())->update(session('id_usuario'), ['nombre' => $nombre]);
        session()->set('nombre', $nombre);

        return redirect()->to(base_url('cuenta'))->with('mensaje', 'Tus datos se actualizaron.');
    }

    public function password()
    {
        $reglas = [
            'password_actual' => 'required',
            'password_nuevo'  => 'required|min_length[8]|max_length[72]',
            'confirmar'       => 'required|matches[password_nuevo]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->to(base_url('cuenta'))->with('error', 'La contraseña nueva tiene que tener entre 8 y 72 caracteres y las dos tienen que coincidir.');
        }

        $usuarios = new UsuariosModel();
        $usuario  = $usuarios->find(session('id_usuario'));

        if (! password_verify((string) $this->request->getPost('password_actual'), $usuario['password'])) {
            return redirect()->to(base_url('cuenta'))->with('error', 'La contraseña actual no es correcta.');
        }

        $usuarios->update($usuario['id'], ['password' => password_hash((string) $this->request->getPost('password_nuevo'), PASSWORD_DEFAULT)]);

        return redirect()->to(base_url('cuenta'))->with('mensaje', 'Tu contraseña se cambió.');
    }

    public function borrar()
    {
        if ($this->request->getPost('confirmacion') !== 'BORRAR') {
            return redirect()->to(base_url('cuenta'))->with('error', 'Para borrar tu cuenta escribí la palabra BORRAR.');
        }

        $usuarios = new UsuariosModel();
        $usuario  = $usuarios->find(session('id_usuario'));

        // El sitio no puede quedarse sin administradores.
        if ($usuario['rol'] === 'admin' && $this->db->table('usuarios')->where('rol', 'admin')->where('fecha_borrado', null)->countAllResults() <= 1) {
            return redirect()->to(base_url('cuenta'))->with('error', 'Sos el único administrador. Primero asigná ese rol a otra persona.');
        }

        // Borrado real (no va a la Papelera), como promete la política de privacidad.
        // Las interacciones del usuario se borran en cascada (clave foránea).
        $usuarios->delete($usuario['id'], true);
        session()->destroy();

        return redirect()->to(base_url());
    }
}
