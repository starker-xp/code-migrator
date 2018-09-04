<?php

namespace Starkerxp\CodeMigrator\Adapter;


use Starkerxp\CodeMigrator\AbstractAdapter;

class ChangeFileExtension extends AbstractAdapter
{
    private $old;
    private $new;

    public function __construct($old, $new)
    {
        $this->old = $old;
        $this->new = $new;
    }

    public function run($file)
    {
        if (!false !== strpos($file, $this->old)) {
            return true;
        }
        $newFilename = basename($file, '.') . $this->new;
        try {
            rename($file, $newFilename);
            $adapter = new ReplaceContent([$this->old], $this->new);
            $adapter->setFiles($this->getFiles());
            $adapter->setBasePath($this->getBasePath());
            $adapter->run($file);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

}
