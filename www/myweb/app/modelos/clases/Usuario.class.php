<?php

namespace app\modelos\clases;

class Usuario {
    private $id = null;
    private $accountType = null;
    private $username = null;
    private $firstName = null;
    private $lastName = null;
    private $email = null;
    private $singUpDate = null;
    private $lastLogin = null;
    private $profilePic = null;
    
    public function __construct($valores) {
        foreach ($valores as $var => $value) {
            if(is_null($this->$var)){
                $this->$var = $value;
            }
        }
    }
    
    function getId() {
        return $this->id;
    }
    
    function getAccountType() {
        return $this->accountType;
    }
    
    function getUsername() {
        return $this->username;
    }

    function getFirstName() {
        return $this->firstName;
    }

    function getLastName() {
        return $this->lastName;
    }

    function getEmail() {
        return $this->email;
    }

    function getSignUpDate() {
        return $this->signUpDate;
    }

    function getLastLogin() {
        return $this->lastLogin;
    }

    function getProfilePic() {
        return $this->profilePic;
    }
    
    function getAll() {
        $data = array(
            'id' => $this->id,
            'accountType' => $this->accountType,
            'username' => $this->username,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'singUpDate' => $this->singUpDate,
            'lastLogin' => $this->lastLogin,
            'profilePic' => $this->profilePic
        );
        
        return $data;
    }

}

