<?php
class Conexion{
    private $conect;
    public function __construct()
    {
        $port = defined('port') ? port : '3306';
        $charset = defined('charset') ? charset : 'utf8mb4';
        $pdo = 'mysql:host=' . host . ';port=' . $port . ';dbname=' . db . ';charset=' . $charset;
        try {
            $this->conect = new PDO($pdo, user, pass);
            $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->conect = null;
            error_log('Cerro Prieto PDO: ' . $e->getMessage());
        }
    }
    public function conect()
    {
        return $this->conect;
    }
}

?>