<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabla noticias.
 */
class NoticiasModel extends Model
{
    protected $table         = 'noticias';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['titulo', 'slug', 'bajada', 'imagen_id', 'contenido', 'categoria', 'autor_id', 'publicado_en'];

    // Fechas que CodeIgniter completa solo al insertar y al actualizar.
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_registro';
    protected $updatedField  = 'fecha_edicion';

    // Borrado lógico: delete() completa fecha_borrado y find()/findAll()/first() no devuelven esas filas.
    protected $useSoftDeletes = true;
    protected $deletedField   = 'fecha_borrado';
}
