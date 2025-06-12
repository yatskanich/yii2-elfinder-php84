<?php
/**
 * Date: 06.01.2017
 * Time: 23:11
 */

namespace mihaildev\elfinder;


class elFinderApi extends \elFinder
{
    public function __construct($opts, $plugins=[]) {
        parent::__construct($opts);

        $_req = $_SERVER["REQUEST_METHOD"] == 'POST' ? $_POST : $_GET;
        $_reqCmd = $_req['cmd'] ?? '';

        foreach($plugins as $plugin){
            /** @var PluginInterface $plugin */
            $plugin = \Yii::createObject($plugin);

            foreach ($plugin->bind as $cmd => $methods) {
                $doRegist = (str_contains((string)$cmd, '*'));
                if (! $doRegist) {
                    //$_getcmd = create_function('$cmd', 'list($ret) = explode(\'.\', $cmd);return trim($ret);');
                    $_getcmd = function ($cmd) {
                        [$ret] = explode('.', (string)$cmd);
                        return trim($ret);
                    };
                    $doRegist = ($_reqCmd && in_array($_reqCmd, array_map($_getcmd, explode(' ', (string)$cmd))));
                }
                if ($doRegist) {
                    if (! is_array($methods))
                        $methods = [$methods];

                    foreach($methods as $method){
                        if ($method && method_exists($plugin, $method)) {
                            if(!isset($this->plugins[$plugin->getName()]))
                                $this->plugins[$plugin->getName()] = $plugin;

                            $this->bind($cmd, [$this->plugins[$plugin->getName()], $method]);
                        }
                    }
                }

            }

        }
    }
}
