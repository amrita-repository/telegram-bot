<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

require_once 'vendor/autoload.php';
require_once 'classes/AcademicTimetable.php';
require_once 'config.php';
require_once 'classes/MainHandler.php';
require_once 'classes/QPapers.php';
require_once 'classes/FacultyTimetable.php';
require_once 'classes/NewsModel.php';
require_once 'classes/News.php';
require_once 'classes/Analytics.php';
require_once 'classes/AUMS.php';
require_once 'classes/AUMSRepository.php';
require_once 'classes/Database.php';
date_default_timezone_set('Asia/Kolkata');

Flight::route('/' . API_KEY, function () {
    $data = json_decode(file_get_contents('php://input'));
    $message = $data->message->text;
    $from = $data->message->from->id;
    $name = $data->message->from->first_name;
    $username = isset($data->message->from->username) ? $data->message->from->username : "NA";
    MainHandler::respond($message, $from, $name, $username);
});
Flight::start();
