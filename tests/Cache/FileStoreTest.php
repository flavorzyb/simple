<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/26
 * Time: 下午8:45
 */

namespace Simple\Cache;

use Mockery as m;
use Simple\Filesystem\Filesystem;

class FileStoreTest extends StoreTest
{
    /**
     * @var Filesystem
     */
    protected $fileSystem = null;

    protected function setUp()
    {
        $this->fileSystem = new Filesystem();
        $this->setStore(new FileStore($this->fileSystem, TESTING_TMP_PATH,  $this->prefix));
        if (!$this->fileSystem->isDirectory(TESTING_TMP_PATH)) {
            $this->fileSystem->makeDirectory(TESTING_TMP_PATH);
        }
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->fileSystem->deleteDirectory(TESTING_TMP_PATH);
    }

    /**
     * @return FileStore
     */
    protected function getStore()
    {
        return $this->store;
    }

    public function testGetFileSystem()
    {
        $this->assertTrue($this->getStore()->getFileSystem() instanceof Filesystem);
    }

    public function testGetDirectory()
    {
        $this->assertEquals(TESTING_TMP_PATH, $this->getStore()->getDirectory());
    }

    public function testGetDataWithMock()
    {
        $fileSystem = m::mock('Simple\Filesystem\Filesystem');
        $fileSystem->shouldReceive('get')->andReturn('1000000000i:2222');
        $fileSystem->shouldReceive('isFile')->andReturn(true);
        $fileSystem->shouldReceive('exists')->andReturn(true);
        $fileSystem->shouldReceive('isDirectory')->andReturn(false);
        $fileSystem->shouldReceive('delete')->andReturn(true);
        $this->setStore(new FileStore($fileSystem, TESTING_TMP_PATH,  $this->prefix));

        $this->assertNull($this->getStore()->get("abc"));
    }
}
