<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 上午9:40
 */

namespace Simple\Filesystem;


class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRetrievesFiles()
    {
        file_put_contents(__DIR__.'/file.txt', 'Hello World');
        $files = new Filesystem;
        $this->assertEquals('Hello World', $files->get(__DIR__.'/file.txt'));
        @unlink(__DIR__.'/file.txt');
    }

    public function testPutStoresFiles()
    {
        $files = new Filesystem;
        $files->put(__DIR__.'/file.txt', 'Hello World');
        $this->assertEquals('Hello World', file_get_contents(__DIR__.'/file.txt'));
        @unlink(__DIR__.'/file.txt');
    }

    public function testDeleteRemovesFiles()
    {
        file_put_contents(__DIR__.'/file.txt', 'Hello World');
        $files = new Filesystem;
        $files->delete(__DIR__.'/file.txt');
        $this->assertFileNotExists(__DIR__.'/file.txt');
        @unlink(__DIR__.'/file.txt');
    }

    public function testPrependExistingFiles()
    {
        $files = new Filesystem;
        $files->put(__DIR__.'/file.txt', 'World');
        $files->prepend(__DIR__.'/file.txt', 'Hello ');
        $this->assertEquals('Hello World', file_get_contents(__DIR__.'/file.txt'));
        @unlink(__DIR__.'/file.txt');
    }

    public function testPrependNewFiles()
    {
        $files = new Filesystem;
        $files->prepend(__DIR__.'/file.txt', 'Hello World');
        $this->assertEquals('Hello World', file_get_contents(__DIR__.'/file.txt'));
        @unlink(__DIR__.'/file.txt');
    }

    /**
     */
    public function testGetThrowsExceptionNonexisitingFile()
    {
        $files = new Filesystem;
        $this->setExpectedException('Simple\Filesystem\FileNotFoundException');
        $files->get(__DIR__.'/unknown-file.txt');
    }

    public function testAppendAddsDataToFile()
    {
        file_put_contents(__DIR__.'/file.txt', 'foo');
        $files = new Filesystem;
        $bytesWritten = $files->append(__DIR__.'/file.txt', 'bar');
        $this->assertEquals(mb_strlen('bar', '8bit'), $bytesWritten);
        $this->assertFileExists(__DIR__.'/file.txt');
        $this->assertStringEqualsFile(__DIR__.'/file.txt', 'foobar');
        @unlink(__DIR__.'/file.txt');
    }
    public function testMoveMovesFiles()
    {
        file_put_contents(__DIR__.'/foo.txt', 'foo');
        $files = new Filesystem;
        $files->move(__DIR__.'/foo.txt', __DIR__.'/bar.txt');
        $this->assertFileExists(__DIR__.'/bar.txt');
        $this->assertFileNotExists(__DIR__.'/foo.txt');
        @unlink(__DIR__.'/bar.txt');
    }
    public function testExtensionReturnsExtension()
    {
        file_put_contents(__DIR__.'/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertEquals('txt', $files->extension(__DIR__.'/foo.txt'));
        @unlink(__DIR__.'/foo.txt');
    }
    public function testTypeIndentifiesFile()
    {
        file_put_contents(__DIR__.'/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertEquals('file', $files->type(__DIR__.'/foo.txt'));
        @unlink(__DIR__.'/foo.txt');
    }

    public function testTypeIndentifiesDirectory()
    {
        @mkdir(__DIR__.'/foo');
        $files = new Filesystem;
        $this->assertEquals('dir', $files->type(__DIR__.'/foo'));
        @rmdir(__DIR__.'/foo');
    }

    public function testSizeOutputsSize()
    {
        $size = file_put_contents(__DIR__.'/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertEquals($size, $files->size(__DIR__.'/foo.txt'));
        @unlink(__DIR__.'/foo.txt');
    }
    public function testMimeTypeOutputsMimeType()
    {
        file_put_contents(__DIR__.'/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertEquals('text/plain', $files->mimeType(__DIR__.'/foo.txt'));
        @unlink(__DIR__.'/foo.txt');
    }
    public function testIsWritable()
    {
        file_put_contents(__DIR__.'/foo.txt', 'foo');
        $files = new Filesystem;
        @chmod(__DIR__.'/foo.txt', 0444);
        $this->assertFalse($files->isWritable(__DIR__.'/foo.txt'));
        @chmod(__DIR__.'/foo.txt', 0777);
        $this->assertTrue($files->isWritable(__DIR__.'/foo.txt'));
        @unlink(__DIR__.'/foo.txt');
    }

    public function testGlobFindsFiles()
    {
        file_put_contents(__DIR__.'/foo.txt', 'foo');
        file_put_contents(__DIR__.'/bar.txt', 'bar');
        $files = new Filesystem;
        $glob = $files->glob(__DIR__.'/*.txt');
        $this->assertContains(__DIR__.'/foo.txt', $glob);
        $this->assertContains(__DIR__.'/bar.txt', $glob);
        @unlink(__DIR__.'/foo.txt');
        @unlink(__DIR__.'/bar.txt');
    }

    public function testRealPath()
    {
        $realPath = realpath(__FILE__);
        $file = new Filesystem();
        $this->assertEquals($realPath, $file->realPath(__FILE__));
    }

    public function testCopy()
    {
        $file = new Filesystem();
        $this->assertTrue($file->copy(__FILE__, __FILE__.'.tmp'));
        @unlink(__FILE__.'.tmp');
    }

    public function testName()
    {
        $name = 'FilesystemTest';
        $file = new Filesystem();
        $this->assertEquals($name, $file->name(__FILE__));
    }

    public function testLastModified()
    {
        $file = new Filesystem();
        $this->assertTrue($file->lastModified(__FILE__) > 0);
    }

    public function testIsReadable()
    {
        $file = new Filesystem();
        $this->assertTrue($file->isReadable(__FILE__));
    }

    public function testIsExecutable()
    {
        $file = new Filesystem();

        $executeFile = __DIR__.'/file_execute.txt';
        @unlink($executeFile);
        file_put_contents($executeFile, 'Hello World');

        $this->assertFalse($file->isExecutable($executeFile));

        clearstatcache();
        @chmod($executeFile, 0777);
        $this->assertTrue($file->isExecutable($executeFile));
        @unlink($executeFile);
    }

    public function testIsDirectory()
    {
        $file = new Filesystem();
        $this->assertTrue($file->isDirectory(__DIR__));
    }

    public function testMakeDirectoryAndCleanDirectory()
    {
        $dir = __DIR__ . '/mytestdir';
        $file = new Filesystem();

        $file->deleteDirectory($dir);
        $this->assertTrue($file->makeDirectory($dir));

        $file->deleteDirectory($dir);

        $dir = __DIR__ . '/mytestdir/xxx/333/fff';
        $file->deleteDirectory($dir);
        $this->assertFalse($file->makeDirectory($dir, 0755, false, true));

        $file->deleteDirectory($dir);
        $this->assertTrue($file->makeDirectory($dir, 0755, true));
        $this->assertEquals(__DIR__ . '/mytestdir/xxx/333', $file->dirName($dir));
        $file->put($dir.'test.log', 'aaaaaa');
        $file->cleanDirectory(__DIR__ . '/mytestdir');
        $file->deleteDirectory($dir);
        $file->deleteDirectory(__DIR__ . '/mytestdir');
    }
}
