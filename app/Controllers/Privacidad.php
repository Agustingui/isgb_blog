<?php

namespace App\Controllers;

/**
 * Página de política de privacidad.
 */
class Privacidad extends BaseController
{
    public function index()
    {
        return $this->vistaPublica('publico/privacidad', [
            'titulo'      => 'Política de privacidad',
            'descripcion' => 'Cómo cuidamos tus datos en el sitio del Instituto Superior Gaspar Benavento.',
        ]);
    }
}
