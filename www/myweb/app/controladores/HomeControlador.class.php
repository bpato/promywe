<?php

namespace app\controladores;
use core\Controlador;
use core\Vista;

/**
 * Clase Controlador de ejemplo que maneja la pagina principal.
 */
class HomeControlador extends Controlador {

    public function indexAccion() {
        Vista::render('info.php');
    }
}
