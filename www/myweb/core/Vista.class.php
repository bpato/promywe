<?php
namespace core;

/**
 * Clase que inicializa twig para su utilización en la visualización de las páginas
 * 
 * @author Brais Pato <bpato@users.noreply.github.com>
 * @version 1.0
 */
abstract class Vista {
    
    /**
     * Instancia del template engine twig
     * @var twig
     */
    private static $twig = null;
    
    /**
     * Visualiza vistas de paginas hechas en php
     * 
     * @param string $vista Archivo que se visualizará
     * @param string $argumentos Argumentos pasados a la vista
     * @throws \Exception
     */
    public static function render($vista, $argumentos = array()) {
        extract($argumentos, EXTR_SKIP);
        
        $archivoVista = VISTAS_PATH.$vista;
        
        if(is_readable($archivoVista)) {
            require $archivoVista;
        } else {
            throw new \Exception("Vista ".$archivoVista." no se encontró");
        }
    }
    
    /**
     * Visualiza plantillas de twig
     * 
     * @param type $template Archivo de plantilla twig
     * @param type $argumentos Argumentos pasados a la vista
     */
    public static function renderTemplate($template, $argumentos = array()) {
        
        if(self::$twig === null) {
            $loader = new \Twig_Loader_Filesystem(VISTAS_PATH);
            self::$twig = new \Twig_Environment($loader);
        }
        
        echo self::$twig->render($template, $argumentos);
    }
}

