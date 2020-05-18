<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */


use PDO;

class Database
{
    public $connection;
    private $DB_SERVER, $DB_USER, $DB_PASS, $DB_NAME;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        $this->DB_SERVER = DB_HOST;
        $this->DB_NAME = DB_NAME;
        $this->DB_PASS = DB_PASSWORD;
        $this->DB_USER = DB_USERNAME;
        $this->connection = $this->connect();
    }

    /**
     * Connects to the database using PDO
     * @return PDO
     */
    private function connect()
    {
        $conn = new PDO("mysql:host=$this->DB_SERVER;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }

    /**
     * Returns the created PDO connection
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

}