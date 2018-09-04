<?php

namespace Starkerxp\CodeMigrator;

class Runner
{
    use ListContents;

    private $baseDir;
    private $files = [];
    /**
     * @var AdapterInterface[]
     */
    private $adapters = [];

    /**
     * Runner constructor.
     * @param $baseDir
     */
    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    public function run()
    {
        foreach ($this->adapters as $adapter) {
            $files = $this->listContents($this->baseDir);
            foreach ($files as $file) {
                $adapter->setFiles($files);
                if (!$adapter->run($file)) {
                    throw  new \LogicException($adapter->getErrors());
                }
            }
        }
    }

    /**
     * @param AdapterInterface $adapter
     */
    public function addAdapter(AdapterInterface $adapter)
    {
        $this->adapters[] = $adapter;
    }


}
