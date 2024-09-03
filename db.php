<?php
// db.php

class Database
{
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $pdo;

    public function __construct()
    {
        // Betöltjük a konfigurációs beállításokat a db_config.php fájlból
        $config = require 'db_config.php';
        $this->host = $config['host'];
        $this->dbname = $config['dbname'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    // Kapcsolódás az adatbázishoz
    public function getConnection()
    {
        if ($this->pdo === null) {
            try {
                // DSN létrehozása a PDO-hoz
                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
                $this->pdo = new PDO($dsn, $this->username, $this->password);
                // Hiba mód beállítása kivételdobásra
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // Hiba esetén kivétel dobása
                throw new Exception('Adatbázis kapcsolódási hiba: ' . $e->getMessage());
            }
        }
        // Visszatérünk a PDO objektummal
        return $this->pdo;
    }
}
