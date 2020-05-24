<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

define("AUMS_MENU", "Here's the menu \n\n➡️ For Attendance /ums\_a\n\n➡️ For Grades /ums\_g\n\n➡️ Logout /ums\_logout\n\n`Disclaimer : AUMS API isn't stable and fails miserably sometimes.`");
define("LOGIN_MENU", "Send your AUMS credentials in this format\n`ums roll_number dob(yyyy-mm-dd)` \n\nExample\n`ums cb.en.u4cse17xxx 1990-08-31` ");
define("NOT_AVAILABLE", "I couldn't find data for your requested semester 😓️");

class AUMS
{
    public static function handle($message, $from, $bot)
    {
        try{
            $repo = new AUMSRepository();
            $res = $repo->checkUser($from);
            if ($message == "ums" || $message == "/ums") {
                $bot->sendMessage($from, empty($res) ? LOGIN_MENU : AUMS_MENU, "markdown");
            } else if (sizeof(explode(" ", $message)) == 3 && ((explode(" ", $message)[0] == "ums") || (explode(" ", $message)[0] == "/ums"))) {
                $credentials = explode(" ", trim(substr($message, 4, strlen($message))));
                $username = $credentials[0];
                $dob = $credentials[1];
                $data = $repo->getUser($from, $username, $dob);
                if ($data->Status == "OK") {
                    $repo->setUserData($from, $username, $data->NAME, $data->Email, LOGIN_TOKEN);
                    $reply = "Hola " . trim($data->NAME) . "!\nEnter OTP to continue \t ( /umsotp xxxxx )\n\n`Example: /umsotp 12345`";
                    $bot->sendMessage($from, $reply, "markdown");
                } else {
                    $reply = "Hey! Your credentials are invalid. Please try again.";
                    $bot->sendMessage($from, $reply, "markdown");
                }
            } else if (sizeof(explode(" ", $message)) == 2 && (explode(" ", $message)[0] == "/umsotp")) {
                $otp = explode(" ", $message)[1];
                $data = $repo->validateOTP($from, $otp);
                if ($data->Status == "Y") {
                    $repo->setAccessToken($from, $data->Token);
                    $reply = "Hurray! You have been logged into AUMS successfully!\n\n" . AUMS_MENU;
                    $bot->sendMessage($from, $reply, "markdown");
                } else {
                    $reply = "Incorrect OTP...";
                    $bot->sendMessage($from, $reply, "markdown");
                }
            } else if (empty($res)) {
                $bot->sendMessage($from, LOGIN_MENU, "markdown");
            } else if ($message == "/ums_a") {
                $res = $repo->getSemesterAttendance($from);
                $reply = "Choose your semester for viewing attendance";
                foreach ($res as $result) {
                    $reply .= "\n\n*Semester " . $result->Semester . "* - /ums\_a\_" . $result->Id;
                }
                $bot->sendMessage($from, $reply, "markdown");
            } else if (sizeof(explode("_", $message)) == 3 && (explode("_", $message)[0] == "/ums") && (explode("_", $message)[1] == "a")) {
                $sem = explode("_", $message)[2];
                $reply = "Here are your attendance details";
                $res = $repo->getAttendance($from, $sem);
                foreach ($res->Values as $result) {
                    $reply .= "\n\n$result->CourseCode - $result->CourseName \nClass Attended : `" . intval($result->ClassPresent) . "` / `" . intval($result->ClassTotal) . "`\nPercentage : `" . $result->TotalPercentage . "` %";
                }
                if (empty($res->Values))
                    $reply = NOT_AVAILABLE;
                $bot->sendMessage($from, $reply, "markdown");
            } else if ($message == "/ums_g") {
                $res = $repo->getSemesterGrade($from);
                $reply = "Choose your semester for viewing grades";
                foreach ($res as $result) {
                    $reply .= "\n\nSemester " . $result->Semester . " - /ums\_g\_" . $result->Id;
                }
                $bot->sendMessage($from, $reply, "markdown");
            } else if (sizeof(explode("_", $message)) == 3 && (explode("_", $message)[0] == "/ums") && (explode("_", $message)[1] == "g")) {
                $sem = explode("_", $message)[2];
                $reply = "Grade Details";
                $res = $repo->getGrade($from, $sem);
                foreach ($res->Subject as $result) {
                    $reply .= "\n\n$result->CourseCode - $result->CourseName \nGrade Obtained : *$result->Grade*";
                }
                if (empty($res->Subject))
                    $reply = NOT_AVAILABLE;
                $bot->sendMessage($from, $reply, "markdown");
            } else if ($message == "/ums_logout") {
                $reply = "You have been logged out of AUMS successfully!";
                $repo->clearUserData($from);
                $bot->sendMessage($from, $reply, "markdown");
            } else {
                global $reply;
                $bot->sendMessage($from, $reply);
            }
        } catch(\Exception $exception){
            $bot->sendMessage($from, "Something went wrong while connecting to AUMS server. Please try again later");
            file_put_contents("error.log", date('d/m/Y h:i:s a', time()) . "  -  " . $exception->getMessage() . "\n" . $exception->getTraceAsString() . "\n\n\n\n\n", FILE_APPEND | LOCK_EX);
        }
    }
}

