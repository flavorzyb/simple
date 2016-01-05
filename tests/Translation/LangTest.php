<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/12/28
 * Time: 下午10:43
 */

namespace Simple\Translation;


class LangTest extends \PHPUnit_Framework_TestCase
{
    public function testOptions() {
        $lang = new Lang("zh_CN");
        $lang->setFallback("en");
        $lang->setLocale("zh_CN");
        $this->assertEquals("zh_CN", $lang->getLocale());
        $this->assertEquals("zh_CN", $lang->locale());
        $this->assertEquals("en", $lang->getFallback());
    }

    public function testGet() {
        $basePath = realpath(__DIR__ . '/../resources/lang');
        $lang = new Lang("zh_CN", $basePath);
        $lang->setFallback("en");
        $this->assertEquals($basePath, $lang->getPath());
        $lang->setPath($basePath);
        $this->assertEquals($basePath, $lang->getPath());

        $this->assertEquals("", $lang->get("error.no_exists_key"));
        $this->assertEquals("", $lang->get("error"));

        $this->assertEquals("错误的用户名", $lang->get("error.error_user_name"));
        $this->assertEquals("错误的uid", $lang->get("error.error_uid"));
        $this->assertEquals("错误信息", $lang->get("error.40010"));
        $this->assertEquals("error no in zh", $lang->get("error.no_in_zh"));

        $this->assertEquals("error user name", $lang->get("error.error_user_name", "en"));
        $this->assertEquals("error uid", $lang->get("error.error_uid", "en"));
        $this->assertEquals("error msg", $lang->get("error.40010", "en"));

        $lang = new Lang("en", $basePath);
        $this->assertEquals("error user name", $lang->get("error.error_user_name"));
        $this->assertEquals("error uid", $lang->get("error.error_uid"));
        $this->assertEquals("error msg", $lang->get("error.40010"));
    }
}
