<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

require_once 'vendor/autoload.php';

if (is_file(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

use Rollbar\Rollbar;

if (!empty($_ENV["ROLLBAR_TOKEN"])) {
    Rollbar::init(
        array(
            'access_token' => $_ENV["ROLLBAR_TOKEN"],
            'environment' => 'production'
        )
    );
}

require_once 'config.php';
require_once 'classes/AcademicTimetable.php';
require_once 'classes/MainHandler.php';
require_once 'classes/QPapers.php';
require_once 'classes/FacultyTimetable.php';
require_once 'classes/NewsModel.php';
require_once 'classes/News.php';
require_once 'classes/Analytics.php';
require_once 'classes/AUMS.php';
require_once 'classes/AUMSRepository.php';
require_once 'classes/Database.php';
require_once 'classes/RedisUtils.php';
require_once 'classes/Logger.php';

date_default_timezone_set('Asia/Kolkata');

Flight::route('/healthcheck', function () {
    echo 'OK';
});

Flight::route('/' . API_KEY, function () {
    $data = json_decode(file_get_contents('php://input'));
    $message = $data->message->text;
    $from = $data->message->from->id;
    $name = $data->message->from->first_name;
    $username = isset($data->message->from->username) ? $data->message->from->username : "NA";
    MainHandler::respond($message, $from, $name, $username);
});

Flight::start();
