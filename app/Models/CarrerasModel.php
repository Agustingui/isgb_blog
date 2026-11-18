<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabla carreras: oferta académica.
 */
class CarrerasModel extends Model
{
    protected $table         = 'carreras';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nombre', 'slug', 'activa', 'duracion', 'descripcion', 'imagen_id', 'plan_pdf_id', 'resolucion_pdf_id'];

    // Fechas que CodeIgniter completa solo al insertar y al actualizar.
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_registro';
    protected $updatedField  = 'fecha_edicion';

    // Borrado lógico: delete() completa fecha_borrado y find()/findAll()/first() no devuelven esas filas.
    protected $useSoftDeletes = true;
    protected $deletedField   = 'fecha_borrado';
}
