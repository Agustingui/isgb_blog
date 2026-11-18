<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabla materias: plan de estudios de cada carrera.
 */
class MateriasModel extends Model
{
    protected $table         = 'materias';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['carrera_id', 'anio', 'nombre'];
}
