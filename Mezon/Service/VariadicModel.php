<?php
namespace Mezon\Service;

use Mezon\Conf\Conf;
use Mezon\Transport\RequestParams;

/**
 * Class VariadicModel
 *
 * @package ServiceModel
 * @subpackage VariadicModel
 * @author Dodonov A.A.
 * @version v.1.0 (2021/02/06)
 * @copyright Copyright (c) 2021, aeon.org
 */

/**
 * Base class for all controllers
 */
class VariadicModel extends ServiceModel
{

    /**
     * Real model
     *
     * @var object
     */
    private $realModel;

    /**
     * Config key to read settings
     *
     * @var string
     */
    protected $configKey = 'variadic-model-config-key';

    // TODO make a list of model types, because we can use variations not only for ...
    // ... local or remote calls, but also for blocking/non-blocking calls and so on
    /**
     * Local model class name
     *
     * @var string
     */
    protected $localModel = ServiceModel::class;

    /**
     * Remote model class name
     *
     * @var string
     */
    protected $remoteModel = ServiceModel::class;

    /**
     * Constructor
     *
     * @param mixed $model
     *            real model
     */
    public function __construct($model = null)
    {
        $modelSetting = Conf::getConfigValue($this->configKey, 'local');

        if ($model !== null) {
            $this->realModel = $model;
        } elseif ($modelSetting === 'local') {
            $this->realModel = new $this->localModel();
        } elseif ($modelSetting === 'remote') {
            $this->realModel = new $this->remoteModel();
        } elseif (is_string($modelSetting) && class_exists($modelSetting)) {
            $this->realModel = new $modelSetting();
        } elseif (is_object($modelSetting)) {
            $this->realModel = $modelSetting;
        } else {
            throw (new \Exception(
                'Can not construct model from value ' .
                (is_string($modelSetting) ? $modelSetting : serialize($modelSetting))));
        }
    }

    /**
     * Method returns real model
     *
     * @return object real model
     */
    public function getRealModel(): object
    {
        return $this->realModel;
    }

    /**
     * Method sets real model
     *
     * @param object $realModel
     *            real model
     */
    public function setRealModel(object $realModel): void
    {
        $this->realModel = $realModel;
    }
}
