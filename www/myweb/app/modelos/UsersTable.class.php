<?php

namespace app\modelos;

class UsersTable extends TableTemplate {
    
    private $error = null;
    
    public function __construct() {
        /*
         * id
         * username
         * firstName
         * lastName
         * email
         * password
         * singUpDate
         * lastLogin
         * profilePic
         */
        parent::__construct("mw_users");
    }
    
    public function getDatosUsuario($valorPK) {
        $datos = $this->obtener($valorPK);
        unset($datos['password']);
        return $datos;
    }
    
    /**
     * Función que valida la longitud de una cadena entre un valor minimo y maximo
     * @param string $string Cadena a validar
     * @param int $min Minimo número de caracteres
     * @param int $max Maximo numero de caracteres
     * @return boolean
     */
    private function validarLongitudString($string, $min = 0, $max=PHP_INT_MAX){
        $longitud = strlen($string);
        return ($longitud >= $min && $longitud < $max)?TRUE:FALSE;
    }
    
    /**
     * Función que valida la duplicidad del nombre de usuario
     * @param string $un nombre de usuario que se validará
     */
    private function validarUsername($un) {
        // Validamos tamaño
        if(!$this->validarLongitudString($un, 3, 25)) {
            $this->error = clases\Textos::$unCharsLength;
            return false;
        }
        
        $sql = 'SELECT count(*) FROM '.$this->nombreTabla;
        $sql .= ' WHERE username = :username';
        if($this->getOneCol($sql, [ 'username' => $un]) != 0) {
            $this->error = clases\Textos::$unTaken;
            return false;
        }
        return true;
    }
    
    private function validarFirstname($fn) {
        if(!$this->validarLongitudString($fn, 3, 25)) {
            $this->error = clases\Textos::$fnCharsLength;
            return false;
        }
        return true;
    }
    
    private function validarLastname($ln) {
        if(!$this->validarLongitudString($ln, 3, 25)) {
            $this->error = clases\Textos::$lnCharsLength;
            return false;
        }
        return true;
    }

    private function validarEmail($em1, $em2) {
        if ($em1 != $em2 ) {
            $this->error = clases\Textos::$emDoNotMatch;
            return false;
        }
        
        if(!filter_var($em1, FILTER_VALIDATE_EMAIL)){
            $this->error = clases\Textos::$emInvalid;
            return false;
        }
        
        $sql = 'SELECT count(*) FROM '.$this->nombreTabla;
        $sql .= ' WHERE email = :email';
        if($this->getOneCol($sql, [ 'email' => $em1]) != 0) {
            $this->error = clases\Textos::$emTaken;
            return false;
        }
        return true;
    }
    
    private function validarPassword($pw1, $pw2){
        if ($pw1 != $pw2) {
            $this->error = clases\Textos::$pwDoNotMatch;
            return false;
        }
        
        if (preg_match('/[^a-zA-Z0-9]/', $pw1)) {
            $this->error = clases\Textos::$pwNotAlphaNum;
            return false;
        }
        
        if(!$this->validarLongitudString($pw1, 5, 30)) {
            $this->error = clases\Textos::$pwCharsLength;
            return false;
        }
        
        return true;
    }

    
    public function cambiarUsername($id, $un) {
        if($this->validarUsername($un)) {
            $valores = array(
                'id' => $id,
                'username' => $un
            );
            return $this->actualizar($valores);
        } else {
            return false;
        }
    }
    
    public function cambiarName($id, $fn, $ln) {
        if($this->validarFirstname($fn) &&
           $this->validarLastname($ln)) {
            $valores = array(
                'id' => $id,
                'firstName' => $fn,
                'lastName' => $ln
            );
            return $this->actualizar($valores);
        } else {
            return false;
        }
    }
    
    public function cambiarEmail($id, $em) {
        if($this->validarEmail($em, $em)) {
            $valores = array(
                'id' => $id,
                'email' => $em
            );
            return $this->actualizar($valores);
        } else {
            return false;
        }
    }
    
    public function cambiarPassword($id, $pw, $pw1, $pw2) {
        $pw = md5($pw);
        $valores = array('id' => $id, 'password' => $pw);
        $sql = 'SELECT count(*) FROM '.$this->nombreTabla;
        $sql .= ' WHERE id = :id AND password = :password';
        if($this->getOneCol($sql, $valores) == 1) {
            if($this->validarPassword($pw1, $pw2)) {
                $valores = array(
                    'id' => $id,
                    'password' => md5($pw1)
                );
                return $this->actualizar($valores);
            } else {
                return false;
            }
        } else {
            $this->error = clases\Textos::$pwDoNotMatch;
            return false;
        }
    }
    
    public function registro($un, $fn, $ln, $em1, $em2, $pw1, $pw2) {        
        if($this->validarUsername($un) &&
           $this->validarFirstname($fn) &&
           $this->validarLastname($ln) &&
           $this->validarEmail($em1, $em2) &&
           $this->validarPassword($pw1, $pw2)) {
            $valores = array(
                //'id'
                'username' => $un,
                'firstName' => $fn,
                'lastName' => $ln,
                'email' => $em1,
                'password' => md5($pw1),
                'singUpDate' => date("Y-m-d H:i:s"),
                //'lastLogin' => date("Y-m-d H:i:s"),
                //'profilePic' => "default-pic.png"
            );
            return $this->insertar($valores);
        } else {
            return false;
        }
    }
    
    private function actualizarLastLogin($valorPK) {
        $valores = array(
            $this->primaryKey => $valorPK, 
            'lastLogin' => date("Y-m-d H:i:s")
        );
        
        $this->actualizar($valores);
    }

    public function login($em, $pw) {
        $pw = md5($pw);
        
        $valores = array('email' => $em, 'password' => $pw);
        $sql = 'SELECT count(*) FROM '.$this->nombreTabla;
        $sql .= ' WHERE email = :email AND password = :password';
        if($this->getOneCol($sql, $valores) == 1) {
            $sql = 'SELECT '.$this->primaryKey.' FROM '.$this->nombreTabla;
            $sql .= ' WHERE email = :email AND password = :password';
            $valorPK = $this->getOneCol($sql, $valores);
            $this->actualizarLastLogin($valorPK);
            return $valorPK;
        } else {
            $this->error = clases\Textos::$lgnFailed;
            return false;
        }
    }

    public function getError() {
        return $this->error;
    }
    
    public function getListaUsuarios($offset, $limit) {
        $registros = $this->obtenerPaginado($offset, $limit);
        foreach ($registros as $key => $value) {
            unset($registros[$key]['password']);
        }
        return $registros;
    }
}

