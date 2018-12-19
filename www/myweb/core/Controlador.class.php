<?php

namespace core;

/**
 * Clase base que define la estructura de la ejecución de las acciones de los controladores
 * 
 * @author Brais Pato <bpato@users.noreply.github.com>
 * @version 1.0
 */

class Controlador {
    
    /**
     *  Array de parametros pasados por url a cuya key se le asigna el nombredel grupo de la expresión regular.
     * @var string
     * @access protected
     */
    protected $parametros_ruta = array();

    public function __construct($parametros) {
        $this->parametros_ruta = $parametros;
    }
    
    public function __call($nombre, $argumentos) {
        # Las acciones tendrán el formato nombreAccion
        $metodo = $nombre . "Accion";
        
        if (method_exists($this, $metodo) && $this->before() !== false) {
            call_user_func([$this, $metodo], $argumentos);
            $this->after();
        } else {
            throw new \Exception("Método ".$metodo." no pertenece a ". get_class($this));
        }
    }
    
    /**
     * Función que se ejecuta antes de la llamada a la acción, si devuelve un
     * resultado false aborta la llamada
     */
    protected function before() {
        
    }
    
    /**
     * Función que se ejecuta despues de la llamada a la acción.
     */
    protected function after() {
        
    }
}
