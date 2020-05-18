<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

namespace Classes;

use GuzzleHttp\Client;
use PDO;

define('AUTHORIZATION', 'Basic YWRtaW46YWRtaW5AQW5kQVBQ');

class DB_Query
{
    private $db;
    private $conn;

    /**
     * Constructor.
     **/
    public function __construct()
    {
        $this->db = new Database();
        $this->client = new Client([
            'base_uri' => "https://amritavidya.amrita.edu:8444/DataServices/rest"
        ]);
        $this->conn = $this->db->getConnection();
    }

    public function getAccessToken($userId)
    {
        $getToken = $this->conn->prepare("SELECT token FROM aums WHERE id=?");
        $getToken->execute([$userId]);
        return $getToken->fetchAll(PDO::FETCH_OBJ);
    }

    public function setAccessToken($userId)
    {

    }

    public function getUser($userId, $username, $dob)
    {
        $token = $this->getAccessToken($userId);
        $res = json_decode($this->client->get("/authRes?rollno=" . $username . "&dob=" . $dob . "&user_type=Student", [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ]
        ])->getBody());
        $this->conn->beginTransaction();
        return $res->NAME;
    }
}