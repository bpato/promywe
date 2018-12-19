<?php

namespace app\controladores;

use app\modelos\UsersTable;
use app\modelos\clases\Usuario;
use core\Controlador;
use core\Vista;

/**
 * Clase Controlador que maneja la pagina principal.
 */
class OpcionesControlador extends Controlador {
    
    /**
     * Función que limpia los campos introducidos para evitar ataques XSS
     * @param type $text
     * @return type
     */
    private function limpiarInputForm($text) {
        $text = strip_tags($text);
        return $text;
    }
    
    /**
     * Eliminamos espacios del nombre de usuario
     * @param type $text
     * @return type
     */
    private function limpiarEspacios($text) {
        $text = $this->limpiarInputForm($text);
        $text = str_replace(" ", "", $text);
        return $text;
    }
    
    /**
     * Capitalizamos los strings
     * @param type $text
     * @return type
     */
    private function limpiarEspaciosUC($text) {
        $text = $this->limpiarEspacios($text);
        $text = ucfirst(strtolower($text));
        return $text;
    }
    
    public function guardarAccion() {
        if (isset($_SESSION['usuario']) 
                && isset($_POST['optionsButton'])) {
            $usuario = unserialize($_SESSION['usuario']);
            $id = $usuario->getId();
            
            $users = new UsersTable();
            
            if (isset($_POST['optsUsername'])) {
                $un = $this->limpiarEspacios($_POST['optsUsername']);
                $users->cambiarUsername($id, $un);
            } else if (isset($_POST['optsFirstname'])) {
                $fn = $this->limpiarEspaciosUC($_POST['optsFirstname']);
                $ln = $this->limpiarEspaciosUC($_POST['optsLastname']);
                $users->cambiarName($id, $fn, $ln);
            } else if (isset($_POST['optsEmail'])) {
                $em = $this->limpiarEspacios($_POST['optsEmail']);
                $users->cambiarEmail($id, $em);
            } else if (isset ($_POST['optsPasswordOld'])) {
                $pw = $_POST['optsPasswordOld'];
                $pw1 = $this->limpiarInputForm($_POST['optsPassword1']);
                $pw2 = $this->limpiarInputForm($_POST['optsPassword2']);
                $users->cambiarPassword($id, $pw, $pw1, $pw2);
            }
            $usuario = new Usuario($users->getDatosUsuario($id));
            $_SESSION['usuario'] = serialize($usuario);
            header('Location: /TESTLAMPP/myweb/opciones');
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
            $parametros['usuario'] = $usuario->getAll();
            
            Vista::renderTemplate('private/opciones.html', $parametros);
        } else {
            throw new \Exception("La ruta no existe", 404);
        }
        
    }
}
