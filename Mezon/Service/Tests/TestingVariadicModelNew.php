<?php
namespace Mezon\Service\Tests;

use Mezon\Service\VariadicModel;
use Mezon\Service\ServiceModel;

class TestingVariadicModelNew extends VariadicModel
{

    /**
     * Config key to read settings
     *
     * @var string
     */
    protected $configKey = 'variadic-model-config-key';

    /**
     * List of models class names
     *
     * @var string[]
     */
    protected $models = [
        'new' => ServiceModel::class
    ];
}
