<?php

namespace app\controladores;

use core\Controlador;
use core\Vista;

/**
 * Clase Controlador que maneja la pagina login.
 */
class LogoutControlador extends Controlador {
    
    public function indexAccion() {
        session_destroy();
        Vista::renderTemplate('public/home.html');
    }
}

