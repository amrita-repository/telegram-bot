<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

use GuzzleHttp\Client;
use voku\helper\HtmlDomParser;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class News
{
    public static function handle($from, $message, $bot)
    {
        if ($message == "allnews" || $message == "/allnews") {
            foreach (self::getNews(true) as $news) {
                $keyboard = new InlineKeyboardMarkup([[['text' => 'Read more', 'url' => $news->getLink()]]]);
                $bot->sendMessage($from, self::toString($news), "html", false, null, null, $keyboard);
            }
        } else if ($message == "/randnews" || $message == "randnews" || strpos($message, "random") !== false) {
            $news = self::getNews();
            $keyboard = new InlineKeyboardMarkup([[['text' => 'Read more', 'url' => $news->getLink()]]]);
            $bot->sendMessage($from, self::toString($news), "html", false, null, null, $keyboard);
        } else {
            $bot->sendMessage($from, "Wanted news capsules regarding Amrita ? Right, choose an option.\n\n1) /allnews - Get all the articles at once (<strong>SPAM ALERT !!</strong>)\n\n2) /randnews - Get a random article", "html");
        }
    }

    public static function getNews($all = false)
    {
        $client = new Client();
        $res = $client->get("https://www.amrita.edu/campus/Coimbatore/news");
        $dom = HtmlDomParser::str_get_html($res->getBody()->__toString());
        $articles = $dom->find("article");
        $news_col = array();
        foreach ($articles as $article) {
            $title = $article->getElementsByTagName("h1", 0)->plaintext;
            $body = $article->getElementsByTagName("p", 0)->plaintext;
            $link = "https://www.amrita.edu" . $article->getElementsByTagName("a", 0)->href;
            array_push($news_col, new NewsModel($title, $body, $link));
        }
        if ($all) return $news_col;
        else return $news_col[array_rand($news_col)];
    }

    public static function toString($news)
    {
        $result = "";
        $result .= "<strong>" . $news->getTitle() . "</strong>\n\n";
        $result .= $news->getBody()->__toString() . "\n\n";
        return $result;
    }
}
