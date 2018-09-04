<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 20/08/2018
 * Time: 11:29
 */

namespace Starkerxp\CodeMigrator;

interface AdapterInterface
{
    public function run($file);

    public function getErrors();

    public function setFiles($files);

    public function setBasePath($basePath);
}
