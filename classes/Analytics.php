<?php
/*
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

use TelegramBot\Api\Types\ReplyKeyboardMarkup;

/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */
class Analytics
{

    public static function handle($message, $from, $bot): ?string
    {
        switch ($message) {
            case "anly" :
                $bot->sendMessage($from, "What analytics do you want to see ?\n`anly - Common Stats`\n`anly - Top 10 users`\n`anly - Message Stats`", "markdown");
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

    private static function getConnection(): PDO
    {
        $DB_SERVER = ANALYTICS_DB_HOST;
        $DB_NAME = ANALYTICS_DB_NAME;
        $DB_PASS = ANALYTICS_DB_PASSWORD;
        $DB_USER = ANALYTICS_DB_USERNAME;
        $conn = new PDO("mysql:host=$DB_SERVER;dbname=$DB_NAME", $DB_USER, $DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::loadMigrations($conn);
        return $conn;
    }

    public static function storeAnalyticsData($name, $username, $message): void
    {
        $query = self::getConnection()
            ->prepare("INSERT INTO analytics(name, username, message) VALUES(?,?,?)");
        $query->execute([$name, $username, $message]);
    }

    private static function getUsersFrequency($count): ?string
    {
        $query = self::getConnection()->query("SELECT * FROM analytics");
        $result = $query->fetchAll();
        if (!empty($result)) {
            $map = [];
            foreach ($result as $item) {
                $name = $item['name'];
                $username = $item['username'];
                if ($username === MASTER_USERNAME) {
                    continue;
                }
                $key = $name . '(@' . $username . ')';
                if (array_key_exists($key, $map)) {
                    $map[$key]++;
                } else {
                    $map[$key] = 1;
                }
            }
            $reply = "Here are the top 10 users of this bot\n\n";
            arsort($map);
            if (!empty($count)) {
                $map = array_slice($map, 0, min($count, count($map)));
            }
            foreach ($map as $key => $value) {
                $reply .= ($key . " - " . $value . "\n");
            }
            return $reply;
        }

        return "Opening Access Log Failed";
    }

    private static function commonStats(): string
    {
        $query = self::getConnection()->query("SELECT * FROM analytics");
        $result = $query->fetchAll();
        if (!empty($result)) {
            $totalCount = 0;
            $todayCount = 0;
            $todayUsersMap = [];
            foreach ($result as $item) {
                $date = $item['created_at'];
                $name = $item['name'];
                $username = $item['username'];
                if ($username === MASTER_USERNAME) {
                    continue;
                }
                if (explode(' ', $date)[0] === date('Y-m-d')) {
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
            $reply = "Here are some common stats of the bot\n\n";
            $reply .= "Total hits till date : $totalCount \n";
            $reply .= "Total hits today : $todayCount\n\n";
            if (!empty($todayUsersMap)) {
                $reply .= "\nHere are the top 10 users for today\n\n";
                arsort($todayUsersMap);
            }
            $map = array_slice($todayUsersMap, 0, min(10, count($todayUsersMap)));
            foreach ($map as $key => $value) {
                $reply .= ($key . " - " . $value . "\n");
            }
            return $reply;
        }

        return "Opening Access Log Failed";
    }

    private static function messageStats(): ?string
    {
        $query = self::getConnection()->query("SELECT * FROM analytics");
        $result = $query->fetchAll();
        if (!empty($result)) {
            $map = [];
            foreach ($result as $item) {
                $username = $item['username'];
                if ($username === MASTER_USERNAME) {
                    continue;
                }
                $key = self::extractFeature($item['message']);
                if (array_key_exists($key, $map)) {
                    $map[$key]++;
                } else {
                    $map[$key] = 1;
                }
            }
            $reply = "Here are the message stats\n\n";
            arsort($map);
            foreach ($map as $key => $value) {
                $reply .= ($key . " - " . $value . "\n\n");
            }
            return $reply;
        }

        return "Opening Access Log Failed";
    }

    private static function extractFeature($message): string
    {
        global $startKeyWords;
        if (in_array(trim($message), $startKeyWords)) {
            return "▶️ Start";
        }

        if (strpos($message, "act") !== false) {
            return "📅️ Academic Timetable";
        }

        if ((strpos($message, "qpapers") !== false) || (strpos($message, "qd") !== false)) {
            return "📚️ QPapers";
        }

        if ((strpos($message, "ft") !== false)) {
            return "👨‍🏫  Faculty Timetable";
        }

        if ((strpos($message, "news") !== false)) {
            return "📰️ News";
        }

        if ((strpos($message, "ums") !== false)) {
            return "💻️ AUMS";
        }

        if ((strpos($message, "thank") !== false)) {
            return "🙏️ Thanks";
        }

        return "💩️ Human Language";
    }

    private static function loadMigrations($conn): void
    {
        $migrations = [
            "CREATE TABLE IF NOT EXISTS `analytics` (
                        `id` integer PRIMARY KEY AUTO_INCREMENT,
                        `name` varchar(255) NOT NULL,
                        `username` varchar(255) DEFAULT NULL,
                        `message` text DEFAULT NULL,
                        `created_at` DATETIME DEFAULT NOW()
            );"
        ];

        $conn->beginTransaction();
        foreach ($migrations as $migration) {
            $conn->exec($migration);
        }
        $conn->commit();
    }
}
