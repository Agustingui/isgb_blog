<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabla interacciones: "me gusta" y lecturas de los posteos.
 */
class InteraccionesModel extends Model
{
    protected $table         = 'interacciones';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['usuario_id', 'posteo_id', 'tipo'];

    // Fechas que CodeIgniter completa solo al insertar y al actualizar.
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_registro';
    protected $updatedField  = ''; // La tabla no tiene fecha de edición.
}
