<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

use GuzzleHttp\Client;

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

    public function getUser($userId, $username, $dob)
    {
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("authRes?rollno=" . $username . "&dob=" . $dob . "&user_type=Student", [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ],
            'allow_redirects' => true
        ]);
        return json_decode($response->getBody());
    }

    public function getAccessToken($userId)
    {
        $getToken = $this->conn->prepare("SELECT token FROM aums WHERE id = ?");
        $getToken->execute([$userId]);
        return $getToken->fetchAll(PDO::FETCH_OBJ)[0]->token ?? LOGIN_TOKEN;
    }

    public function setUserData($userId, $username, $name, $email, $token)
    {
        $setData = $this->conn->prepare(
            "INSERT INTO aums (id,username,name,email,token) VALUES (?,?,?,?,?)
                        ON DUPLICATE KEY UPDATE username = VALUES(username), name = VALUES(name), email = VALUES(email), token = VALUES(token)");
        $setData->execute([$userId, $username, $name, $email, $token]);
    }

    public function validateOTP($userId, $otp)
    {
        $username = $this->getUsername($userId);
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("authRes/register?rollno=" . $username . "&otp=" . $otp, [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ],
            'allow_redirects' => true
        ]);
        return json_decode($response->getBody());
    }

    public function getUsername($userId)
    {
        $getUsername = $this->conn->prepare("SELECT username FROM aums WHERE id = ?");
        $getUsername->execute([$userId]);
        return $getUsername->fetchAll(PDO::FETCH_OBJ)[0]->username;
    }

    public function getSemesterAttendance($userId)
    {
        $username = $this->getUsername($userId);
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("semAtdRes?rollno=" . $username, [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ],
            'allow_redirects' => true
        ]);
        $res = json_decode($response->getBody());
        $this->setAccessToken($userId, $res->Token);
        return $res->Semester;
    }

    public function setAccessToken($userId, $token)
    {
        $setToken = $this->conn->prepare("UPDATE aums SET token = ? WHERE id = ?");
        $setToken->execute([$token, $userId]);
    }

    public function getAttendance($userId, $sem)
    {
        $username = $this->getUsername($userId);
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("attRes?rollno=" . $username . "&sem=" . $sem, [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ],
            'allow_redirects' => true
        ]);

        $res = json_decode($response->getBody());
        $this->setAccessToken($userId, $res->Token);
        return $res;
    }

    public function getSemesterGrade($userId)
    {
        $username = $this->getUsername($userId);
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("semRes?rollno=" . $username, [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ],
            'allow_redirects' => true
        ]);
        $res = json_decode($response->getBody());
        $this->setAccessToken($userId, $res->Token);
        return $res->Semester;
    }

    public function getGrade($userId, $sem)
    {
        $username = $this->getUsername($userId);
        $token = $this->getAccessToken($userId);
        $response = $this->client->get("andRes?rollno=" . $username . "&sem=" . $sem, [
            'headers' => [
                'Authorization' => AUTHORIZATION,
                'token' => $token
            ],
            'allow_redirects' => true
        ]);
        $res = json_decode($response->getBody());
        $this->setAccessToken($userId, $res->Token);
        return $res;
    }

    public function clearUserData($userId)
    {
        $setData = $this->conn->prepare("DELETE FROM aums WHERE id  = ?");
        $setData->execute([$userId]);
    }

    public function checkUser($userId)
    {
        return $this->getAccessToken($userId) != LOGIN_TOKEN;
    }
}

