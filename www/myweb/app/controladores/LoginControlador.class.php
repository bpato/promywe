<?php

namespace app\controladores;

use app\modelos\UsersTable;
use app\modelos\clases\Usuario;
use core\Controlador;
use core\Vista;

/**
 * Clase Controlador que maneja la pagina login.
 */
class LoginControlador extends Controlador {
    
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
    
    public function indexAccion() {
        $parametros = array();
        
        if(isset($_POST['loginButton'])) {
            $users = new UsersTable();
            
            $em = $_POST['lgnEmail'];
            $pw = $_POST['lgnPassword'];
            
            if ($valorPK = $users->login($em, $pw)) {
                $usuario = new Usuario($users->getDatosUsuario($valorPK));
                $_SESSION['usuario'] = serialize($usuario);
                header('Location: /myweb/');
            } else {
                $parametros["errorMsg"]  =  $users->getError();
                Vista::renderTemplate('public/login-registro.html', $parametros);
            }            
        } else if(isset($_POST['registerButton'])) {
            $users = new UsersTable();
            
            $un = $this->limpiarEspacios($_POST['rgsUsername']);
            $fn = $this->limpiarEspaciosUC($_POST['rgsFirstname']);
            $ln = $this->limpiarEspaciosUC($_POST['rgsLastname']);
            $em1 = $this->limpiarEspacios($_POST['rgsEmail1']);
            $em2 = $this->limpiarEspacios($_POST['rgsEmail2']);
            $pw1 = $this->limpiarInputForm($_POST['rgsPassword1']);
            $pw2 = $this->limpiarInputForm($_POST['rgsPassword2']);
            
            if($valorPK = $users->registro($un, $fn, $ln, $em1, $em2, $pw1, $pw2)){
                $usuario = new Usuario($users->getDatosUsuario($valorPK));
                $_SESSION['usuario'] = serialize($usuario);
                header('Location: /myweb/');
            } else {
                $parametros["errorMsg"]  =  $users->getError();
                Vista::renderTemplate('public/login-registro.html', $parametros);
            }
        } else {
            if (isset($_SESSION['usuario'])) {
                $parametros['online'] = true;
            }
            Vista::renderTemplate('public/login-registro.html', $parametros);
        }
    }
}

