<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

use GuzzleHttp\Client;
use Sunra\PhpSimple\HtmlDomParser;

class FacultyTimetable
{

    public static function handle($message, $from, $bot)
    {
        if ($message == "ft") {
            $reply = "Alright. I just heard you used the command for faculty timetable.\n";
            $reply .= "So, send me *ft* followed by a keyword out of the faculty's name and I'll show you related results. \n\nFor example, if you want to search for staff named Steve Jobs, please reply as: \n\n`ft ste`";
            $bot->sendMessage($from, $reply, "markdown");
            return;
        } else if (sizeof(explode("_", $message)) < 2) {
            $query = substr($message, 3, strlen($message));
            $bot->sendMessage($from, "Ok, your search query is " . $query);
            $options = self::getSearchResults($query);
            $reply = "";
            $i = 1;
            if (empty($options) || $options == "") {
                $bot->sendMessage($from, "Uh-Oh! I couldn't find any faculty with that keyword.");
                return;
            }
            foreach ($options as $option) {
                if ($i == sizeof($options)) break;
                $reply .= "\n\n" . $i . ") " . $option . " (/ft_" . $query . "_$i" . ")";
                $i++;
            }
        } else if (sizeof(explode("_", $message)) == 3) {
            $reply = self::getYears($message);
        } else if (sizeof(explode("_", $message)) == 4) {
            $reply = self::getSemester($message);
        } else if (sizeof(explode("_", $message)) == 5) {
            self::sendDoc($bot, $from, $message);
            return;
        } else {
            global $reply;
        }
        if (!empty($reply) && !is_null($reply) && isset($reply) && $reply != "") {
            $bot->sendMessage($from, $reply);
        }
    }

    public static function getSearchResults($query)
    {
        $ajaxURL = "https://intranet.cb.amrita.edu/TimeTable/Faculty/get_staff_list.php?limit=10&q=" . $query;
        $client = new Client();
        $response = $client->get($ajaxURL);
        return (empty(trim($response->getBody())) ? "" : explode("\n", $response->getBody()));
    }

    public static function getYears($prev)
    {
        $current_year = date("Y");
        $year = "Okay. Tell me the academic year you want the timetable for : ";
        for ($i = 4, $j = 3; $i >= 0; $i--, $j--) {
            $year .= "\n\n(" . (($current_year - $i) . "-" . substr((string)((int)$current_year - $j), 2, 4)) . ")  -  " . $prev . "_" . (($current_year - $i) . "" . substr((string)((int)$current_year - $j), 2, 4));
        }
        return $year;
    }

    public static function getSemester($prev)
    {
        $reply = "Okay which semester ? \n\n1) ODD (" . $prev . "_O" . ")";
        $reply .= "\n\n2) EVEN (" . $prev . "_E" . ")";
        return $reply;
    }

    public static function sendDoc($bot, $from, $msg)
    {
        $bot->sendMessage($from, "Please wait for a moment, while I search for your requested document :) ");
        $broken = explode("_", $msg);
        $query = $broken[1];
        $option_chosen = $broken[2];
        $year = $broken[3];
        $sem = $broken[4];

        $faculties = self::getSearchResults($query);
        $faculty = $faculties[$option_chosen - 1];
        $year = substr($year, 0, 4) . "_" . substr($year, 4, 6);

        $data = [
            'year' => trim($year),
            'sem' => trim($sem),
            'faculty' => trim($faculty),
            'Nyear' => trim($year),
            'Nsem' => trim($sem),
            'NAMEshwbutton' => "Show Details"
        ];
        $client = new Client();
        $response = $client->request("post", "https://intranet.cb.amrita.edu/TimeTable/Faculty/index.php", [
            'form_params' => $data,
        ]);
        $dom = HtmlDomParser::str_get_html($response->getBody());
        try {
            $filename = $dom->getElementsByTagName("iframe", 0)->src;
            $doc = "https://intranet.cb.amrita.edu/TimeTable/Faculty/" . $filename;
            $bot->sendDocument($from, $doc);
        } catch (Exception $exception) {
            $bot->sendMessage($from, "Oops! The document you asked for, is not uploaded yet.");
        }

    }
}
