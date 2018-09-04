<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 20/08/2018
 * Time: 15:20
 */

namespace Starkerxp\CodeMigrator\Adapter;

use Starkerxp\CodeMigrator\AbstractAdapter;

class ValidNamespace extends AbstractAdapter
{

    /**
     * ObjectExtractor constructor.
     * @param array $baseNamespaces
     */
    public function __construct(array $baseNamespaces)
    {
        $this->baseNamespaces = $baseNamespaces;
    }

    public function run($file)
    {
        $tokens = $this->tokenise($file);
        $namespace = $this->getNamespace($file);
        $startNamespace = false;
        $currentNamespace = '';
        foreach ($tokens as $token) {
            $currentContent = is_array($token) ? $token[1] : $token;
            if ($token[0] === 'T_NAMESPACE') {
                $startNamespace = true;
            }
            $currentNamespace .= $currentContent;

            if ($startNamespace && $currentContent === ';') {
                $currentNamespace = trim($currentNamespace);
                break;
            }
        }

        if ($currentNamespace === $namespace) {
            return true;
        }
        if (empty($currentNamespace)) {
            $content = str_replace('<?php', "<?php\nnamespace $namespace;\n", $this->getContent($file));
            return $this->writeContent($file, $content);
        }

        return true;
    }

}
