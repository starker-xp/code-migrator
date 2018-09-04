<?php

namespace Starkerxp\CodeMigrator;

abstract class AbstractAdapter implements AdapterInterface
{
    use ListContents;

    /**
     * @var string[]
     */
    protected $baseNamespaces;

    /**
     * @var string[]
     */
    private $errors;

    /**
     * @var string[]
     */
    private $files;

    private $basePath;

    /**
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $error
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @param string $file
     * @return array
     */
    protected function tokenise($file)
    {
        $code = $this->getContent($file);
        $tokens = token_get_all($code);
        foreach ($tokens as $key => $token) {
            if (is_array($token)) {
                $tokens[$key][0] = token_name($token[0]);
            }
        }

        return $tokens;
    }

    protected function getContent($file)
    {
        return file_get_contents($file);
    }

    protected function writeContent($file, $content, $contentBak = null)
    {
        if ($contentBak !== null && $content === $contentBak) {
            return true;
        }
        try {
            file_put_contents($file, $content);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string[] $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return string[]
     */
    public function getFiles()
    {
        return $this->files;
    }


    /**
     * @param mixed $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    protected function getNamespace($file)
    {
        $fqdn = '';
        foreach ($this->getFiles() as $element) {
            if ($element !== $file) {
                continue;
            }

            foreach ($this->baseNamespaces as $namespace => $baseDirectory) {
                if (false !== strpos($element, $baseDirectory)) {
                    $fqdn = str_replace([$baseDirectory, '/'], [$namespace . '\\', '\\'], $element);
                    break;
                }
            }
            break;
        }
        if (empty($fqdn)) {
            throw new \LogicException('can not find namespace');
        }

        $tmp = explode('\\', $fqdn);
        unset($tmp[count($tmp) - 1]);

        return implode('\\', $tmp);
    }


    public function getFilename($fqdn)
    {
        $tmp = explode('\\', $fqdn);
        $tmp[0] = $this->baseNamespaces[$tmp[0]];

        return implode('/', $tmp);
    }

    /**
     * @return mixed
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

}
