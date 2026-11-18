<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabla usuarios.
 */
class UsuariosModel extends Model
{
    protected $table         = 'usuarios';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nombre', 'email', 'password', 'rol', 'token_reset', 'token_reset_expira'];

    // Fechas que CodeIgniter completa solo al insertar y al actualizar.
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_registro';
    protected $updatedField  = 'fecha_edicion';

    // Borrado lógico: delete() completa fecha_borrado y find()/findAll()/first() no devuelven esas filas.
    protected $useSoftDeletes = true;
    protected $deletedField   = 'fecha_borrado';
}
