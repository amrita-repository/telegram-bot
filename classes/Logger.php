<?php
/*
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */


use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class Logger
{

    public static function error($message)
    {
        if (!empty(ROLLBAR_TOKEN)) {
            Rollbar::log(Level::ERROR, $message);
        }
    }

}
