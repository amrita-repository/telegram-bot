<?php
use GuzzleHttp\Client;

class AUMS
{
    public static function handle($message, $from, $bot)
    {
        $repo = new AUMSRepository();
        if ($message == "ums" || $message == "/ums") {
            $res = $repo->checkUser($from);
            if ($res) {
                $reply = "For Attendance /ums\_a\n\nFor Grades /ums\_g\n\nTo Logout /ums\_logout";
                $bot->sendMessage($from, $reply, "markdown");
                return;
            } else {

                $reply = "Send your AUMS credentials in this format\n\n`ums roll_number dob(yyyy-mm-dd)` \n\nExample \n\n`ums cb.en.u4cse17xxx 1990-08-31` ";
                $bot->sendMessage($from, $reply, "markdown");
                return;
            }


        } else if (sizeof(explode(" ", $message)) == 3 and ((explode(" ", $message)[0] == "ums") || (explode(" ", $message)[0] == "/ums"))) {
            $credentials = substr($message, 4, strlen($message));
            $username = explode(" ", $credentials)[0];
            $dob = explode(" ", $credentials)[1];
            $data = $repo->getUser($from, $username, $dob);
            if ($data->Status == "OK") {
                $repo->setUserData($from, $username, $data->NAME, $data->Email, LOGIN_TOKEN);
                $reply = "Hola " . $data->NAME . "! Enter OTP to Continue \t ( /umsotp xxxxx )\n\nExample /umsotp 12345";
                $bot->sendMessage($from, $reply, "markdown");
            } else {
                $reply = $data . " ! \nTry Again...";
                $bot->sendMessage($from, $reply, "markdown");
                return;
            }

        } else if (sizeof(explode(" ", $message)) == 2 and (explode(" ", $message)[0] == "/umsotp")) {
            $otp = explode(" ", $message)[1];

            $data = $repo->validateOTP($from, $otp);
            if ($data->Status == "Y") {
                $repo->setAccessToken($from, $data->Token);
                $reply = "Hurray! Login Successful \n\nFor Attendance /ums\_a\n\nFor Grades /ums\_g\n\nLogout /ums\_logout";
                $bot->sendMessage($from, $reply, "markdown");
            } else {
                $reply = "Incorrect OTP...";
                $bot->sendMessage($from, $reply, "markdown");
                return;
            }

        } else if ($message == "/ums_a") {
            $res = $repo->getSemesterAttendance($from);
            $reply = "Choose Semester";
            foreach ($res as $result) {
                $reply .= "\n\nSemester " . $result->Semester . " - /ums\_a\_" . $result->Id;
            }
            $bot->sendMessage($from, $reply, "markdown");
            return;
        } else if (sizeof(explode("_", $message)) == 3 and (explode("_", $message)[0] == "/ums") and (explode("_", $message)[1] == "a")) {
            $sem = explode("_", $message)[2];
            $reply = "Attendance Details";
            $res = $repo->getAttendance($from, $sem);
            foreach ($res->Values as $result) {
                $reply .= "\n\n" . $result->CourseCode . " - " . $result->CourseName . "\nClass Attended : `" . $result->ClassPresent . "` / `" . $result->ClassTotal . "`\nPercentage : `" . $result->TotalPercentage . "` %";
            }
            if (empty($result))
                $reply .= " Not Available";
            $bot->sendMessage($from, $reply, "markdown");
            return;
        } else if ($message == "/ums_g") {
            $res = $repo->getSemesterGrade($from);
            $reply = "Choose Semester";
            foreach ($res as $result) {
                $reply .= "\n\nSemester " . $result->Semester . " - /ums\_g\_" . $result->Id;
            }

            $bot->sendMessage($from, $reply, "markdown");
            return;
        } else if (sizeof(explode("_", $message)) == 3 and (explode("_", $message)[0] == "/ums") and (explode("_", $message)[1] == "g")) {
            $sem = explode("_", $message)[2];
            $reply = "Grade Details";
            $res = $repo->getGrade($from, $sem);
            foreach ($res->Subject as $result) {
                $reply .= "\n\n" . $result->CourseCode . " - " . $result->CourseName . "\nGrade Obtained : `" . $result->Grade . "`";
            }
            if (empty($result))
                $reply .= " Not Available";
            $bot->sendMessage($from, $reply, "markdown");
            return;
        } else if ($message == "/ums_logout") {

            $reply = "Logout Successful";
            $repo->clearUserData($from);
            $bot->sendMessage($from, $reply, "markdown");
            return;
        } else {
            $reply = "Oops! The Request Failed.";
            $bot->sendMessage($from, $reply, "markdown");
            return;
        }


    }
}

