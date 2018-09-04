<?php

namespace Starkerxp\CodeMigrator\Tests\Adapter;


use org\bovigo\vfs\vfsStream;
use Starkerxp\CodeMigrator\Adapter\ReplaceContent;

class ReplaceContentTest extends \PHPUnit_Framework_TestCase
{
    public function testA()
    {
        $baseContent = <<<EOF
<?php
class ModelUser
{
    public function getElement()
    {
        return 'oui';
    }
}
EOF;

        $directory = [
            'Model' => [
                'ModelUser.php' => $baseContent,
            ],
        ];

        $fileSystem = vfsStream::setup('/', 644, ['project' => $directory]);
        $adapter = new ReplaceContent('oui', 'non');
        $adapter->run($fileSystem->url() . '/project/Model/ModelUser.php');
        $content = file_get_contents($fileSystem->url() . '/project/Model/ModelUser.php');
        $this->assertEquals(str_replace('oui', 'non', $baseContent), $content);
    }
}


