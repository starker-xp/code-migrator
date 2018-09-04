<?php

namespace Starkerxp\CodeMigrator\Adapter;

use Starkerxp\CodeMigrator\AbstractAdapter;

class ReplaceContent extends AbstractAdapter
{

    private $search;
    private $to;

    /**
     * ReplaceContent constructor.
     * @param $search
     * @param $to
     */
    public function __construct($search, $to)
    {
        $this->search = $search;
        $this->to = $to;
    }

    public function run($file)
    {
        $codeBak = $code = $this->getContent($file);
        $code = str_replace($this->search, $this->to, $code);

        return $this->writeContent($file, $code, $codeBak);
    }

}
