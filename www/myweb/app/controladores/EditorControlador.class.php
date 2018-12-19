<?php

namespace app\controladores;
use core\Controlador;
use core\Vista;

/**
 * Clase Controlador que maneja la pagina principal.
 */
class EditorControlador extends Controlador {
    public function indexAccion() {
        $parametros = array();
       
        if (isset($_SESSION['usuario'])) {
            $parametros['online'] = true;
            $usuario = unserialize($_SESSION['usuario']);
            $parametros['username'] = $usuario->getUsername();
            $parametros['email'] = $usuario->getEmail();
            $parametros['accountType'] = $usuario->getAccountType();
            $parametros['id'] = $usuario->getId();
            if($parametros['accountType'] <= 1) {
                Vista::renderTemplate('private/editor.html', $parametros);
            } else {
                throw new \Exception("La ruta no existe", 404);
            }
        } else {
             throw new \Exception("La ruta no existe", 404);
        }
    }
}
