<?php

namespace Starkerxp\CodeMigrator\Adapter;

use Starkerxp\CodeMigrator\AbstractAdapter;

class ConvertRequireToUseForHektor extends AbstractAdapter
{
    const REGEX_REQUIRE_CLASS = "#require_once PATHADMIN(?:| )\.(?:| )[\'\"](.*?)\.class\.php[\'\"]\;#";

    public function run($file)
    {
        $content = $contentBak = $this->getContent($file);

        preg_match_all(self::REGEX_REQUIRE_CLASS, $content, $outRequire);
        foreach ($outRequire[0] as $key => $namespaceBrut) {
            $tmpNamespace = explode('.', str_replace('/', "\\", 'use Hektor\\' . $outRequire[1][$key]))[0] . ';';
            $content = str_replace($namespaceBrut, $tmpNamespace, $content);
        }

        return $this->writeContent($file, $content, $contentBak);
    }

}
