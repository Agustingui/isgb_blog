<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabla media: imágenes y PDF subidos a public/uploads.
 */
class MediaModel extends Model
{
    protected $table         = 'media';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['alt', 'archivo'];

    // Fechas que CodeIgniter completa solo al insertar y al actualizar.
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_registro';
    protected $updatedField  = 'fecha_edicion';

    // Borrado lógico: delete() completa fecha_borrado y find()/findAll()/first() no devuelven esas filas.
    protected $useSoftDeletes = true;
    protected $deletedField   = 'fecha_borrado';
}
