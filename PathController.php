<?php
/**
 * Date: 28.11.2014
 * Time: 14:21
 *
 * This file is part of the MihailDev project.
 *
 * (c) MihailDev project <http://github.com/mihaildev/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace mihaildev\elfinder;
use mihaildev\elfinder\volume\Local;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class PathController
 *
 * @package mihaildev\elfinder
 */
class PathController extends BaseController{
	public $disabledCommands = ['netmount'];
	public $root = [];
	public $watermark;

	private $_options;

    #[\Override]
    public function getOptions()
	{
		if($this->_options !== null)
			return $this->_options;

		$subPath = Yii::$app->request->getQueryParam('path', '');

		$this->_options['roots'] = [];

		$root = $this->root;

		if(is_string($root))
			$root = ['path' => $root];

		if(!isset($root['class']))
			$root['class'] = Local::className();

		if(!isset($root['path']))
			$root['path'] = '';

		if(!empty($subPath)){
            if (preg_match("/\./i", (string)$subPath)) {
				$root['path'] = rtrim($root['path'], '/');
			}
			else{
				$root['path'] = rtrim($root['path'], '/');
                $root['path'] .= '/' . trim((string)$subPath, '/');
			}
		}

		$root = Yii::createObject($root);

		/** @var Local $root*/

		if($root->isAvailable())
			$this->_options['roots'][] = $root->getRoot();

		if(!empty($this->watermark)){
			$this->_options['bind']['upload.presave'] = 'Plugin.Watermark.onUpLoadPreSave';

			if(is_string($this->watermark)){
				$watermark = [
					'source' => $this->watermark
				];
			}else{
				$watermark = $this->watermark;
			}

			$this->_options['plugin']['Watermark'] = $watermark;
		}

		$this->_options = ArrayHelper::merge($this->_options, $this->connectOptions);

		return $this->_options;
	}

    #[\Override]
    public function getManagerOptions()
    {
		$options = parent::getManagerOptions();
		$options['url'] = Url::toRoute(['connect', 'path' => Yii::$app->request->getQueryParam('path', '')]);
		return $options;
	}
} 