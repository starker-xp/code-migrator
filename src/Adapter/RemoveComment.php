<?php

namespace Starkerxp\CodeMigrator\Adapter;

use Starkerxp\CodeMigrator\AbstractAdapter;

class RemoveComment extends AbstractAdapter
{
    public function run($file)
    {
        $tokens = $this->tokenise($file);
        $commentTokens = ['T_COMMENT', 'T_DOC_COMMENT', 'T_ML_COMMENT'];
        $newStr = '';

        foreach ($tokens as $token) {
            if (is_array($token)) {
                if (in_array($token[0], $commentTokens, false)) {
                    continue;
                }
                $token = $token[1];
            }
            $newStr .= $token;
        }

        return $this->writeContent($file, $newStr);
    }


}
