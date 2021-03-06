<?php
namespace Simple\Log;

use PHPUnit\Framework\TestCase;
use Simple\Filesystem\Filesystem;

use Mockery as m;

class WriterTest extends TestCase
{
    /**
     * @var Writer
     */
    private $log        = null;
    /**
     * @var Filesystem
     */
    private $fileSystem = null;
    private $logDir     = TESTING_TMP_PATH . DIRECTORY_SEPARATOR . 'test_logs';

    protected function setUp()
    {
        $this->fileSystem   = new Filesystem();
        $this->log          = new Writer($this->fileSystem);
        if (!$this->fileSystem->isDirectory($this->logDir)) {
            $this->fileSystem->makeDirectory($this->logDir, 0755, true);
        }
    }

    protected function tearDown()
    {
        $this->fileSystem->deleteDirectory(TESTING_TMP_PATH);
    }

    public function testLog()
    {
        $this->assertNull($this->log->dirPath());
        $this->assertEquals($this->fileSystem, $this->log->getFilesystem());
        $this->assertFalse($this->log->api("this is a test debug"));

        $this->log->setDirPath($this->logDir);
        $this->assertEquals($this->logDir, $this->log->dirPath());

        $this->assertTrue($this->log->debug("this is a test debug"));
        $this->assertTrue($this->log->info("this is a test info"));
        $this->assertTrue($this->log->notice("this is a test notice"));
        $this->assertTrue($this->log->warning("this is a test warning"));
        $this->assertTrue($this->log->error("this is a test error"));
        $this->assertTrue($this->log->api("this is a test api"));
        $this->assertTrue($this->log->db("this is a test db"));

        $this->assertFalse($this->log->debug(""));

        $fileSystem = m::mock("Simple\\Filesystem\\Filesystem");
        $fileSystem->shouldReceive("size")->andReturn(Writer::MAX_FILE_SIZE + 1000);
        $fileSystem->shouldReceive("isDirectory")->andReturn(true);
        $fileSystem->shouldReceive("realPath")->andReturn($this->logDir);
        $fileSystem->shouldReceive("isFile")->andReturn(true);
        $fileSystem->shouldReceive("move")->andReturn(true);
        $fileSystem->shouldReceive("dirName")->andReturn($this->logDir . DIRECTORY_SEPARATOR . date('Y/M'));
        $fileSystem->shouldReceive("append")->andReturn(true);
        $log        = new Writer($fileSystem);
        $log->setDirPath($this->logDir);
        $this->assertTrue($log->api("this is a test debug"));
    }
}
