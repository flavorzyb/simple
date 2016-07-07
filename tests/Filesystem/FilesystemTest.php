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
    protected $basePath     = TESTING_TMP_PATH;

    /**
     * @var Filesystem
     */
    protected $fileSystem   = null;

    protected function setUp()
    {
        $this->fileSystem   = new Filesystem();

        if (!$this->fileSystem->isDirectory($this->basePath)) {
            $this->fileSystem->makeDirectory($this->basePath, 0755, true);
        }
    }

    protected function tearDown()
    {
        if ($this->fileSystem->isDirectory($this->basePath)) {
            $this->fileSystem->deleteDirectory($this->basePath, true);
        }
    }

    public function testGetRetrievesFiles()
    {
        file_put_contents($this->basePath . '/file.txt', 'Hello World');
        $files = new Filesystem;
        $this->assertEquals('Hello World', $files->get($this->basePath .'/file.txt'));
        @unlink($this->basePath . '/file.txt');
    }

    public function testPutStoresFiles()
    {
        $files = new Filesystem;
        $files->put($this->basePath . '/file.txt', 'Hello World');
        $this->assertEquals('Hello World', file_get_contents($this->basePath . '/file.txt'));
        @unlink($this->basePath . '/file.txt');
    }

    public function testDeleteRemovesFiles()
    {
        file_put_contents($this->basePath . '/file.txt', 'Hello World');
        $files = new Filesystem;
        $files->delete($this->basePath . '/file.txt');
        $this->assertFileNotExists($this->basePath . '/file.txt');
        @unlink($this->basePath . '/file.txt');
    }

    public function testPrependExistingFiles()
    {
        $files = new Filesystem;
        $files->put($this->basePath . '/file.txt', 'World');
        $files->prepend($this->basePath . '/file.txt', 'Hello ');
        $this->assertEquals('Hello World', file_get_contents($this->basePath . '/file.txt'));
        @unlink($this->basePath . '/file.txt');
    }

    public function testPrependNewFiles()
    {
        $files = new Filesystem;
        $files->prepend($this->basePath . '/file.txt', 'Hello World');
        $this->assertEquals('Hello World', file_get_contents($this->basePath . '/file.txt'));
        @unlink($this->basePath . '/file.txt');
    }

    /**
     * @expectedException \Simple\Filesystem\FileNotFoundException
     */
    public function testGetThrowsExceptionNonexisitingFile()
    {
        $files = new Filesystem;
        $files->get($this->basePath . '/unknown-file.txt');
    }

    public function testAppendAddsDataToFile()
    {
        file_put_contents($this->basePath . '/file.txt', 'foo');
        $files = new Filesystem;
        $bytesWritten = $files->append($this->basePath . '/file.txt', 'bar');
        $this->assertEquals(mb_strlen('bar', '8bit'), $bytesWritten);
        $this->assertFileExists($this->basePath . '/file.txt');
        $this->assertStringEqualsFile($this->basePath . '/file.txt', 'foobar');
        @unlink($this->basePath . '/file.txt');
    }
    public function testMoveMovesFiles()
    {
        file_put_contents($this->basePath . '/foo.txt', 'foo');
        $files = new Filesystem;
        $files->move($this->basePath . '/foo.txt', $this->basePath . '/bar.txt');
        $this->assertFileExists($this->basePath . '/bar.txt');
        $this->assertFileNotExists($this->basePath . '/foo.txt');
        @unlink($this->basePath . '/bar.txt');
    }
    public function testExtensionReturnsExtension()
    {
        file_put_contents($this->basePath . '/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertEquals('txt', $files->extension($this->basePath . '/foo.txt'));
        @unlink($this->basePath . '/foo.txt');
    }
    public function testTypeIndentifiesFile()
    {
        file_put_contents($this->basePath . '/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertEquals('file', $files->type($this->basePath . '/foo.txt'));
        @unlink($this->basePath . '/foo.txt');
    }

    public function testTypeIndentifiesDirectory()
    {
        @mkdir($this->basePath . '/foo');
        $files = new Filesystem;
        $this->assertEquals('dir', $files->type($this->basePath . '/foo'));
        @rmdir($this->basePath . '/foo');
    }

    public function testSizeOutputsSize()
    {
        $size = file_put_contents($this->basePath . '/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertEquals($size, $files->size($this->basePath . '/foo.txt'));
        @unlink($this->basePath . '/foo.txt');
    }
    public function testMimeTypeOutputsMimeType()
    {
        file_put_contents($this->basePath . '/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertEquals('text/plain', $files->mimeType($this->basePath . '/foo.txt'));
        @unlink($this->basePath . '/foo.txt');
    }
    public function testIsWritable()
    {
        file_put_contents($this->basePath . '/foo.txt', 'foo');
        $files = new Filesystem;
        @chmod($this->basePath . '/foo.txt', 0444);
        $this->assertFalse($files->isWritable($this->basePath . '/foo.txt'));
        @chmod($this->basePath . '/foo.txt', 0777);
        $this->assertTrue($files->isWritable($this->basePath . '/foo.txt'));
        @unlink($this->basePath . '/foo.txt');
    }

    public function testGlobFindsFiles()
    {
        file_put_contents($this->basePath . '/foo.txt', 'foo');
        file_put_contents($this->basePath . '/bar.txt', 'bar');
        $files = new Filesystem;
        $glob = $files->glob($this->basePath . '/*.txt');
        $this->assertContains($this->basePath . '/foo.txt', $glob);
        $this->assertContains($this->basePath . '/bar.txt', $glob);
        @unlink($this->basePath . '/foo.txt');
        @unlink($this->basePath . '/bar.txt');
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

        $executeFile = $this->basePath . '/file_execute.txt';
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
        $this->assertTrue($file->isDirectory($this->basePath));
    }

    public function testMakeDirectoryAndCleanDirectory()
    {
        $dir = $this->basePath . '/mytestdir';
        $file = new Filesystem();

        $file->deleteDirectory($dir);
        $this->assertTrue($file->makeDirectory($dir));

        $file->deleteDirectory($dir);

        $dir = $this->basePath . '/mytestdir/xxx/333/fff';
        $file->deleteDirectory($dir);
        $this->assertFalse($file->makeDirectory($dir, 0755, false, true));

        $file->deleteDirectory($dir);
        $this->assertTrue($file->makeDirectory($dir, 0755, true));
        $this->assertEquals($this->basePath . '/mytestdir/xxx/333', $file->dirName($dir));
        $file->put($dir.'test.log', 'aaaaaa');
        $file->cleanDirectory($this->basePath . '/mytestdir');
        $file->deleteDirectory($dir);
        $file->deleteDirectory($this->basePath . '/mytestdir');
    }

    public function testFilesReturnArray()
    {
        $dir = $this->basePath . '/mytestdir';
        $file = new Filesystem();

        $file->deleteDirectory($dir);
        $file->makeDirectory($dir);
        $file->put($dir . '/aaa.txt', 'aaaaa');
        $file->put($dir . '/bbbb.txt', 'aaaaa');

        $file->makeDirectory($dir.'/abcd');
        $file->put($dir . '/abcd/bbbb.txt', 'aaaaa');
        $file->put($dir . '/abcd/ccccc.txt', 'aaaaa');

        $this->assertEquals(2, sizeof($file->files($dir)));
        $this->assertEquals(4, sizeof($file->files($dir, true)));
        $file->deleteDirectory($dir);
    }

    public function testMd5FilePath() {
        $file = new Filesystem();
        $this->assertFalse($file->md5File($this->basePath . '/foo.txt'));

        file_put_contents($this->basePath . '/foo.txt', 'foo');
        $this->assertTrue(strlen($file->md5File($this->basePath . '/foo.txt')) > 0);
    }
}
