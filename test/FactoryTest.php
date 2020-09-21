<?php

namespace Wordpress\Type\Code\Test;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Wordpress\Type\Code\FactoryInterface;
use Wordpress\Type\Code\Base\Factory;
use Wordpress\Type\Code\HandlerInterface;
use Wordpress\Type\Code\LoaderInterface;
use Wordpress\Type\Code\PluginInterface;

class FactoryTest extends TestCase
{
    public function provideFactory()
    {
        return [
            [Factory::getInstance()],
        ];
    }

    public function testUniqueness()
    {
        $firstCall = Factory::getInstance();
        $secondCall = Factory::getInstance();
        $this->assertInstanceOf(Factory::class, $firstCall);
        $this->assertSame($firstCall, $secondCall);
    }

    public function testShouldBeImplementFactoryInterface()
    {
        $this->assertInstanceOf(FactoryInterface::class, Factory::getInstance());
    }

    /**
     * @dataProvider provideFactory
     * @param FactoryInterface $factory
     */
    public function testCanCreateFactory(FactoryInterface $factory)
    {
        if (! defined('WORDPRESS_TYPE_CODE_SHORT_CODE')) {
            define('WORDPRESS_TYPE_CODE_SHORT_CODE', 'type-code');
            define('WORDPRESS_TYPE_CODE_LOG', sprintf('%s/%s', __DIR__, 'logs/app.log'));
        }
        $this->assertInstanceOf(LoaderInterface::class, $factory->getLoader());
        $this->assertInstanceOf(LoggerInterface::class, $factory->getLogger());
        $this->assertInstanceOf(PluginInterface::class, $factory->getPlugin());
        $this->assertInstanceOf(HandlerInterface::class, $factory->getHandler());
    }
}
