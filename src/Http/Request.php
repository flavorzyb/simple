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
     * get method
     */
    const GET = "GET";

    /**
     * post method
     */
    const POST = "POST";
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

    /**
     * is get request or not
     * @return bool
     */
    public static function isGetMethod() {
        return isset($_SERVER['REQUEST_METHOD']) && self::GET == $_SERVER['REQUEST_METHOD'];
    }

    /**
     * is post request or not
     *
     * @return bool
     */
    public static function isPostMethod() {
        return isset($_SERVER['REQUEST_METHOD']) && self::POST == $_SERVER['REQUEST_METHOD'];
    }
}
