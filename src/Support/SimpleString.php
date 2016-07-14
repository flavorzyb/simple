<?php

namespace Simple\Support;


class SimpleString
{
    /**
     * the slat for create password
     * @var string
     */
    protected $code = "simple_27$%jl0#sx2";

    /**
     * get the slat code
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * set slat code
     * @param $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * check the strings is the same or not
     * @param string $leftString
     * @param string $rightString
     * @return bool
     */
    public function isSameString($leftString, $rightString)
    {
        if (is_string($leftString) && is_string($rightString) &&
            strlen($leftString) == strlen($rightString) &&
            md5($leftString) == md5($rightString)
        )
        {
            return true;
        }

        return false;
    }

    /**
     * generate password for users
     * @param string $string
     * @return string
     */
    public function generatorPassword($string)
    {
        return md5($this->getCode().$string);
    }

    /**
     * generate password for admin users
     * @param string $string
     * @return string
     */
    public function generatorAdminPassword($string)
    {
        return md5($this->getCode().$string.$this->getCode());
    }
}
