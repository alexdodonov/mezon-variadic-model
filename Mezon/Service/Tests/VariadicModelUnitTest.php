<?php
namespace Mezon\Service\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Service\DbServiceModel;
use Mezon\Conf\Conf;
use Mezon\Service\VariadicModel;
use Mezon\Service\ServiceModel;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class VariadicModelUnitTest extends TestCase
{

    /**
     * Testing data provider
     *
     * @return array testing data
     */
    public function constructorDataProvider(): array
    {
        return [
            // #0, default value
            [
                function (): object {
                    Conf::deleteConfigValue('variadic-model-config-key');

                    return new TestingVariadicModel();
                },
                DbServiceModel::class
            ],
            // #1, local model
            [
                function (): object {
                    Conf::setConfigValue('variadic-model-config-key', 'local');

                    return new TestingVariadicModel();
                },
                DbServiceModel::class
            ],
            // #2, remote model
            [
                function (): object {
                    Conf::setConfigValue('variadic-model-config-key', 'remote');

                    return new TestingVariadicModel();
                },
                VariadicModel::class
            ],
            // #3, explicit model
            [
                function (): object {
                    return new TestingVariadicModel(new ServiceModel());
                },
                ServiceModel::class
            ],
            // #4, global model
            [
                function (): object {
                    Conf::setConfigValue('variadic-model-config-key', new ServiceModel());
                    return new TestingVariadicModel();
                },
                ServiceModel::class
            ],
            // #5, some other model
            [
                function (): object {
                    Conf::setConfigValue('variadic-model-config-key', ServiceModel::class);
                    return new TestingVariadicModel();
                },
                ServiceModel::class
            ],
            // #6, model from array of class names
            [
                function (): object {
                    Conf::setConfigValue('variadic-model-config-key', 'new');
                    return new TestingVariadicModelNew();
                },
                ServiceModel::class
            ]
        ];
    }

    /**
     * Testing method
     *
     * @param callable():VariadicModel $setup
     *            setup method
     * @param string $expected
     *            expected type
     * @psalm-param class-string $expected
     * @dataProvider constructorDataProvider
     */
    public function testConstructor(callable $setup, string $expected): void
    {
        // setup
        $model = $setup();

        // assertions
        $this->assertInstanceOf($expected, $model->getRealModel());
    }

    /**
     * Testing exception while passing invalid class name
     */
    public function testExceptionInvalidClassName(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('Can not construct model from value some model');

        // setup
        Conf::setConfigValue('variadic-model-config-key', 'some model');
        new TestingVariadicModel();
    }

    /**
     * Testing exception while passing invalid class name
     */
    public function testExceptionInvalidObjectType(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('Model must be derived from ServiceModel class');

        // setup
        Conf::setConfigValue('variadic-model-config-key', new \stdClass());
        new TestingVariadicModel();
    }

    /**
     * Testing method setRealModel
     */
    public function testSetRealModel(): void
    {
        // setup
        Conf::setConfigValue('variadic-model-config-key', ServiceModel::class);
        $model = new TestingVariadicModel();

        // test body
        $model->setRealModel(new DbServiceModel());

        // assertions
        $this->assertInstanceOf(DbServiceModel::class, $model->getRealModel());
    }
}
