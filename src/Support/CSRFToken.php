<?php
namespace Simple\Support;

class CSRFToken
{
    /**
     * @var string
     */
    protected $csrfTokenString = 'X-CSRF-TOKEN';

    /**
     * set csrf token key string
     *
     * @param string $code
     */
    public function setTokenKey($code)
    {
        $this->csrfTokenString = trim($code);
    }

    /**
     * get csrf token key string
     *
     * @return string
     */
    public function getTokenKey()
    {
        return $this->csrfTokenString;
    }

    /**
     * get csrf token string
     * @return string
     */
    public function getCSRFString()
    {
        return (isset($_SESSION[$this->csrfTokenString]) ? trim($_SESSION[$this->csrfTokenString]) : '');
    }

    /**
     * register csrf string
     */
    public function registerCSRFString()
    {
        mt_srand(time());
        $string = md5(mt_rand().'_'.time().'_'.mt_rand().'_'.mt_rand());
        $_SESSION[$this->csrfTokenString] = $string;
    }

    /**
     * has token
     * @return bool
     */
    public function hasToken()
    {
        return isset($_SESSION[$this->csrfTokenString]);
    }

    /**
     * validate csrf string
     *
     * @param string $str
     * @return bool
     */
    public function matchCSRFString($str)
    {
        $str = trim($str);
        return (('' != $str) && ($str == $this->getCSRFString()));
    }

    /**
     * clean csrf string
     */
    public function cleanCSRFString() {
        if (isset($_SESSION[$this->csrfTokenString])) {
            unset($_SESSION[$this->csrfTokenString]);
        }
    }
}