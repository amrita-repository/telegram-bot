<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

class NewsModel
{
    private $title;
    private $body;
    private $link;

    public function __construct($title, $body, $img)
    {

        $this->title = $title;
        $this->body = $body;
        $this->link = $img;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }
}
