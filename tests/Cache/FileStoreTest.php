<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/26
 * Time: 下午8:45
 */

namespace Simple\Cache;


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
     * @return MemcachedStore
     */
    protected function getStore()
    {
        return $this->store;
    }
}
