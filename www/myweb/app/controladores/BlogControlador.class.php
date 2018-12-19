<?php

namespace app\controladores;
use core\Controlador;
use core\Vista;

/**
 * Clase Controlador que maneja la pagina principal.
 */
class BlogControlador extends Controlador {
        public function verAccion() {
            $parametros = array();
       
            if (isset($_SESSION['usuario']) &&
                isset($this->parametros_ruta['articuloId'])) {
                $parametros['online'] = true;
                $usuario = unserialize($_SESSION['usuario']);
                $parametros['username'] = $usuario->getUsername();
                $parametros['email'] = $usuario->getEmail();
                $parametros['accountType'] = $usuario->getAccountType();
                $parametros['articuloId'] = $this->parametros_ruta['articuloId'];
                Vista::renderTemplate('private/articulo.html', $parametros);
            } else {
                 throw new \Exception("La ruta no existe", 404);
            }
        }

        public function indexAccion() {
        $parametros = array();
       
        if (isset($_SESSION['usuario'])) {
            $parametros['online'] = true;
            $usuario = unserialize($_SESSION['usuario']);
            $parametros['username'] = $usuario->getUsername();
            $parametros['email'] = $usuario->getEmail();
            $parametros['accountType'] = $usuario->getAccountType();
            Vista::renderTemplate('private/blog.html', $parametros);
        } else {
             throw new \Exception("La ruta no existe", 404);
        }
    }
}
