<?php
namespace Mezon\Service;

use Mezon\Conf\Conf;

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
     * @var ServiceModel
     */
    private $realModel;

    /**
     * Config key to read settings
     *
     * @var string
     */
    protected $configKey = 'variadic-model-config-key';

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
     * List of hiding models
     *
     * @var class-string[]
     */
    protected $models = [];

    /**
     * Trying to set real model
     *
     * @param object $realModel
     *            real model
     */
    private function trySetRealModel(object $realModel): void
    {
        if ($realModel instanceof ServiceModel) {
            $this->realModel = $realModel;
        } else {
            throw (new \Exception('Model must be derived from ServiceModel class', - 1));
        }
    }

    /**
     * Constructor
     *
     * @param ServiceModel $model
     *            real model
     * @psalm-suppress MixedMethodCall,
     */
    public function __construct(ServiceModel $model = null)
    {
        /** @var ?mixed $modelSetting */
        $modelSetting = Conf::getValue($this->configKey, 'default');

        if ($model !== null) {
            // set real model from constructor parameter
            $this->realModel = $model;
        } elseif (is_object($modelSetting)) {
            $this->trySetRealModel($modelSetting);
        } elseif (is_string($modelSetting) && isset($this->models[$modelSetting])) {
            // getting from $this->models
            $modelClassName = $this->models[$modelSetting];
            $this->trySetRealModel(new $modelClassName());
        } elseif ($modelSetting === 'default' || $modelSetting === 'local') {
            // use deprecated local model setting
            $this->trySetRealModel(new $this->localModel());
        } elseif ($modelSetting === 'remote') {
            // remote
            $this->trySetRealModel(new $this->remoteModel());
        } elseif (is_string($modelSetting) && class_exists($modelSetting)) {
            $this->trySetRealModel(new $modelSetting());
        } else {
            throw (new \Exception(
                'Can not construct model from value ' .
                (is_string($modelSetting) ? $modelSetting : serialize($modelSetting)),
                - 1));
        }
    }

    /**
     * Method returns real model
     *
     * @return ServiceModel real model
     */
    public function getRealModel(): ServiceModel
    {
        return $this->realModel;
    }

    /**
     * Method sets real model
     *
     * @param ServiceModel $realModel
     *            real model
     */
    public function setRealModel(ServiceModel $realModel): void
    {
        $this->realModel = $realModel;
    }
}
