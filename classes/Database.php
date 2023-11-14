<?php
/*
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */


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
        $this->loadMigrations();
    }

    /**
     * Connects to the database using PDO
     * @return PDO
     */
    private function connect(): PDO
    {
        $conn = new PDO("mysql:host=$this->DB_SERVER;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }

    /**
     * Load initial migrations for creating tables
     */
    public function loadMigrations()
    {
        $migrations = [
            "CREATE TABLE IF NOT EXISTS `aums` ( `id` bigint(255) NOT NULL, `username` varchar(255) NOT NULL, `name` varchar(255) DEFAULT NULL, `email` varchar(255) DEFAULT NULL, `token` text DEFAULT NULL, PRIMARY KEY (`id`))"
        ];

        $this->connection->beginTransaction();
        foreach ($migrations as $migration) {
            $this->connection->exec($migration);
        }
        if ($this->connection->inTransaction()) {
            $this->connection->commit();
        }
    }

    /**
     * Returns the created PDO connection
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

}
