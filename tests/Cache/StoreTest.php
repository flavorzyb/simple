<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 下午4:45
 */

namespace Simple\Cache;

abstract class StoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Store
     */
    protected $store    = null;

    /**
     * prefix
     * @var string
     */
    protected $prefix   = "sc_";

    protected function tearDown()
    {
        $this->store->flush();
    }

    public function testGetAndSet()
    {
        $key    = "1123";
        $value  = "aaaaa";

        $this->assertNull($this->store->get($key));
        $this->assertTrue($this->store->set($key, $value, 1000));
        $this->assertEquals($value, $this->store->get($key));

        $this->assertTrue($this->store->delete($key));
        $this->assertNull($this->store->get($key));
    }

    public function testMuSetAndGet()
    {
        $dataArray  = array(1=>123, 2=>2222, 3=>444);
        $keyArray   = array_keys($dataArray);

        $this->assertNull($this->store->mGet($keyArray));
        $this->assertTrue($this->store->mSet($dataArray, 1000));
        $this->assertEquals($dataArray, $this->store->mGet($keyArray));
    }

    public function testIncrementAndDecrement()
    {
        $key    = "aaaa";
        $value  = 3;

        $this->store->set($key, $value, 10000);
        $this->assertEquals($value + 1, $this->store->increment($key));
        $this->assertEquals($value, $this->store->decrement($key));
    }

    public function testPrefix()
    {
        $this->assertEquals($this->prefix, $this->store->getPrefix());
    }
}
