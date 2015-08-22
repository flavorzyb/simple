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
     * RETRY COUNT
     */
    const RETRY_COUNT   = 3;

    /**
     * 证书的类型。支持的格式有"PEM" (默认值), "DER"和"ENG"。
     */
    const CERT_TYPE_PEM = "PEM";

    const CERT_TYPE_DER = "DER";

    const CERT_TYPE_ENG = "ENG";
    /**
     * @var string
     */
    protected $method       = self::METHOD_GET;

    /**
     * @var string
     */
    protected $url          = '';
    /**
     * post data array
     * @var array
     */
    protected $postDataArray = [];
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
     * response string
     * @var string
     */
    protected $response     = '';

    /**
     * response code
     * @var int
     */
    protected $responseCode = 0;

    /**
     * retry count
     *
     * @var int
     */
    protected $retryCount   = self::RETRY_COUNT;

    /**
     * SSL证书认证
     * @var bool
     */
    protected $sslVerifyPeer    = false;
    /**
     * 严格认证
     * @var bool
     */
    protected $sslVerifyHost    = false;
    /**
     * 证书地址
     * @var string
     */
    protected $caInfo           = "";

    /**
     * cert type
     * @var string
     */
    protected $sslCertType      = self::CERT_TYPE_PEM;

    /**
     * cert file path
     * @var string
     */
    protected $sslCert          = "";

    /**
     * ssl key type
     * @var string
     */
    protected $sslKeyType       = self::CERT_TYPE_PEM;

    /**
     * ssl key path
     * @var string
     */
    protected $sslKey           = "";

    /**
     * use cert or not
     * @var bool
     */
    protected $useCert          = false;

    /**
     * user anent
     *
     * @var string
     */
    protected $userAgent        = "";
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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     * @return int
     */
    public function getRetryCount()
    {
        return $this->retryCount;
    }

    /**
     * @param int $retryCount
     */
    public function setRetryCount($retryCount)
    {
        $this->retryCount = $this->toInt($retryCount);
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * convert method to curl options array
     * ['method' => CURLOPT_HTTPGET, 'value' => true];
     *
     * @param string $method
     * @return array ['method' => CURLOPT_HTTPGET, 'value' => true];
     */
    protected function getCurlMethod($method)
    {
        $result = ['method' => CURLOPT_HTTPGET, 'value' => true];
        if (self::METHOD_POST == $method) {
            $result['method'] = CURLOPT_POST;
        }

        return $result;
    }

    /**
     * get post data
     * @return array
     */
    public function getPostDataArray()
    {
        return $this->postDataArray;
    }

    /**
     * set post data
     * @param array $data
     */
    public function setPostDataArray(array $data)
    {
        $this->postDataArray = $data;
    }

    /**
     * get response code
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * SSL证书认证
     * @return boolean
     */
    public function isSslVerifyPeer()
    {
        return $this->sslVerifyPeer;
    }

    /**
     * SSL证书认证
     * @param boolean $sslVerifyPeer
     */
    public function setSslVerifyPeer($sslVerifyPeer)
    {
        $this->sslVerifyPeer = boolval($sslVerifyPeer);
    }

    /**
     * 严格认证
     * @return boolean
     */
    public function isSslVerifyHost()
    {
        return $this->sslVerifyHost;
    }

    /**
     * 严格认证
     * @param boolean $sslVerifyHost
     */
    public function setSslVerifyHost($sslVerifyHost)
    {
        $this->sslVerifyHost = intval($sslVerifyHost);
    }

    /**
     * @return string
     */
    public function getCaInfo()
    {
        return $this->caInfo;
    }

    /**
     * @param string $caInfo
     */
    public function setCaInfo($caInfo)
    {
        $this->caInfo = $caInfo;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return boolean
     */
    public function isUseCert()
    {
        return $this->useCert;
    }

    /**
     * @param boolean $useCert
     */
    public function setUseCert($useCert)
    {
        $this->useCert = $useCert;
    }

    /**
     * use cert
     * @param string $certType
     * @param string $certPath
     * @param string $keyPath
     */
    public function useCert($certType, $certPath, $keyPath)
    {
        switch ($certType) {
            case self::CERT_TYPE_PEM:
            case self::CERT_TYPE_DER:
            case self::CERT_TYPE_ENG:
                $this->useCert      = true;
                $this->sslCertType  = $certType;
                $this->sslKeyType   = $certType;

                $this->sslCert      = $certPath;
                $this->sslKey       = $keyPath;
                break;
            default:
                $this->useCert = false;
                break;
        }
    }

    /**
     * exec
     * @return boolean
     */
    public function exec()
    {
        $result = false;

        $methodArray = $this->getCurlMethod($this->method);

        while (true) {
            $ch   = curl_init();

            curl_setopt($ch, CURLOPT_HTTP_VERSION, $this->httpVersion);
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

            if ($this->maxRedirects > 0) {
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_MAXREDIRS, $this->maxRedirects);
            } else {
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            }

            // user agent
            if ("" != $this->userAgent) {
                curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
            }

            // ssl opt
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerifyPeer);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslVerifyHost);

            if ($this->sslVerifyPeer && $this->sslVerifyHost) {
                curl_setopt($ch, CURLOPT_CAINFO, $this->caInfo);
            }

            // use cert
            if (true == $this->useCert) {
                curl_setopt($ch, CURLOPT_SSLCERTTYPE,   $this->sslCertType);
                curl_setopt($ch, CURLOPT_SSLCERT,       $this->sslCert);
                curl_setopt($ch, CURLOPT_SSLKEYTYPE,    $this->sslKeyType);
                curl_setopt($ch, CURLOPT_SSLKEY,        $this->sslKey);
            }

            //method and data
            curl_setopt($ch, $methodArray['method'], $methodArray['value']);

            if ((CURLOPT_POST == $methodArray['method']) && (!empty($this->postDataArray))) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->postDataArray));
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);

            if (!curl_errno($ch)) {
                $this->responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            }

            curl_close($ch);

            if (false !== $result) {
                break;
            }

            $this->retryCount -- ;
            if ($this->retryCount < 1) {
                break;
            }
        }

        $this->response     =  $result;
        return (false !== $result);
    }
}
