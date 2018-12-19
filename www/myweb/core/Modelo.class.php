<?php

namespace core;

use PDO, PDOException;

/**
 * Clase Modelo que gestiona los privilegios de acceso y la forma de acceder a 
 * los datos.
 * @author Brais Pato <bpato@users.noreply.github.com>
 * @version 1.0
 * @abstract 
 */
abstract class Modelo {
    
    /**
     * Objeto almacena la conexión con la base de datos
     * @var PDO
     * @access private
     */
    private static $db = null;

    /**
     * Id del ultimo registro insertado
     * @var string Valor del Id
     * @access protected
     */
    protected static $insertId = null;
    
    /**
     * Función que establece una conexión con la base de datos siempre que no
     * haya una ya establecia.
     * @staticvar type $db Variable estatica que mantendrála conexión entre las
     * diferentes clases.
     * @return PDO
     * @access protected
     * @static
     */
    protected static function getDB() {
        
        if (is_null(self::$db)) {
            $dsn;
            $opciones;
            Configuracion::cargar();
            switch (Configuracion::$DB_DRIVER) {
                case "mysql":
                    $dsn = Configuracion::$DB_DRIVER.':';
                    if(isset(Configuracion::$DB_HOST)) {
                        $dsn .= 'host='.Configuracion::$DB_DRIVER.';';
                        $dsn .= 'port='.Configuracion::$DB_PORT.';';
                    } else {
                        $dsn .= 'unix_socket='.Configuracion::$DB_SOCKET.';';
                    }
                    $dsn .= 'dbname='.Configuracion::$DB_NAME.';';
                    $dsn .= 'charset='.Configuracion::$DB_CHARSET;
                    break;
                /* DEPRECATED
                case "sqlite":
                case "sqlite2":
                    $dsn = Configuracion::$DB_DRIVER.':';
                    $dsn .= Configuracion::$DB_PATH;
                    $opciones = array(PDO::ATTR_PERSISTENT => true);
                    break;*/
            }
            
            try {
                self::$db = new PDO($dsn, Configuracion::$DB_USER, Configuracion::$DB_PASS);
                self::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                # Establecemos el uso de excepciones para el manejo de errores
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $ex) {
                throw new \ErrorException($ex->getMessage(),
                        0, $ex->getCode(), $ex->getFile(), $ex->getLine());
            }
        } 
        
        return self::$db;
    }
    #TODO: Mejorar las sentencias preparadas para ejecutar multiples veces una misma
    # sentencia preparada
    
    /**
     * Función que ejecuta una acción en la base de datos
     * @param string $sql Sentencia en lenguaje SQL de la acción.
     * @param array $datos Array que cuyos indices son el nombre de los parametros
     * a substituir
     * @return Boolean;
     * @throws \ErrorException
     * @access protected
     */
    protected function exec($sql, $datos = null) {
        try {
            self::$db = static::getDB();
            self::$db->beginTransaction();
            $query = self::$db->prepare($sql);
            if (!is_null($datos) && is_array($datos)) {
                $this->autoBindParam($query, $datos);
            }
            
            if($query->execute()) {
                self::$insertId = self::$db->lastInsertId();
                self::$db->commit();
                return true;
            } else {
                self::$db->rollBack();
                return false;
            }
        } catch (PDOException $ex) {
            throw new \ErrorException($ex->getMessage(),
                        0, $ex->getCode(), $ex->getFile(), $ex->getLine());
        }
    }
        
    /**
     * Función que solicita una consulta a la base de datos
     * @param string $sql Sentencia en lenguaje SQL de la acción.
     * @param array $datos Array que cuyos indices son el nombre de los parametros
     * a substituir
     * @return PDOStatement;
     * @throws \ErrorException
     * @access private
     */
    private function query($sql, $datos=null) {
        try {
            self::$db = static::getDB();
            
            $query = self::$db->prepare($sql);
            if (!is_null($datos) && is_array($datos)) {
                $this->autoBindParam($query, $datos);
            }
            return ($query->execute())?$query:false;
        } catch (Exception $ex) {
            throw new \ErrorException($ex->getMessage(),
                        0, $ex->getCode(), $ex->getFile(), $ex->getLine());
        }
    }
    
    /**
     * Función auxiliar para automatizar el paso de parametros a una consulta 
     * preparada
     * @param PDOStatement $query Sentencia preparada.
     * @param array $datos array de datos cuyos indices son el nombre de los parametros
     * a substituir
     * @access private
     */
    private function autoBindParam(&$query, $datos) {
        foreach ($datos as $key => &$value) {
            $query->bindParam($key, $value);
        }
    }
    
    
    /**
     * Función que devuelve el valor de una columna en una consulta SQL
     * @param string $sql Sentencia en lenguaje SQL de la acción.
     * @param int $columna Posición de la columna que retornara su valor
     * @param array $datos array de datos cuyos indices son el nombre de los parametros
     * a substituir
     * @return type
     * @access protected
     */
    protected function getOneCol($sql, $datos=null, $columna=0) {
        $result = $this->query($sql, $datos);
        return $result->fetchColumn($columna);
    }
    
    /**
     * Función que devulve el valor de un registrode en una consulta SQL.
     * @param string $sql Sentencia en lenguaje SQL de la acción.
     * @param array $datos array de datos cuyos indices son el nombre de los parametros
     * a substituir
     * @return array Array asociativo
     * @access protected
     */
    protected function getOneRow($sql, $datos=null) {
        $result = $this->query($sql, $datos);
        return $result->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Función que retorna una lista con todos los valores de una columna de 
     * una consulta sql
     * @param string $sql Sentencia en lenguaje SQL de la acción.
     * @param int $columna Posición de la columna que retornara su valor
     * @param array $datos array de datos cuyos indices son el nombre de los 
     * parametros a substituir
     * @return array
     * @access protected
     */
    protected function getAllCol($sql, $columna = 0, $datos=null){
        $result = $this->query($sql, $datos);
        $list = array();
        while ($list[] = $result->fetchColumn($columna)) {}
        return $list;
    }
    
    /**
     * Función que retorna una columna con todos los registros obtenidos 
     * en una conslta SQL
     * @param string $sql Sentencia en lenguaje SQL de la acción.
     * @param array $datos array de datos cuyos indices son el nombre de los parametros
     * a substituir
     * @return type
     * @access protected
     */
    protected function getAllRow($sql, $datos=null) {
        $result = $this->query($sql, $datos);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Devuelve el id de la ultima inserción de la base de datos.
     * @return type
     */
    protected function getInsertId() {
        return self::$insertId;
    }
    
    /**
     * Retorna el codigo del ultimo error
     * @return type
     */
    protected function errCode() {
        self::$db = static::getDB();
        return self::$db->errorCode();
    }
    
    /**
     * Retorna el mensaje del ultimo error
     * @return type
     */
    protected function errMsg() {
        self::$db = static::getDB();
        return self::$db->errorInfo();
    }
    
}

