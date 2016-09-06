<?php
namespace Simple\Validation;

class Validator {

    /**
     * validate name
     *
     * @param string $name
     * @return bool
     */
    public function validateName($name)
    {
        if (is_string($name) && strlen($name) > 1)
        {
            return true;
        }

        return false;
    }

    /**
     * validate email
     * @param string $email
     * @return bool
     */
    public function validateEmail($email)
    {
        // Split email address up and disallow '..'
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if ((strpos($email, '..') === false) && preg_match( $pattern, $email))
        {
            return true;
        }

        return false;
    }

    /**
     * validate password
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        if (is_string($password) && strlen($password) > 5)
        {
            return true;
        }

        return false;
    }

    /**
     * validate mobile number
     *
     * @param string $mobile
     * @return bool
     */
    public function validateMobile($mobile)
    {
        $mobile = trim($mobile);
        return 1 == preg_match('/^1[34578]{1}\d{9}$/',$mobile);
    }
}
