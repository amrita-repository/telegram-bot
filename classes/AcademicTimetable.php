<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

class AcademicTimetable
{

    public static function handle($bot, $from, $msg)
    {
        switch ($msg) {
            case '/actimetable' :
                $reply = self::getYears();
                break;
            default:
                switch (sizeof(explode("_", $msg))) {
                    case 2:
                        $reply = self::getCourses($msg);
                        break;
                    case 3:
                        $reply = self::getSemesters($msg);
                        break;
                    case 4:
                        $reply = self::getSections($msg);
                        break;
                    case 5:
                        $reply = self::getBranches($msg);
                        break;
                    case 6:
                        $bot->sendMessage($from, "Please wait for a moment, while I search for the requested document :) \n`In case I dont respond, it means that the file has not yet been uploaded.`", "markdown");
                        try {
                            $bot->sendDocument($from, self::getTimetableURL($msg));
                            $bot->sendMessage($from, "There you go! Check out your academic timetable !!");
                        } catch (\TelegramBot\Api\HttpException $exception) {
                            file_put_contents("error.log", date('d/m/Y h:i:s a', time()) . "  -  " . $exception->getMessage() . "\n" . $exception->getTraceAsString() . "\n\n\n\n\n", FILE_APPEND | LOCK_EX);
                        }
                        return "";
                    default:
                        $reply = "Oh-No! I don't understand human language!\nContact my master : @rajkumaar23";
                        break;
                }
                break;
        }
        return $reply;
    }

    public static function getYears()
    {
        $current_year = date("Y");
        $year = "";
        for ($i = 4, $j = 3; $i >= 0; $i--, $j--) {
            $year .= "\n\n(" . (($current_year - $i) . "-" . substr((string)((int)$current_year - $j), 2, 4)) . ")  -  /act_" . (($current_year - $i) . "" . substr((string)((int)$current_year - $j), 2, 4));
        }
        $year .= "\n\nPlease choose `current` academic year from the below options : ";
        return $year;
    }

    public static function getCourses($year)
    {
        $courses = "Please choose your course from the options below : \n\n";
        $courses .= "1)B.Tech     -   " . $year . "_BTech\n\n";
        $courses .= "2)BA         -   " . $year . "_BA\n\n";
        $courses .= "3)IMSc       -   " . $year . "_IMsc\n\n";
        $courses .= "4)M.Tech     -   " . $year . "_MTech\n\n";
        $courses .= "5)MA         -   " . $year . "_MA\n\n";
        $courses .= "6)MBA        -   " . $year . "_MBA\n\n";
        $courses .= "7)MCA        -   " . $year . "_MCA\n\n";
        $courses .= "8)MSW        -   " . $year . "_MSW\n\n";
        $courses .= "9)PGD        -   " . $year . "_PGD\n\n";
        return $courses;
    }

    public static function getSemesters($prev)
    {
        $sems = "Okay now! You're progressing nice! Choose your semester : ";
        for ($i = 1; $i <= 10; ++$i) {
            $sems .= "\n\n Semester " . $i . "   -  " . $prev . "_" . $i;
        }
        return $sems;
    }

    public static function getSections($prev)
    {
        $sections = "Okay cool! Please be patient. Just one more! Choose your section : ";
        for ($i = 'A'; $i <= 'F'; ++$i) {
            $sections .= "\n\n Section " . $i . "   -  " . $prev . "_" . $i;
        }
        return $sections;
    }

    private static function getBranches($msg)
    {
        $course = explode("_", $msg)[2];
        $response = "Okay! Final question xD ! Which branch do you belong to ?";
        $branches = [];
        switch ($course) {
            case "BTech":
                array_push($branches, "AEE");
                array_push($branches, "CHE");
                array_push($branches, "CIE");
                array_push($branches, "CVI");
                array_push($branches, "CSE");
                array_push($branches, "ECE");
                array_push($branches, "EEE");
                array_push($branches, "EIE");
                array_push($branches, "MEE");
                break;
            case "BA":
                array_push($branches, "MAC");
                array_push($branches, "ENG");
                break;
            case "IMsc":
                array_push($branches, "CHE");
                array_push($branches, "MAT");
                array_push($branches, "PHY");
                break;
            case "MTech":
                array_push($branches, "ATE");
                array_push($branches, "ATL");
                array_push($branches, "BME");
                array_push($branches, "CEN");
                array_push($branches, "CHE");
                array_push($branches, "CIE");
                array_push($branches, "CSE");
                array_push($branches, "CSP");
                array_push($branches, "CVI");
                array_push($branches, "CYS");
                array_push($branches, "EBS");
                array_push($branches, "EDN");
                array_push($branches, "MFG");
                array_push($branches, "MSE");
                array_push($branches, "PWE");
                array_push($branches, "RET");
                array_push($branches, "RSW");
                array_push($branches, "SCE");
                array_push($branches, "VLD");
                break;
            case "MA":
                array_push($branches, "CMN");
                array_push($branches, "MAC");
                array_push($branches, "ENG");
                break;
            case "MBA":
                array_push($branches, "MBA");
                break;
            case "MCA":
                array_push($branches, "MCA");
                break;
            case "MSW":
                array_push($branches, "MSW");
                break;
            case "PGD":
                array_push($branches, "JLM");
                break;
        }
        foreach ($branches as $branch) {
            $response .= "\n\n" . $branch . "   -  " . $msg . "_" . $branch;
        }
        return $response;
    }

    public static function getTimetableURL($msg)
    {
        $url = 'https://intranet.cb.amrita.edu/TimeTable/PDF';
        $data = explode("_", $msg);
        $url .= "/" . substr($data[1], 0, 4) . "_" . substr($data[1], 4, 6);
        $url .= "/" . $data[2];
        $url .= "/" . $data[5];
        $url .= "/" . $data[2] . $data[5] . $data[4] . $data[3] . ".jpg";
        return $url;
    }
}
