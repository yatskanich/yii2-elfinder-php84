<?php

/**
 * Date: 28.03.19
 * Time: 10:39
 */

namespace mihaildev\elfinder\volume;

use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * @property array defaults
 */
class Base extends BaseObject
{
    /**
     * @var string
     */
    public string $driver = 'LocalFileSystem';

    public string|array $name = 'Root';

    /**
     * @var array
     */
    public array $options = [];

    /**
     * @var array
     */
    public array $access = ['read' => '*', 'write' => '*'];

    /**
     * @var string
     */
    public ?string $tmbPath = null;

    /**
     * @var array
     */
    public array $plugin = [];

    /**
     * @var array
     */
    private array $_defaults = [];

    /**
     * @return string
     */
    public function getAlias()
    {
        if (is_array($this->name)) {
            return Yii::t($this->name['category'], $this->name['message']);
        }

        return $this->name;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->defaults['read'];
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        if ($this->_defaults !== null) {
            return $this->_defaults;
        }

        $this->_defaults['read'] = false;
        $this->_defaults['write'] = false;

        if (isset($this->access['write'])) {
            $this->_defaults['write'] = true;
            if ($this->access['write'] != '*') {
                $this->_defaults['write'] = Yii::$app->user->can($this->access['write']);
            }
        }

        if ($this->_defaults['write']) {
            $this->_defaults['read'] = true;
        } elseif (isset($this->access['read'])) {
            $this->_defaults['read'] = true;
            if ($this->access['read'] != '*') {
                $this->_defaults['read'] = Yii::$app->user->can($this->access['read']);
            }
        }

        return $this->_defaults;
    }

    /**
     * @param array $options
     * @return array
     */
    protected function optionsModifier(array $options): array
    {
        return $options;
    }

    /**
     * @return array
     */
    public function getRoot(): array
    {
        $options['driver'] = $this->driver;
        $options['plugin'] = $this->plugin;
        $options['defaults'] = $this->getDefaults();
        $options['alias'] = $this->getAlias();

        $options['tmpPath'] = Yii::getAlias('@runtime/elFinderTmpPath');
        if (!empty($this->tmbPath)) {
            $this->tmbPath = trim($this->tmbPath, '/');
            $options['tmbPath'] = Yii::getAlias('@webroot/' . $this->tmbPath);
            $options['tmbURL'] = Yii::$app->request->hostInfo . Yii::getAlias('@web/' . $this->tmbPath);
        } else {
            $subPath = md5($this->className() . '|' . serialize($this->name));
            $options['tmbPath'] = Yii::$app->assetManager->getPublishedPath(__DIR__) . DIRECTORY_SEPARATOR . $subPath;
            $options['tmbURL'] = Yii::$app->request->hostInfo . Yii::$app->assetManager->getPublishedUrl(
                    __DIR__
                ) . '/' . $subPath;
        }

        FileHelper::createDirectory($options['tmbPath']);


        $options['mimeDetect'] = 'internal';
        $options['imgLib'] = 'auto';
        $options['attributes'][] = [
            'pattern' => '#.*(\.tmb|\.quarantine)$#i',
            'read' => false,
            'write' => false,
            'hidden' => true,
            'locked' => false,
        ];

        $options = $this->optionsModifier($options);

        return ArrayHelper::merge($options, $this->options);
    }
}
