<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/24
 * Time: 上午12:45
 */

namespace Simple\Http;


class Request
{
    /**
     * get value
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (is_null($key)) {
            return $default;
        }

        return (isset($_GET[$key]) ? $_GET[$key] : $default);
    }

    /**
     * post value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function post($key, $default = null)
    {
        if (is_null($key)) {
            return $default;
        }

        return (isset($_POST[$key]) ? $_POST[$key] : $default);
    }
}
