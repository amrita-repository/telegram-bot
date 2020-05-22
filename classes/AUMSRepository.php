<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

use GuzzleHttp\Client;
use PDO;

define('AUTHORIZATION', 'Basic YWRtaW46YWRtaW5AQW5kQVBQ');
define('LOGIN_TOKEN', 'logintoken');

class AUMSRepository
{
    private $db;
    private $conn;
    private $client;

    /**
     * Constructor.
     **/
    public function __construct()
    {
        $this->db = new Database();
        $this->client = new Client([
            'base_uri' => "https://amritavidya.amrita.edu:8444/DataServices/rest/"
        ]);
        $this->conn = $this->db->getConnection();
    }

    public function getAccessToken($userId)
    {
        $getToken = $this->conn->prepare("SELECT token FROM aums WHERE id=?");
        $getToken->execute([$userId]);
        return $getToken->fetchAll(PDO::FETCH_OBJ)[0]->token ?? LOGIN_TOKEN;
    }

    public function setAccessToken($userId, $token)
    {
        $setToken = $this->conn->prepare("UPDATE aums SET token =? WHERE id=?");
        $setToken->execute([$token, $userId]);
        $this->conn->commit();
    }

    public function getUser($userId, $username, $dob)
    {
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("authRes?rollno=" . $username . "&dob=" . $dob . "&user_type=Student", [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ]
        ]);
        $this->conn->beginTransaction();
        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody());
        }
    }

    public function setUserData($userId, $username, $name, $email, $token)
    {
        $setData = $this->conn->prepare("INSERT IGNORE INTO aums (id,username,name,email,token) VALUES (?,?,?,?,?);");
        $setData->execute([$userId, $username, $name, $email, $token]);
        $this->conn->commit();
    }

    public function getUsername($userId)
    {
        $getUsername = $this->conn->prepare("SELECT username FROM aums WHERE id=?");
        $getUsername->execute([$userId]);
        return $getUsername->fetchAll(PDO::FETCH_OBJ)[0]->username;
    }

    public function validateOTP($userId, $otp)
    {
        $username = $this->getUsername($userId);
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("authRes/register?rollno=" . $username . "&otp=" . $otp, [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ]
        ]);
        $this->conn->beginTransaction();
        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody());
        }

    }

    public function getSemesterAttendance($userId)
    {
        $username = $this->getUsername($userId);
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("semAtdRes?rollno=" . $username, [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ]
        ]);
        $this->conn->beginTransaction();
        if ($response->getStatusCode() == 200) {
            $res = json_decode($response->getBody());
            $this->setAccessToken($userId, $res->Token);
            return $res->Semester;
        }
    }

    public function getAttendance($userId, $sem)
    {
        $username = $this->getUsername($userId);
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("attRes?rollno=" . $username . "&sem=" . $sem, [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ]
        ]);
        $this->conn->beginTransaction();
        if ($response->getStatusCode() == 200) {
            $res = json_decode($response->getBody());
            $this->setAccessToken($userId, $res->Token);
            return $res;
        }
    }

    public function getSemesterGrade($userId)
    {
        $username = $this->getUsername($userId);
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("semRes?rollno=" . $username, [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ]
        ]);
        $this->conn->beginTransaction();
        if ($response->getStatusCode() == 200) {
            $res = json_decode($response->getBody());
            $this->setAccessToken($userId, $res->Token);
            return $res->Semester;
        }
    }

    public function getGrade($userId, $sem)
    {
        $username = $this->getUsername($userId);
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("andRes?rollno=" . $username . "&sem=" . $sem, [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ]
        ]);
        $this->conn->beginTransaction();
        if ($response->getStatusCode() == 200) {
            $res = json_decode($response->getBody());
            $this->setAccessToken($userId, $res->Token);
            return $res;
        }
    }
}

