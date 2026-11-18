<?php

namespace App\Controllers;

use App\Models\UsuariosModel;

/**
 * Registro público de alumnos con email y contraseña.
 *
 * Las cuentas nuevas siempre tienen el rol "alumnado": el rol no se lee del
 * formulario, así nadie puede registrarse como editor o administrador.
 */
class Registro extends BaseController
{
    public function index()
    {
        if (session('rol')) {
            return redirect()->to(base_url());
        }

        if (! env('auth.registroAbierto', true)) {
            return redirect()->to(base_url('login'))->with('error', 'El registro de cuentas está cerrado. Consultá en la secretaría del instituto.');
        }

        return $this->vistaPublica('publico/registro', [
            'google' => env('google.clientId') != '',
            'titulo' => 'Crear cuenta de alumno',
        ]);
    }

    public function guardar()
    {
        if (session('rol')) {
            return redirect()->to(base_url());
        }

        // La institución puede cerrar el registro público con auth.registroAbierto = false en el .env.
        if (! env('auth.registroAbierto', true)) {
            return redirect()->to(base_url('login'))->with('error', 'El registro de cuentas está cerrado. Consultá en la secretaría del instituto.');
        }

        // El email se normaliza antes de validar, así "is_unique" compara lo mismo que se guarda.
        $datos = [
            'nombre'    => trim((string) $this->request->getPost('nombre')),
            'email'     => strtolower(trim((string) $this->request->getPost('email'))),
            'password'  => (string) $this->request->getPost('password'),
            'confirmar' => (string) $this->request->getPost('confirmar'),
        ];

        $reglas = [
            'nombre'    => ['label' => 'Nombre y apellido', 'rules' => 'required|max_length[120]'],
            'email'     => [
                'label'  => 'Email',
                'rules'  => 'required|valid_email|max_length[150]|is_unique[usuarios.email]',
                'errors' => ['is_unique' => 'Ya hay una cuenta con ese email. Ingresá o usá "Olvidé mi contraseña".'],
            ],
            'password'  => ['label' => 'Contraseña', 'rules' => 'required|min_length[8]|max_length[72]'],
            'confirmar' => ['label' => 'Repetir contraseña', 'rules' => 'required|matches[password]'],
        ];

        if (! $this->validateData($datos, $reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        // Hasta 20 cuentas por hora desde la misma IP, para frenar registros automáticos.
        // Es alto a propósito: en un aula todos los alumnos salen a internet con la misma IP.
        if (service('throttler')->check('registro_' . md5($this->request->getIPAddress()), 20, 3600) === false) {
            return redirect()->back()->withInput()->with('errores', ['Se crearon demasiadas cuentas desde esta conexión. Probá más tarde.']);
        }

        $usuarios = new UsuariosModel();
        $id       = $usuarios->insert([
            'nombre'   => $datos['nombre'],
            'email'    => $datos['email'],
            'password' => password_hash($datos['password'], PASSWORD_DEFAULT),
            'rol'      => 'alumnado',
        ]);

        return $this->iniciarSesion($usuarios->find($id))->with('mensaje', '¡Bienvenido/a! Tu cuenta de alumno ya está creada.');
    }
}
