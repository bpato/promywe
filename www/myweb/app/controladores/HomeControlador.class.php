<?php

namespace app\controladores;
use core\Controlador;
use core\Vista;

/**
 * Clase Controlador que maneja la pagina principal.
 */
class HomeControlador extends Controlador {
    public function indexAccion() {
        $parametros = array();
        
        if (isset($_SESSION['usuario'])) {
            $parametros['online'] = true;
            $usuario = unserialize($_SESSION['usuario']);
            $parametros['username'] = $usuario->getUsername();
            $parametros['email'] = $usuario->getEmail();
            $parametros['accountType'] = $usuario->getAccountType();
        }
        
        Vista::renderTemplate('public/home.html', $parametros);
    }
    
    public function infoAccion() {
        Vista::render('test/info.php');
    }
}
