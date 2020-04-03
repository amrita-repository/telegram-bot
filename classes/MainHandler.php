<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class MainHandler
{

    public static function respond($message, $from, $name, $username)
    {
        file_put_contents("access.log", date('d/m/Y h:i:s a', time()) . " - " . $name . "(@" . $username . ")" . " -> " . $message . "\n", FILE_APPEND | LOCK_EX);
        $startKeyWords = ['hey', 'hello', 'hi', 'yo', 'menu', 'start', '/start'];
        $bot = new BotApi(API_KEY);
        try {
            if (in_array(trim(strtolower($message)), $startKeyWords)) {
                $keyboard = new InlineKeyboardMarkup([[['text' => 'Read more about my developer', 'url' => 'http://rajkumaar.co.in']]]);
                if ($message == "start" || $message == "/start") {
                    $reply = self::getStartText($from, $name, $bot, true);
                } else {
                    $reply = self::getStartText($from, $name, $bot);
                }
                $bot->sendMessage($from, $reply, "markdown", false, null, $keyboard);
                return;
            } else if (strpos($message, "act") !== false) {
                $reply = AcademicTimetable::handle($bot, $from, $message);
            } else if ((strpos($message, "qpapers") !== false) || (strpos($message, "qd") !== false)) {
                QPapers::handle($message, $from, $bot);
                return;
            } else if ((strpos(strtolower($message), "ft") !== false)) {
                FacultyTimetable::handle($message, $from, $bot);
            } else if ((strpos($message, "news") !== false)) {
                News::handle($from, $message, $bot);
            } else if (strtolower($message) == "logs") {
                if ($from == MASTER_ID) {
                    $keyboard = new InlineKeyboardMarkup([[
                        ['text' => 'Access', 'url' => "http://" . $_SERVER['HTTP_HOST'] . "/access.log"],
                        ['text' => 'Errors', 'url' => "http://" . $_SERVER['HTTP_HOST'] . "/error.log"]
                    ]]);
                    $bot->sendMessage($from, "May the logs be with you, master! ❤", "markdown", false, null, $keyboard);
                } else {
                    $bot->sendMessage($from, "Haha, Nice try! I can share the logs only with my master, @rajkumaar23 ^_^");
                }
                return;
            } else if ((strpos(strtolower($message), "thank") !== false)) {
                $reply = "You are welcome. I'll convey it to my master @rajkumaar23 ❤";
            } else {
                $reply = "Ahha! I don't understand your language!\n\nContact my master : @rajkumaar23 ❤";
            }
            if (!empty($reply) && !is_null($reply) && isset($reply) && $reply != "") {
                $bot->sendMessage($from, $reply);
            }
        } catch (Exception $exception) {
            $bot->sendMessage($from, "Uh-Oh, Something went wrong!! Sorry about that. Reported to @rajkumaar23 👍&#x1f44d;", "html");
            file_put_contents("error.log", date('d/m/Y h:i:s a', time()) . "  -  " . $exception->getMessage() . "\n" . $exception->getTraceAsString() . "\n\n\n\n\n", FILE_APPEND | LOCK_EX);
            if ($from != MASTER_ID) {
                $bot->sendMessage(MASTER_ID, $exception->getMessage());
                $bot->sendMessage(MASTER_ID, $exception->getTraceAsString());
            }
        }
    }

    public static function getStartText($from, $name, $bot, $start = false)
    {
        if ($start) {
            $start = "Hola " . $name . ", \nI'm here to make the lives of `Amritians` simpler. I'm still on `beta` testing. ";
            $start .= "\n\n`Please note that I work mostly on commands (which start with a /) and I don't understand your language otherwise.`";
            $bot->sendMessage($from, $start, "markdown");
            sleep(5);
        }
        $start = "\n\nAll set! How shall I help you ? Please click a command from the options below\n";
        $start .= "\n1) /actimetable - Academic Timetable for students of all departments";
        $start .= "\n\n2) /qpapersc - Previous year question papers for all the departments";
        $start .= "\n\n3) /ft - Get the timetable of any faculty in the campus";
        $start .= "\n\n4) /news - Get fastest news capsules regarding our university";
        $start .= "\n\nDeveloped with ❤ by @rajkumaar23 \n\n`I'm still on beta testing, please report to my developer if I go mad.`";
        $start .= "\n\nIf you would like to contribute, do visit https://github.com/rajkumaar23/amritarepo-bot";
        $start .= "\n\n*If you want me to display this menu anytime later, just send me a hi or hello*.";
        return $start;
    }
}
