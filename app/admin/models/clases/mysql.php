<?php

class mysql
{
    private $host;
    private $user;
    private $pass;
    private $db;

    public function __construct()
    {
        /* $this->host = 'appwhatsapp.crzqomk2sbtp.us-east-2.rds.amazonaws.com';
        $this->user = 'root';
        $this->pass = 'Clau32290398';
        $this->db = 'Whatsapp';  */
        $this->host = 'localhost';
        $this->user = 'pma';
        $this->pass = 'Seguros2022.';
        $this->db = 'Whatsapp';
    }

    public function Conexion()
    {
        $mysql = new mysqli($this->host, $this->user, $this->pass, $this->db);

        //error_reporting(0);
        
        if (isset($mysql->mysqli_connect_errno)) {
            echo 'Error de conexion';
            
        } elseif ($mysql->set_charset('utf8')) {
            return $mysql;
        }
    }

    public function __destruct()
    {
        mysqli_close($this->Conexion());
    }
}
