<?php

/*
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

$startKeyWords = ['hey', 'hello', 'hi', 'yo', 'menu', 'start', '/start'];
$reply = "Oh dear, I wish I was a human to understand what you speak 😓️ May be try that with @rajkumaar23 and see if he understands? 😬️";

class MainHandler
{

    public static function respond($message, $from, $name, $username)
    {
        $smallerMessage = strtolower($message);
        if ($from != MASTER_ID && SHOULD_PROXY) {
            Analytics::storeAnalyticsData($name, $username??'', $message);
        }
        $bot = new BotApi(API_KEY);
        try {
            global $startKeyWords;
            if (in_array(trim($smallerMessage), $startKeyWords)) {
                $keyboard = new InlineKeyboardMarkup(
                    [
                        [
                            ['text' => 'About my Master', 'url' => 'http://rajkumaar.co.in'],
                            ['text' => 'Source Code', 'url' => 'https://github.com/rajkumaar23/amritarepo-bot']
                        ]
                    ]
                );
                if ($smallerMessage == "start" || $smallerMessage == "/start") {
                    $reply = self::getStartText($from, $name, $bot, true);
                } else {
                    $reply = self::getStartText($from, $name, $bot);
                }
                $bot->sendMessage($from, $reply, "markdown", false, null, $keyboard);
                return;
            } else if (strpos($smallerMessage, "act") !== false) {
                $reply = AcademicTimetable::handle($bot, $from, $message);
            } else if ((strpos($smallerMessage, "qpapers") !== false) || (strpos($smallerMessage, "qd") !== false)) {
                QPapers::handle($smallerMessage, $from, $bot);
                return;
            } else if ((strpos($smallerMessage, "ums") !== false)) {
                AUMS::handle($smallerMessage, $from, $bot);
            } else if ((strpos($smallerMessage, "ft") !== false)) {
                FacultyTimetable::handle($smallerMessage, $from, $bot);
            } elseif ((strpos($smallerMessage, "news") !== false)) {
                News::handle($from, $smallerMessage, $bot);
            } elseif ($smallerMessage === "logs") {
                if ($from == MASTER_ID) {
                    $keyboard = new InlineKeyboardMarkup(
                        [
                            [
                                ['text' => 'Access', 'url' => "http://" . $_SERVER['HTTP_HOST'] . "/logs/access.log"],
                                ['text' => 'Errors', 'url' => "http://" . $_SERVER['HTTP_HOST'] . "/logs/error.log"]
                            ]
                        ]
                    );
                    $bot->sendMessage($from, "May the logs be with you, master! ❤", "markdown", false, null, $keyboard);
                } else {
                    $bot->sendMessage(
                        $from,
                        "Haha, Nice try! I can share the logs only with my master, @rajkumaar23 ^_^"
                    );
                }
                return;
            } else if (strpos($smallerMessage, "anly") !== false && $from == MASTER_ID) {
                $reply = Analytics::handle($smallerMessage, $from, $bot);
            } else if ((strpos($smallerMessage, "thank") !== false)) {
                $reply = "You are welcome. I'll convey it to my master @rajkumaar23 ❤";
            } else if (strpos($smallerMessage, "love you") !== false || strpos($smallerMessage, "love u") !== false || strpos($smallerMessage, "love ya") !== false) {
                $reply = "Hey " . explode(" ", $name)[0] . ", \n\nYou are wind beneath my wings 😌️";
            } else {
                global $reply;
            }
            if (!empty($reply) && $reply != "") {
                $bot->sendMessage($from, $reply);
            }
        } catch (Exception $exception) {
            $bot->sendMessage($from, "Uh-Oh, Something went wrong!! Sorry about that. Reported to @rajkumaar23 👍&#x1f44d;", "html");
            Logger::error($exception->getMessage() . "\n" . $exception->getTraceAsString());
        }
    }

    public static function getStartText($from, $name, $bot, $start = false)
    {
        if ($start) {
            $start = "Hola " . $name . ", \nI'm here to make the lives of `Amritians` simpler. 😉️";
            $start .= "\n\n`Please note that I work mostly on commands (which start with a /) and I don't understand your language otherwise.`";
            $bot->sendMessage($from, $start, "markdown");
            sleep(3);
        }
        $start = "\n\nAll set! How shall I help you ? Please click a command from the options below\n";
        $start .= "\n📅   /actimetable - Student Timetable ";
        $start .= "\n\n📝   /qpapersc - Question papers ";
        $start .= "\n\n💻️   /ums - AUMS ";
        $start .= "\n\n👨‍🏫   /ft - Faculty Timetable ";
        $start .= "\n\n📰   /news - News capsules ";
        $start .= "\n\nDeveloped with ❤ by @rajkumaar23";
        $start .= "\n\n*If you want me to display this menu anytime later, just send me a hi or hello*.";
        return $start;
    }
}
