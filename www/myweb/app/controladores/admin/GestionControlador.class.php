<?php

namespace app\controladores\admin;

use app\modelos\UsersTable;
use core\Controlador;
use core\Vista;

class GestionControlador extends Controlador{
    
    public function usuariosAccion() {
        $parametros = array();
        if (isset($_SESSION['usuario'])) {
            $parametros['online'] = true;
            $usuario = unserialize($_SESSION['usuario']);
            $parametros['username'] = $usuario->getUsername();
            $parametros['email'] = $usuario->getEmail();
            $parametros['accountType'] = $usuario->getAccountType();
            if($parametros['accountType'] == 0) {
                $users = new UsersTable();
                
                if(isset($_POST["accion"]) && $_POST["accion"] == "loadmore") {
                    $pagina = $_POST['page'];
                    $x = (($pagina * 11) - 11);
                    $registros = $users->getListaUsuarios($x, 11);
                    $parametros['usuarios'] = $registros;
                    Vista::renderTemplate('private/ajax-tabla-gestion-usuarios.html', $parametros);
                } else {
                    $registros = $users->getListaUsuarios(0, 11);
                    $total = $users->totalFilas();
                    $parametros['usuarios'] = $registros;
                    $parametros['pagMax'] = round($total/11);
                    Vista::renderTemplate('private/gestion-usuarios.html', $parametros);
                }                
            } else {
                throw new \Exception("La ruta no existe", 404);
            }
        } else {
             throw new \Exception("La ruta no existe", 404);
        }
    }
}
