<?php

namespace core;

/**
 * Clase estática usada para almacenar las variables de configuración.
 * La configuración la obtendrá del archivo config.ini
 * 
 * @author Brais Pato <bpato@users.noreply.github.com>
 * @version 1.0
 * @abstract
 */
abstract class Configuracion {
    
    /**
     * Tipo de driver usado por la base de datos.
     * @var string
     * @access public
     * @static 
     */
    public static $DB_DRIVER;

    /**
     * Ruta de la base de datos sqlite.
     * @var string 
     * @access public
     * @static
     */
    public static $DB_PATH;
    
    /**
     * IP del host donde se aloja la base de datos.
     * @var string 
     * @access public
     * @static
     */
    public static $DB_HOST;
    
    /**
     * Puerto de escucha de la base de datos.
     * @var string 
     * @access public
     * @static
     */
    public static $DB_PORT;

    /**
     * Path del socket linux usado por la base de datos.
     * @var string 
     * @access public
     * @static
     */
    public static $DB_SOCKET;

    /**
     * Nombre de la base de datos a la que se conectará.
     * @var string 
     * @access public
     * @static
     */
    public static $DB_NAME;

    /**
     * Codificación de caracteres que se usará en la base de datos.
     * @var string 
     * @access public
     * @static
     */
    public static $DB_CHARSET;

    /**
     * Usuario que realizará la conexión a la base datos.
     * @var string 
     * @access public
     * @static
     */
    public static $DB_USER;

    /**
     * Contraseña del usuario que se conectará a la base de datos.
     * @var string 
     * @access public
     * @static
     */
    public static $DB_PASS;
    
    /**
     * Función que obtendrá los datos del archivo de 
     * configuración pasado como parametro.
     * Por defecto se situa en la ruta "app/config.ini".
     * @param string $file Archivo .ini con la configuración.
     * @access public
     * @static
     */
    public static function cargar($file = ROOT."app/config.ini") {
        $settings = parse_ini_file($file, true);
        extract($settings['database'], EXTR_SKIP);
        self::$DB_DRIVER = $driver;
        switch (self::$DB_DRIVER) {
            case "mysql":
                if (isset($host)) {
                    self::$DB_HOST = $host;
                    self::$DB_PORT = $port;
                } else {
                    self::$DB_SOCKET = $socket;
                }
                
                self::$DB_NAME = $database;
                self::$DB_CHARSET = $charset;
                
                self::$DB_USER = $user;
                self::$DB_PASS = $pass;
                break;
            case "sqlite":
            case "sqlite2":
                self::$DB_PATH = $path;
                break;
        }
    }
}