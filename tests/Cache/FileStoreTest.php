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
    protected function setUp()
    {
        $fileSystem = new Filesystem();
        $this->setStore(new FileStore($fileSystem, TESTING_TMP_PATH,  $this->prefix));
        if (!$fileSystem->isDirectory(TESTING_TMP_PATH)) {
            $fileSystem->makeDirectory(TESTING_TMP_PATH);
        }
    }

    /**
     * @return MemcachedStore
     */
    protected function getStore()
    {
        return $this->store;
    }
}
