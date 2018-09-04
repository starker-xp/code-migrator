<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 20/08/2018
 * Time: 11:59
 */

namespace Starkerxp\CodeMigrator;


trait ListContents
{
    /**
     * @param string $directory
     *
     * @return string[]
     */
    private function listContents($directory)
    {
        $files = [];
        $directoryIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($directoryIterator as $file) {
            if ($file->isDir()) {
                continue;
            }
            if (false !== strpos($file, '.git')) {
                continue;
            }
            $files[] = str_replace("\\", '/', $file->getPathname());
        }

        return $files;
    }
}
