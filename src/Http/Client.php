<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/21
 * Time: 上午9:21
 */

namespace Simple\Http;


class Client
{
    /**#@+
     * @const string METHOD constant names
     */
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';

    /**
     * http version
     */
    const HTTP_VERSION_10    = '1.0';
    const HTTP_VERSION_11    = '1.1';

    /**
     * default time out
     */
    const TIME_OUT      = 5;

    /**
     * @var string
     */
    protected $method       = self::METHOD_GET;

    /**
     * @var string
     */
    protected $uri          = '';
    /**
     * @var int
     */
    protected $timeout      = self::TIME_OUT;
    /**
     * @var int
     */
    protected $maxRedirects = 0;
    /**
     * @var string
     */
    protected $httpVersion  = self::HTTP_VERSION_11;

    /**
     * @var string
     */
    protected $response     = '';

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        switch($method)
        {
            case self::METHOD_GET:
            case self::METHOD_POST:
                $this->method = $method;
                break;
            default:
                break;
        }
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $this->toInt($timeout);
    }

    /**
     * @return int
     */
    public function getMaxRedirects()
    {
        return $this->maxRedirects;
    }

    /**
     * @param int $maxRedirects
     */
    public function setMaxRedirects($maxRedirects)
    {
        $this->maxRedirects = $this->toInt($maxRedirects);
    }

    /**
     * @return string
     */
    public function getHttpVersion()
    {
        return $this->httpVersion;
    }

    /**
     * @param string $httpVersion
     */
    public function setHttpVersion($httpVersion)
    {
        if (self::HTTP_VERSION_10 == $httpVersion) {
            $this->httpVersion = self::HTTP_VERSION_10;
        } else {
            $this->httpVersion = self::HTTP_VERSION_11;
        }
    }

    /**
     * to int
     * @param int $value
     * @return int
     */
    protected function toInt($value)
    {
        $value  = intval($value);
        return ($value > 0 ? $value : 0);
    }

    /**
     * exec
     */
    public function exec()
    {
    }
}
