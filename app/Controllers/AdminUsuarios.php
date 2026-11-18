<?php

namespace App\Controllers;

use App\Models\UsuariosModel;

/**
 * Panel: alta, edición y borrado de usuarios (solo administradores).
 *
 * El sitio nunca puede quedarse sin administradores, y nadie puede borrarse a sí mismo desde acá.
 */
class AdminUsuarios extends BaseController
{
    public function index()
    {
        return $this->vistaAdmin('admin/usuarios/listado', [
            'usuarios' => $this->db->table('usuarios')->where('fecha_borrado', null)->orderBy('nombre')->get()->getResultArray(),
            'titulo'   => 'Usuarios',
            'menu'     => 'usuarios',
        ]);
    }

    public function nuevo()
    {
        return $this->vistaAdmin('admin/usuarios/formulario', [
            'usuario' => null,
            'titulo'  => 'Nuevo usuario',
            'menu'    => 'usuarios',
        ]);
    }

    public function editar($id)
    {
        $usuario = (new UsuariosModel())->find($id);

        if ($usuario === null) {
            return redirect()->to(base_url('admin/usuarios'))->with('error', 'No se encontró el usuario.');
        }

        return $this->vistaAdmin('admin/usuarios/formulario', [
            'usuario' => $usuario,
            'titulo'  => 'Editar usuario',
            'menu'    => 'usuarios',
        ]);
    }

    public function insertar()
    {
        $reglas = [
            'nombre'   => ['label' => 'Nombre', 'rules' => 'required|max_length[120]'],
            'email'    => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[150]|is_unique[usuarios.email]'],
            'password' => ['label' => 'Contraseña', 'rules' => 'required|min_length[8]|max_length[72]'],
            'rol'      => ['label' => 'Rol', 'rules' => 'required|in_list[admin,editor,alumnado]'],
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        (new UsuariosModel())->insert([
            'nombre'   => $this->request->getPost('nombre'),
            'email'    => strtolower(trim($this->request->getPost('email'))),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'rol'      => $this->request->getPost('rol'),
        ]);

        return redirect()->to(base_url('admin/usuarios'))->with('mensaje', 'El usuario se guardó correctamente.');
    }

    public function actualizar()
    {
        $usuarios = new UsuariosModel();
        $id       = (int) $this->request->getPost('id');
        $usuario  = $usuarios->find($id);

        if ($usuario === null) {
            return redirect()->to(base_url('admin/usuarios'))->with('error', 'No se encontró el usuario.');
        }

        $reglas = [
            'nombre'   => ['label' => 'Nombre', 'rules' => 'required|max_length[120]'],
            'email'    => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[150]|is_unique[usuarios.email,id,' . $id . ']'],
            // Vacía = no se cambia la contraseña.
            'password' => ['label' => 'Contraseña', 'rules' => 'permit_empty|min_length[8]|max_length[72]'],
            'rol'      => ['label' => 'Rol', 'rules' => 'required|in_list[admin,editor,alumnado]'],
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $rol = $this->request->getPost('rol');

        if ($rol !== 'admin' && $this->esElUnicoAdmin($usuario)) {
            return redirect()->back()->withInput()->with('errores', ['rol' => 'Es el único administrador: asigná ese rol a otra persona antes de cambiarlo.']);
        }

        $datos = [
            'nombre' => $this->request->getPost('nombre'),
            'email'  => strtolower(trim($this->request->getPost('email'))),
            'rol'    => $rol,
        ];

        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $datos['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $usuarios->update($id, $datos);

        // Si se editó a sí mismo, se actualizan también los datos de su sesión.
        if ($id === (int) session('id_usuario')) {
            session()->set(['nombre' => $datos['nombre'], 'email' => $datos['email'], 'rol' => $datos['rol']]);
        }

        return redirect()->to(base_url('admin/usuarios'))->with('mensaje', 'El usuario se actualizó correctamente.');
    }

    public function borrar($id)
    {
        $usuarios = new UsuariosModel();
        $usuario  = $usuarios->find($id);

        if ($usuario === null) {
            return $this->respuestaAccion(false, 'No se encontró el usuario.', 'admin/usuarios');
        }

        if ((int) $id === (int) session('id_usuario')) {
            return $this->respuestaAccion(false, 'No podés borrar tu propio usuario.', 'admin/usuarios');
        }

        if ($this->esElUnicoAdmin($usuario)) {
            return $this->respuestaAccion(false, 'No se puede borrar al único administrador.', 'admin/usuarios');
        }

        $usuarios->delete($id);

        return $this->respuestaAccion(true, 'El usuario se movió a la Papelera (ya no puede ingresar).', 'admin/usuarios');
    }

    private function esElUnicoAdmin($usuario)
    {
        return $usuario['rol'] === 'admin' && $this->db->table('usuarios')->where('rol', 'admin')->where('fecha_borrado', null)->countAllResults() <= 1;
    }
}
