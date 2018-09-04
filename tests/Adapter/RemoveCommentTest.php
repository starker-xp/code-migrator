<?php

namespace Starkerxp\CodeMigrator\Tests\Adapter;


use org\bovigo\vfs\vfsStream;
use Starkerxp\CodeMigrator\Adapter\RemoveComment;

class RemoveCommentTest extends \PHPUnit_Framework_TestCase
{
    public function testA()
    {
        $content = <<<EOF
<?php
class ModelUser
{
    /**
     * First comment
     */
    public function getElement()
    {
        // Second comment
        return 'oui';
        /**
         * Thrid comment
         */
    }
}
# Fourth comment
EOF;

        $directory = [
            'Model' => [
                'ModelUser.php' => $content,
            ],
        ];

        $fileSystem = vfsStream::setup('/', 644, ['project' => $directory]);
        $adapter = new RemoveComment();
        $adapter->run($fileSystem->url() . '/project/Model/ModelUser.php');
        $content = file_get_contents($fileSystem->url() . '/project/Model/ModelUser.php');
        $content = str_replace(["\n", "\r", "\t", '  ', '   '], '', $content);
        $this->assertEquals("<?phpclass ModelUser{public function getElement(){return 'oui';}}", $content);
    }
}


