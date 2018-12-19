<?php

namespace core;

/**
 * Clase que gestionara la elección de controlador y sus acciones a traves de los
 * parametros pasados en la url
 * 
 * @author Brais Pato <bpato@users.noreply.github.com>
 * @version 1.0
 */

class Enrutador {
    
    /**
     * Array de expresiones regulares de las rutas almacenadas.
     * @var type
     * @access protected
     */
    protected $rutas = array();
    
    /**
     * Array de parametros pasados por url almacenados.
     * @var type
     * @access protected
     */
    protected $parametros = array();
    
    /**
     * Función que devuelve las rutas
     * @return type
     */
    public function getRutas() {
        return $this->rutas;
    }
    
    /**
     * Función que devuelve los parametros almacenados
     * @return type
     */
    public function getParametros() {
        return $this->parametros;
    }
    
    /**
     * Añade un formato de ruta y genera su expresión regular para compararla.
     * @param string $ruta  Formato del path
     * @param array $parametros Formato de los parametros del controlador
     */
    public function agregarRuta($ruta, $parametros = array()) {        
        $patrones = array(
            '/\//',                     # / -> \/
            '/\{([a-z]+)\}/',           # {abc} -> (?P<abc>[a-z-]+)
            '/\{([a-zA-Z]+):([^\}]+)\}/'   # {abc:def} -> (?P<abc>def)
        );
        $sustituciones = array(
            '\\/',
            '(?P<$1>[a-z-]+)',
            '(?P<$1>$2)'
        );
        
        $rutaRegExp = "/^".preg_replace($patrones, $sustituciones, $ruta)."$/i";
        $this->rutas[$rutaRegExp] = $parametros;
    }
    
    
    /**
     * Función que traduce el path de la url en un controlador y una acción y los ejecuta
     * @param type $url
     * @throws \Exception
     */
    public function resuelve($url) {
        $url = preg_replace('/\/$/', "", $url);
        $url = $this->eliminarQuerysUrl($url);
        $url = preg_replace('/\/$/', "", $url);
        if ($this->coincide($url)) {
            # Recuperamos el controlador indicado en la url junto el espacio de 
            # nombres donde se localiza
            $controlador = $this->getNamespace() . $this->getControlador();
            
            if (class_exists($controlador)) {
                $obj = new $controlador($this->getParametros());
                $accion = $this->getAccion();
                if (is_callable([$obj, $accion])) {
                    $obj->$accion();
                } else {
                    throw new \Exception("Método ".$accion." no pertenece a ".$controlador, 404);
                }                
            } else {
                throw new \Exception("Controlador de clase ".$controlador." no encontrado", 404);
            }
        } else {
            throw new \Exception("La ruta no coincide", 404);
        }
    }
    
    /**
     * Dibide la url las querys que la acompañan
     * @param string $url Url completa path y querys
     * @return string
     * @access protected
     */
    protected function eliminarQuerysUrl($url) {
        if (!empty($url)) {
            # Dibide en dos a partir del primer caracter &
            $partes = explode("&", $url, 2);
            if (strpos($partes[0], "=") === false) {
                $url = $partes[0];
            } else {
                $url = '';
            }
        }
        return $url;
    }
    
    /**
     * Función que busca coincidencias de la url en las rutas almacenadas y
     * obtiene los parametros del controlador
     * @param type $url
     * @return boolean
     */
    protected function coincide($url) {
        foreach ($this->rutas as $ruta => $parametros) {
            if(\preg_match($ruta, $url, $coinciden)) {
                # Si la url coincide con la expresión regular
                # El array tiene formato con indices numericos y de string
                # el string descriptivo que se añade al añadir la ruta
                foreach ($coinciden as $key => $coincidencia) {
                    if(is_string($key)) {
                        $parametros[$key] = $coincidencia;
                    }
                }
                # Almacenamos los parametros obtenidos de la url
                $this->parametros = $parametros;
                return true;
            }
        }
        return false;
    }
    
    /**
     * Función que genera el formato correcto del controlador a partir de la 
     * información almacenada en los parametros
     * @return type
     * @access protected
     */
    protected function getControlador() {
        return $this->transformarStudlyCaps($this->parametros['controlador']).'Controlador';
    }

    /**
     * Funcion que genera el espacio de nombres a partir de la información 
     * suministrada en los parametros
     * @return string
     * @access protected
     */
    protected function getNamespace() {
        $ns = CONTROL_NAMESPACE;
        if (array_key_exists('namespace', $this->getParametros())) {
            # Si se ha definido un namespace en los parametros
            $ns .= $this->parametros['namespace'].'\\';
        }
        return $ns;
    }
    
    /**
     * Funcion que obtiene la informacion de la accion a realizar a partir 
     * de la informacion pasada por los parametros
     * @return type
     * @access protected
     */
    protected function getAccion() {
        return $this->transformarCamelCase($this->parametros['accion']);
    }

    /**
     * Convierte un string a formato "StudlyCaps" donde la primera letra de cada
     * palabra es mayuscula. Admite strings divididos mediante guión.
     * @param type $string
     * @access protected
     */
    protected function transformarStudlyCaps($string) {
        $string = str_replace("-", " ", $string);
        $string = ucwords($string);
        return str_replace(" ", "", $string);
    }
    
    /**
     * Convierte un string a formato "camelCase" donde la primera letra de cada
     * palabra sera mayuscula salvo la primera.
     * @param type $string
     * @access protected
     */
    protected function transformarCamelCase($string) {
        return lcfirst($this->transformarStudlyCaps($string));
    }
    
}

