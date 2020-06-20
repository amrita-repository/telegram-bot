<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 */

class RedisUtils
{
    /**
     * @var Predis\Client
     */
    private $instance = null;

    /**
     * RedisUtils constructor.
     */
    public function __construct()
    {
        if (empty($this->instance)) {
            $this->instance = new Predis\Client([
              'host' => REDIS_HOST,
              'port' => REDIS_PORT,
              'password' => REDIS_PW,
              'database' => REDIS_DB
            ]);
        }
    }

    /**
     * @param $key
     * @param $value
     * @param $expiry
     */
    public function setValue($key, $value, $expiry = 43200)
    {
        $this->instance->set($key, $value, "EX", $expiry);
    }

    /**
     * @param $key
     * @return string
     */
    public function get($key)
    {
        return $this->instance->get($key);
    }
}
