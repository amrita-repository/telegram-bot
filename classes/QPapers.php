<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

use GuzzleHttp\Client;
use Sunra\PhpSimple\HtmlDomParser;

class QPapers
{

    public static function handle($message, $from, $bot)
    {
        if (strpos($message, "qpapersc") !== false) {
            $reply = self::getCourses();
        } else if (strpos($message, "qpapersem") !== false) {
            $reply = self::getSemesters($message);
        } else if (strpos($message, "qpapersa") !== false) {
            $reply = self::getAssessments($message);
        } else if (strpos($message, "qpapersub") !== false) {
            $reply = self::getSubjects($message);
        } else if (strpos($message, "/qd") !== false) {
            self::sendDoc($message, $bot, $from);
            return;
        } else {
            global $reply;
        }
        if (!empty($reply) && !is_null($reply) && isset($reply) && $reply != "") {
            $bot->sendMessage($from, $reply);
        }
    }

    public static function getCourses()
    {
        $prefix = "/qpapersem";
        $courses = "Please choose your course from the options below : \n\n";
        $courses .= "1) B.Tech        -   " . $prefix . "_150\n\n";
        $courses .= "2) BA Comm       -   " . $prefix . "_893\n\n";
        $courses .= "3) MA Comm       -   " . $prefix . "_894\n\n";
        $courses .= "4) Int. MSc & MA -   " . $prefix . "_903\n\n";
        $courses .= "7) MCA           -   " . $prefix . "_331\n\n";
        $courses .= "8) MSW           -   " . $prefix . "_393\n\n";
        $courses .= "9) M.Tech        -   " . $prefix . "_279\n\n";
        return $courses;
    }

    public static function getSemesters($msg)
    {
        $course = explode("_", $msg)[1];
        $url = "http://dspace.amritanet.edu:8080/xmlui/handle/123456789/" . $course;
        $client = new Client();
        if (!SHOULD_PROXY) {
            $response = $client->get($url);
        } else {
            $response = $client->post('http://dev.rajkumaar.co.in/proxy.php', [
                'form_params' => [
                    'data' => $url, 'hash' => HASH
                ]
            ]);
        }
        $dom = HtmlDomParser::str_get_html($response->getBody());
        $semesters = $dom->find('ul', 2)->getElementsByTagName('li');
        $result = "Okay which semester are you currently in ? ";
        foreach ($semesters as $item) {
            $result .= "\n\n" . trim($item->find('a[href]', 0)->text()) . " (";
            $total = sizeof(explode("/", $item->find('a[href]', 0)->href));
            $result .= "/qpapersa" . "_" . explode("/", $item->find('a[href]', 0)->href)[$total - 1] . ")";
        }
        return $result;
    }

    public static function getAssessments($msg)
    {
        $semester = explode("_", $msg)[1];
        $url = "http://dspace.amritanet.edu:8080/xmlui/handle/123456789/" . $semester;
        $client = new Client();
        if (!SHOULD_PROXY) {
            $response = $client->get($url);
        } else {
            $response = $client->post('http://dev.rajkumaar.co.in/proxy.php', [
                'form_params' => [
                    'data' => $url, 'hash' => HASH
                ]
            ]);
        }
        $dom = HtmlDomParser::str_get_html($response->getBody());
        $semesters = $dom->find('ul', 3)->getElementsByTagName('li');
        $result = "";
        $i = 1;
        foreach ($semesters as $item) {
            $title = trim($item->find('a[href]', 0)->text());
            $array = explode(" ", $title);
            unset($array[0]);
            unset($array[1]);
            $array = array_values($array);
            $title = implode(" ", $array);
            $result .= "\n\n\n" . $i . ") " . $title . " (";
            $total = sizeof(explode("/", $item->find('a[href]', 0)->href));
            $result .= "/qpapersub" . "_" . explode("/", $item->find('a[href]', 0)->href)[$total - 1] . ")";
            $i++;
        }
        $result .= "\n\nOkay which assessment are you preparing for ? ";
        return $result;
    }

    public static function getSubjects($msg)
    {
        $ass = explode("_", $msg)[1];
        $url = "http://dspace.amritanet.edu:8080/xmlui/handle/123456789/" . $ass;
        $client = new Client();
        if (!SHOULD_PROXY) {
            $response = $client->get($url);
        } else {
            $response = $client->post('http://dev.rajkumaar.co.in/proxy.php', [
                'form_params' => [
                    'data' => $url, 'hash' => HASH
                ]
            ]);
        }
        $dom = HtmlDomParser::str_get_html($response->getBody());
        $nextURL = $dom->find('ul', 2)->find('li', 0)->find('a[href]', 0)->href;
        $url = "http://dspace.amritanet.edu:8080" . $nextURL;
        if (!SHOULD_PROXY) {
            $response = $client->get($url);
        } else {
            $response = $client->post('http://dev.rajkumaar.co.in/proxy.php', [
                'form_params' => [
                    'data' => $url, 'hash' => HASH
                ]
            ]);
        }
        $dom = HtmlDomParser::str_get_html($response->getBody());
        $links = $dom->find('div.file-link');
        $titles = $dom->find('div.file-metadata');
        $result = "";
        for ($i = 0; $i < sizeof($titles); ++$i) {
            $title = explode("_", $titles[$i]->find('span', 1)->title)[0];
            $link = explode("?", $links[$i]->find('a', 0)->href)[0];
            $count = sizeof(explode("/", $link));
            $link = explode("/", $link)[$count - 1];
            $result .= "\n\n" . ($i + 1) . ") " . strtoupper($title) . "     -  /qd_" . $ass . "_" . ($i + 1);
        }
        return $result;
    }

    public static function sendDoc($msg, $bot, $from)
    {
        $bot->sendMessage($from, "Please wait for a moment, while I search for your requested document :) ");
        $ass = explode("_", $msg)[1];
        $subject = explode("_", $msg)[2];
        $url = "http://dspace.amritanet.edu:8080/xmlui/handle/123456789/" . $ass;
        $client = new Client();
        if (!SHOULD_PROXY) {
            $response = $client->get($url);
        } else {
            $response = $client->post('http://dev.rajkumaar.co.in/proxy.php', [
                'form_params' => [
                    'data' => $url, 'hash' => HASH
                ]
            ]);
        }
        $dom = HtmlDomParser::str_get_html($response->getBody());
        $nextURL = $dom->find('ul', 2)->find('li', 0)->find('a[href]', 0)->href;
        $url = "http://dspace.amritanet.edu:8080" . $nextURL;
        if (!SHOULD_PROXY) {
            $response = $client->get($url);
        } else {
            $response = $client->post('http://dev.rajkumaar.co.in/proxy.php', [
                'form_params' => [
                    'data' => $url, 'hash' => HASH
                ]
            ]);
        }
        $dom = HtmlDomParser::str_get_html($response->getBody());
        $links = $dom->find('div.file-link');
        $links_array = [];
        for ($i = 0; $i < sizeof($links); ++$i) {
            $link = explode("?", $links[$i]->find('a', 0)->href)[0];
            array_push($links_array, $link);
        }
        $doc = $links_array[$subject - 1];
        if (empty($doc) || is_null($doc)) {
            $bot->sendMessage($from, "Sorry about that! Something bad happened to me");
            return;
        }
        $url = 'http://dspace.amritanet.edu:8080/' . $doc;
        $client = new Client();
        if (!SHOULD_PROXY) {
            $response = $client->get($url);
        } else {
            $response = $client->post('http://dev.rajkumaar.co.in/proxy.php', [
                'form_params' => [
                    'data' => $url, 'hash' => HASH
                ]
            ]);
        }
        try {
            $title = urldecode(explode("_", basename($links_array[$subject - 1]))[0]);
            file_put_contents('docs/' . $title . '.pdf', $response->getBody());
            $bot->sendDocument($from, new CURLFile('docs/' . $title . '.pdf'));
            $bot->sendMessage($from, "There you go! All the best ^_^");
            unlink('docs/' . $title . '.pdf');
        } catch (Exception $exception) {
            $bot->sendMessage($from, "Uh-Oh, Something went wrong!! Sorry about that. Reported to @rajkumaar23 👍&#x1f44d;", "html");
            Logger::error($exception->getMessage() . "\n" . $exception->getTraceAsString());
        }
    }
}
