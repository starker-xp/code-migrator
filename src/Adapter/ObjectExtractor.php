<?php

namespace Starkerxp\CodeMigrator\Adapter;

use Starkerxp\CodeMigrator\AbstractAdapter;

class ObjectExtractor extends AbstractAdapter
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

        $classes = [];
        $startClasses = false;
        $bracesOpen = 0;
        $bracesClose = 0;
        $contentClasses = '';
        $className = '';
        foreach ($tokens as $token) {
            $currentContent = is_array($token) ? $token[1] : $token;
            if (in_array($token[0], ['T_CLASS', 'T_INTERFACE', 'T_TRAIT'], true)) {
                $startClasses = true;
            }
            if ($currentContent === '{') {
                $bracesOpen++;
            }
            if ($currentContent === '}') {
                $bracesClose++;
            }
            if ($startClasses && empty($className) && $token[0] === 'T_STRING') {
                $className = $currentContent;
            }
            if ($startClasses) {
                $contentClasses .= $currentContent;
            }
            if ($bracesOpen && $bracesOpen === $bracesClose) {
                $classes[$namespace . '\\' . $className] = $contentClasses;
                $className = '';
                $contentClasses = '';
                $bracesOpen = 0;
                $bracesClose = 0;
                $startClasses = false;
            }
        }

        if ($bracesOpen !== $bracesClose) {
            throw new \LogicException('parse error');
        }
        $code = $this->getContent($file);

        foreach ($classes as $fqdn => $classContent) {
            $filename = $this->getFilename($classContent);
            if (file_exists($filename)) {
                throw new \LogicException('new file \'' . $filename . '\' already exist please check manually');
            }
            $content = $this->getContentOtherClass($code, $classes, $fqdn);
            if(!$this->writeContent($filename, $content, $code)){
                return false;
            }
        }

        // add require_once in first class

        return true;
    }

    private function getContentOtherClass($baseContent, $classes, $fqdn)
    {
        $export = $baseContent;
        foreach ($classes as $fqdnIdentifier => $content) {
            if ($fqdnIdentifier === $fqdn) {
                continue;
            }
            $export = str_replace($content, '', $export);

        }

        return $export;
    }

}
