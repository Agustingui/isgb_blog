<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabla posteos: entradas del blog.
 */
class PosteosModel extends Model
{
    protected $table         = 'posteos';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['titulo', 'slug', 'extracto', 'imagen_id', 'contenido', 'carrera_id', 'estado', 'autor_id', 'publicado_en'];

    // Fechas que CodeIgniter completa solo al insertar y al actualizar.
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_registro';
    protected $updatedField  = 'fecha_edicion';

    // Borrado lógico: delete() completa fecha_borrado y find()/findAll()/first() no devuelven esas filas.
    protected $useSoftDeletes = true;
    protected $deletedField   = 'fecha_borrado';
}
