<?php
/**
 * Date: 23.01.14
 * Time: 22:47
 */

namespace mihaildev\elfinder\volume;

use Yii;

class Local extends Base
{
    public $path;

    public string $baseUrl = '@web';

    public string $basePath = '@webroot';

    public function getUrl(): string
    {
        return Yii::getAlias($this->baseUrl . '/' . trim((string)$this->path, '/'));
    }

    public function getRealPath(): string
    {
        $path = Yii::getAlias($this->basePath . '/' . trim((string)$this->path, '/'));

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    #[\Override]
    protected function optionsModifier(array $options): array
    {
        $options['path'] = $this->getRealPath();
        $options['URL'] = $this->getUrl();

        return $options;
    }
} 