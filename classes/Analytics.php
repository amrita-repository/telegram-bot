<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

use TelegramBot\Api\Types\ReplyKeyboardMarkup;

/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */
class Analytics
{
    static $regex = '/(\d+\/\d+\/\d+) (\d+:\d+:\d+) (am|pm) - (.+)\(@(.+)\) -> (.*)/m';

    public static function handle($message, $from, $bot)
    {
        $message = strtolower($message);
        switch ($message) {
            case "anly" :
                $keyboard = new ReplyKeyboardMarkup([
                    ["anly - Common Stats"], ["anly - Top 10 users"], ["anly - Message Stats"]
                ], true);
                $bot->sendMessage($from, "What analytics do you want to see ?", null, false, null, $keyboard);
                return "";
            case "anly - top 10 users" :
                return self::getUsersFrequency(10);
            case "anly - common stats" :
                return self::commonStats();
            case "anly - message stats" :
                return self::messageStats();
            default :
                return "Uh? 🙄️";
        }
    }

    private static function getUsersFrequency($count)
    {
        $handle = fopen("access.log", "r");
        if ($handle) {
            $map = [];
            while (($line = fgets($handle)) !== false) {
                preg_match(self::$regex, $line, $match);
                if (empty($match)) continue;
                $name = $match[4];
                $username = $match[5];
                if ($username == MASTER_USERNAME) {
                    continue;
                }
                $key = $name . '(@' . $username . ')';
                if (array_key_exists($key, $map)) {
                    $map[$key]++;
                } else {
                    $map[$key] = 1;
                }
            }
            fclose($handle);
            $reply = "Here are the top 10 users of this bot\n\n";
            arsort($map);
            if (!empty($count)) $map = array_slice($map, 0, min($count, sizeof($map)));
            foreach ($map as $key => $value) {
                $reply .= ($key . " - " . $value . "\n");
            }
            return $reply;
        } else {
            return "Opening Access Log Failed";
        }
    }

    private static function commonStats()
    {
        $handle = fopen("access.log", "r");
        if ($handle) {
            $totalCount = 0;
            $todayCount = 0;
            $todayUsersMap = [];
            while (($line = fgets($handle)) !== false) {
                preg_match(self::$regex, $line, $match);
                if (empty($match)) continue;
                $date = $match[1];
                $name = $match[4];
                $username = $match[5];
                if ($username == MASTER_USERNAME) {
                    continue;
                }
                if ($date == date('d/m/Y')) {
                    $todayCount++;
                    $key = $name . '(@' . $username . ')';
                    if (array_key_exists($key, $todayUsersMap)) {
                        $todayUsersMap[$key]++;
                    } else {
                        $todayUsersMap[$key] = 1;
                    }
                }
                $totalCount++;
            }
            fclose($handle);
            $reply = "Here are some common stats of the bot\n\n";
            $reply .= "Total hits till date : $totalCount \n";
            $reply .= "Total hits today : $todayCount\n\n";
            if (!empty($todayUsersMap)) {
                $reply .= "\nHere are the top 10 users for today\n\n";
                arsort($todayUsersMap);
            }
            $map = array_slice($todayUsersMap, 0, min(10, sizeof($todayUsersMap)));
            foreach ($map as $key => $value) {
                $reply .= ($key . " - " . $value . "\n");
            }
            return $reply;
        } else {
            return "Opening Access Log Failed";
        }
    }

    private static function messageStats()
    {
        $handle = fopen("access.log", "r");
        if ($handle) {
            $map = [];
            while (($line = fgets($handle)) !== false) {
                preg_match(self::$regex, $line, $match);
                if (empty($match)) continue;
                $username = $match[5];
                $msg = $match[6];
                if ($username == MASTER_USERNAME) {
                    continue;
                }
                $key = self::extractFeature($msg);
                if (array_key_exists($key, $map)) {
                    $map[$key]++;
                } else {
                    $map[$key] = 1;
                }
            }
            fclose($handle);
            $reply = "Here are the message stats\n\n";
            arsort($map);
            foreach ($map as $key => $value) {
                $reply .= ($key . " - *" . $value . "*\n\n");
            }
            return $reply;
        } else {
            return "Opening Access Log Failed";
        }
    }

    private static function extractFeature($message)
    {
        global $startKeyWords;
        if (in_array(trim($message), $startKeyWords)) {
            return "▶️ Start";
        } else if (strpos($message, "act") !== false) {
            return "📅️ Academic Timetable";
        } else if ((strpos($message, "qpapers") !== false) || (strpos($message, "qd") !== false)) {
            return "📚️ QPapers";
        } else if ((strpos(strtolower($message), "ft") !== false)) {
            return "👨‍🏫  Faculty Timetable";
        } else if ((strpos($message, "news") !== false)) {
            return "📰️ News";
        } else if ((strpos(strtolower($message), "thank") !== false)) {
            return "🙏️ Thanks";
        } else {
            return "💩️ Human Language";
        }
    }
}
