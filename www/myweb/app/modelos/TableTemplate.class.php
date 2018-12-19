<?php

namespace app\modelos;

/**
 * Plantilla generica de utilidades para trabajar con las tablas de la base de datos 
 * 
 * @author Brais Pato <bpato@users.noreply.github.com>
 * @version 1.0
 */

abstract class TableTemplate extends \core\Modelo {
    
    /**
     * Variable que contendrá el nombre de la tabla de la base de datos,
     * se declarará en las clases hijas
     * @var string
     * @access protected
     */
    protected $nombreTabla;
    
    /**
     * Array de valores con el nombre de los campos caracteristicos de una tabla
     * @var Array de strings
     * @access protected
     */
    protected $campos = null;
    
    /**
     * Nombre del campo que actua como clave primaria
     * @var string
     * @access protected
     */
    protected $primaryKey = null;
    
    public function __construct($nomTabla) {
        $this->nombreTabla = $nomTabla;
        $this->getCampos();
    }

    /**
     * Función que obtiene la estructura de una tabla de la base de datos
     * @access private
     */
    private function getCampos() {
        
        if(is_null($this->campos)) {
        
            $this->campos = array();
            $sql = "DESC ".$this->nombreTabla;
            $resultado = $this->getAllRow($sql);

            foreach ($resultado as $row) {
                $this->campos[] = $row['Field'];

                if($row['Key'] == 'PRI') {
                    $this->primaryKey = $row['Field'];
                }
            }
        } 
    }
    
    /**
     * Función que inserta un registro de datos en la base de datos
     * @param array $valores Array cuyos indices son los campos de la tabla 
     * @return boolean
     * @access public
     */
    public function insertar($valores) {
        $lista_campos = '';
        $lista_campos_bind = '';
        $datos = array();
        
        foreach ($valores as $campo => $valor) {
            if(in_array($campo, $this->campos)) {
                # si existe el campo en la tabla
                # generamos la sentencia sql y el array de datos
                $lista_campos_bind .= ":".$campo.",";
                $datos[$campo] = $valor;
            }
        }
        
        $lista_campos_bind = rtrim($lista_campos_bind,",");
        $lista_campos = str_replace(":", "", $lista_campos_bind);
        
        $sql = "INSERT INTO ".$this->nombreTabla;
        $sql .= " (".$lista_campos.") ";
        $sql .= "VALUES (".$lista_campos_bind.")";
        
        if ($this->exec($sql, $datos)) {
            return $this->getInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Función que obtiene un registro de datos de la base de datos en función de
     * la clave primaria introducida.
     * @param type $valorPK valor de la clave primaria según la cual se obtendrán los
     * datos.
     * @return array
     */
    public function obtener($valorPK) {
        $datos = array($this->primaryKey => $valorPK);
        
        $sql = "SELECT * FROM ".$this->nombreTabla;
        $sql .= " WHERE ".$this->primaryKey." = :".$this->primaryKey;
        return $this->getOneRow($sql, $datos);
    }

    /**
     * Función que actualiza un registro de la base de datos en función de la 
     * clave primaria
     * @param type $valores Arrayde valores cuyos indices son campos de la tabla
     * @return type
     */
    public function actualizar($valores) {
        $lista_campos_bind = '';
        $campo_where = '';
        $datos = array();
        
        foreach ($valores as $campo => $valor) {
            if(in_array($campo, $this->campos)) {
                # si existe el campo en la tabla
                if($campo == $this->primaryKey) {
                    # si el campo es PK de la tabla se usa como punto de coincidencia
                    $campo_where = $campo." = :".$campo;
                } else {
                    # generamos las sentencia sql
                    $lista_campos_bind .= $campo." = :".$campo.",";
                }
                $datos[$campo] = $valor;
            }
        }
        
        $lista_campos_bind = rtrim($lista_campos_bind, ",");
        $sql = "UPDATE ".$this->nombreTabla;
        $sql .= " SET ".$lista_campos_bind;
        $sql .= " WHERE ".$campo_where;
        
        return $this->exec($sql,$datos);
    }
    
    /**
     * Elimina un registro de la base de datos en función de su clave primaria
     * @param type $valorPK Valor de la clave primaria del registro a eliminar
     * @return type
     */
    public function borrar($valorPK) {
        $campo_where = '';
        
        $datos = array($this->primaryKey => $valorPK);
        $where = $this->primaryKey.' =: '. $this->primaryKey;
        
        $sql = "DELTE FROM ".$this->nombreTabla;
        $sql .= " WHERE ".$campo_where;
        return $this->exec($sql, $datos);
    }
    
    /**
     * Función que contabiliza el número total de registros de la tabla
     * @return type
     */
    public function totalFilas() {
        $sql = "SELECT count(*) FROM ".$this->nombreTabla;
        return $this->getOneCol($sql);
    }
    
    /**
     * Función que obtiene una porción de un conjunto de registros 
     * @param type $offset Posición desde donde se comenzará a obtener registros
     * @param type $limit Número de registros que se obtendrán
     * @param array $valores Array de valores cuyos indices son campos de la tabla
     * para especificar los datos obtenidos-
     * @return type
     */
    public function obtenerPaginado($offset, $limit, $valores=null) {
        $sql = "SELECT * FROM ".$this->nombreTabla;
        if(is_null($valores)) {
            $sql .= " LIMIT ".$offset.",".$limit;
            return $this->getAllRow($sql);
        } else {
            $campo_where = '';
            $datos = array();
            foreach ($valores as $campo => $valor) {
                if(in_array($campo, $this->campos)) {
                    if(count($datos)>0) {
                        $campo_where .= " AND ";
                    }
                    $campo_where .= $campo." = :".$campo;
                    $datos[$campo] = $valor;
                }
            }
            $sql = " WHERE ".$campo_where." LIMIT ".$offset.",".$limit;
            return $this->getAllRow($sql, $datos);
        }
    }
} 

