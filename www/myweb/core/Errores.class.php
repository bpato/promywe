<?php

namespace core;

/**
 * Clase estática usada para definir el comportamiento de la gestión de errores
 * almacenando los errores en un archivo de log.
 * 
 * @author Brais Pato <bpato@users.noreply.github.com>
 * @version 1.0
 * @abstract
 */
abstract class Errores {
    /**
     * Función que sobreescribe el gestor de errores por defecto provocando una
     * excepción cuando se produzca uno
     * @param type $nivel
     * @param type $mensaje
     * @param type $archivo
     * @param type $linea
     * @throws \ErrorException
     * @access public
     * @static
     */
    public static function gestorErrores($nivel, $mensaje, $archivo, $linea) {
        if (error_reporting() !== 0) {
            # Si el reporte de errores está activo
            throw new \ErrorException($mensaje, 0, $nivel, $archivo, $linea);
        }
    }
    
    /**
     * Función que sobreescribe el manejo de excepciones por defecto almacenando
     * en un archivo log los detalles de los errores producidos.
     * @param type $exception
     * @access public
     * @static
     */
    public static function gestorExcepciones($exception) {
        
        $msg = sprintf("%s:%s. MENSAJE: %s. ORIGEN: %s en la linea %s",
                get_class($exception),
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine());
        self::guardarLog($msg, ROOT."logs/error_{fecha}.log");
        
        $codigo = $exception->getCode();
        if($codigo != 404){
            $codigo = 500;
        }
        http_response_code($codigo);
        $parametros = array();
        if (isset($_SESSION['usuario'])) {
            $parametros['online'] = true;
            $usuario = unserialize($_SESSION['usuario']);
            $parametros['username'] = $usuario->getUsername();
            $parametros['email'] = $usuario->getEmail();
            $parametros['accountType'] = $usuario->getAccountType();
        }
        Vista::renderTemplate("error/".$codigo.".html", $parametros);
    }
    
    /**
     * Función para incluir un mensaje en un archivo log.
     * Para incluir la fecha en el nombre del archivo agregar el comodin {fecha}
     * @param string $mensaje Mensaje que se incluirá
     * @param string $archivo Ruta al archivo de logs
     * @access public
     * @static
     */
    public static function guardarLog($mensaje, $archivo) {
        //ini_set('log_errors', 1);
        
        $archivo = \str_replace("{fecha}", \date('Y-m-d'), $archivo);
        
        # Establecemos el archivo de logs
        ini_set('error_log', $archivo);
        error_log($mensaje);
    }
}

