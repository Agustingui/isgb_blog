<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabla configuracion: datos del instituto (una sola fila).
 */
class ConfiguracionModel extends Model
{
    protected $table         = 'configuracion';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'direccion', 'telefono', 'email_contacto', 'inscripcion_abierta', 'inscripcion_texto', 'descripcion_pie', 'portada_imagen_id', 'instagram', 'facebook',
        'portada_etiqueta', 'portada_titulo', 'portada_texto', 'portada_boton1_texto', 'portada_boton1_enlace',
        'portada_boton2_texto', 'portada_boton2_enlace', 'portada_boton2_visible',
    ];

    // Fechas que CodeIgniter completa solo al insertar y al actualizar.
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_registro';
    protected $updatedField  = 'fecha_edicion';
}
