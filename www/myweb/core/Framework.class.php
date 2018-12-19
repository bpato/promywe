<?php

namespace core;

/**
 * Clase que controla la puesta en marcha y ejecución del entorno
 * @author Brais Pato <bpato@users.noreply.github.com>
 * @version 1.0
 * @abstract
 */

abstract class Framework {
    
    /**
     * Función de arranque del sistema.
     * @access public
     * @static
     */
    public static function run() {
        self::beforeInit();
        self::init();
        self::afterInit();
    }
    
    /**
     * Función principal del iniciodel sistema
     * @access private
     * @static
     */
    private static function init() {
         /* CARGADOR AUTOMATICO DE CLASES */
        \spl_autoload_register(function ($class) {
            $file = ROOT.\str_replace('\\', DS, $class).'.class.php';
            if (\is_readable($file)) {
                require $file;
            }
        });
        
        /* MANEJO DE ERRORES */
        \error_reporting(E_ALL);
        \set_error_handler("core\Errores::gestorErrores");
        \set_exception_handler("core\Errores::gestorExcepciones");
        
        session_start();
    }

    /**
     * Función que ejecuta las acciones previas al inicio del sistema
     * @access private
     * @static
     */
    private static function beforeInit() {
        /* DEFINIMOS CONSTANTES GLOBALES */
        
        /**
         * Constante que contiene el separador usado por los directorios
         */
        define("DS", DIRECTORY_SEPARATOR);
        /**
         * Directorio de la raiz de la aplicación
         */
        define("ROOT", \getcwd().DS);
        
        /**
         * PATH a la carpeta publica de la aplicación
         */
        define("PUBLIC_PATH", ROOT."public".DS);
        /**
         * PATH a la carpeta utilizada para la subida de archivos
         */
        define("UPLOADS_PATH", PUBLIC_PATH."uploads".DS);
        
        /**
         * PATH al directorio donde se almacenan las vistas
         */
        define("VISTAS_PATH", ROOT."app/vistas".DS);
        
        /**
        * Espacio de nombres por defecto donde se localizan los controladores
        */
        define("CONTROL_NAMESPACE", '\app\controladores\\');
    }
    
    /**
     * Función que ejecuta las acciones posteriores al inicio del sistema
     * Añadimos todas las opciones de enrutamiento utilizadas
     * @access private
     * @static
     */
    private static function afterInit() {        
        $router = new Enrutador();
        $router->agregarRuta('', ['controlador'=>'home','accion'=>'index']);
        $router->agregarRuta("info", ['controlador'=>'home','accion'=>'info']);
        
        $router->agregarRuta("{controlador}", ['accion'=>'index']);
        //$router->agregarRuta("login", ['controlador'=>'login', 'accion'=>'index']);
        //$router->agregarRuta("logout", ['controlador'=>'logout', 'accion'=>'index']);
        
        $router->agregarRuta("{controlador}/{accion}");
        $router->agregarRuta('{controlador}/{articuloId:\d+}',['accion'=>'ver']);
        $router->agregarRuta("admin/{controlador}/{accion}", ['namespace' => 'admin']);
        
        $router->resuelve($_SERVER['QUERY_STRING']);
    }
}

