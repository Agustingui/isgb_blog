<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Panel: Papelera (solo administradores).
 *
 * Lo que se borra desde los listados del panel no desaparece: queda con fecha
 * en "fecha_borrado" (borrado lógico) y se ve acá, donde se puede restaurar o
 * borrar definitivamente. Al borrar definitivamente actúan las claves foráneas
 * (por ejemplo, donde se usaba un archivo queda "sin imagen").
 */
class AdminPapelera extends BaseController
{
    // Tablas con Papelera: tabla => [título, columna con el nombre, listado del panel].
    private $tipos = [
        'noticias' => ['Noticias', 'titulo', 'admin/noticias'],
        'posteos'  => ['Posteos', 'titulo', 'admin/posteos'],
        'carreras' => ['Carreras', 'nombre', 'admin/carreras'],
        'media'    => ['Archivos', 'alt', 'admin/media'],
        'usuarios' => ['Usuarios', 'nombre', 'admin/usuarios'],
    ];

    public function index($tipo)
    {
        if (! isset($this->tipos[$tipo])) {
            throw PageNotFoundException::forPageNotFound();
        }

        [$titulo, $columna, $listado] = $this->tipos[$tipo];

        $filas = $this->db->table($tipo)
            ->where('fecha_borrado IS NOT NULL', null, false)
            ->orderBy('fecha_borrado', 'DESC')
            ->get()
            ->getResultArray();

        return $this->vistaAdmin('admin/papelera', [
            'tipo'    => $tipo,
            'filas'   => $filas,
            'columna' => $columna,
            'listado' => $listado,
            'titulo'  => 'Papelera: ' . $titulo,
            'menu'    => $tipo,
        ]);
    }

    public function restaurar($tipo, $id)
    {
        $fila = $this->buscarBorrado($tipo, $id);

        if ($fila === null) {
            return $this->respuestaAccion(false, 'No se encontró en la Papelera.', 'admin/papelera/' . $tipo);
        }

        $this->db->table($tipo)->where('id', $id)->update(['fecha_borrado' => null]);

        return $this->respuestaAccion(true, 'Se restauró "' . $fila[$this->tipos[$tipo][1]] . '".', 'admin/papelera/' . $tipo);
    }

    public function eliminar($tipo, $id)
    {
        $fila = $this->buscarBorrado($tipo, $id);

        if ($fila === null) {
            return $this->respuestaAccion(false, 'No se encontró en la Papelera.', 'admin/papelera/' . $tipo);
        }

        $this->db->table($tipo)->where('id', $id)->delete();

        // Los archivos de la biblioteca también se borran del disco.
        if ($tipo === 'media') {
            $this->borrarArchivo($fila['archivo']);
        }

        return $this->respuestaAccion(true, 'Se borró definitivamente.', 'admin/papelera/' . $tipo);
    }

    /**
     * Fila que está en la Papelera, o null si el tipo no existe o la fila no está borrada.
     */
    private function buscarBorrado($tipo, $id)
    {
        if (! isset($this->tipos[$tipo])) {
            return null;
        }

        return $this->db->table($tipo)
            ->where('id', (int) $id)
            ->where('fecha_borrado IS NOT NULL', null, false)
            ->get()
            ->getRowArray();
    }
}
