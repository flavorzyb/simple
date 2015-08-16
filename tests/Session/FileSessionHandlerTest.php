<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 下午10:17
 */

namespace Simple\Session;

use Simple\Filesystem\Filesystem;
use Mockery as m;

class FileSessionHandlerTest extends SessionHandlerTest
{
    /**
     * @var Filesystem
     */
    protected $fileSystem   = null;

    protected $path         = null;

    protected function setUp()
    {
        parent::setUp();
        $this->fileSystem   = new Filesystem();
        $this->path         = TESTING_TMP_PATH;

        if (!$this->fileSystem->isDirectory($this->path)) {
            $this->fileSystem->makeDirectory($this->path);
        }

        $this->setSessionHandler(new FileSessionHandler($this->fileSystem, $this->path));
    }

    protected function tearDown()
    {
        $this->fileSystem->deleteDirectory($this->path);
    }

    public function testGc()
    {
        $fileSystem = m::mock("Simple\\Filesystem\\Filesystem");
        $fileSystem->shouldReceive("files")->andReturn(array(TESTING_TMP_PATH . '/test1', TESTING_TMP_PATH . '/test2'));
        $fileSystem->shouldReceive("lastModified")->andReturn(time() - 1000);
        $fileSystem->shouldReceive("delete")->andReturn(true);

        $this->setSessionHandler(new FileSessionHandler($fileSystem, $this->path));
        $this->assertTrue($this->sessionHandler->gc(100));
    }
}
