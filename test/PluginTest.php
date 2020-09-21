<?php

namespace Wordpress\Type\Code\Test;

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Wordpress\Type\Code\Base\Handler;
use Wordpress\Type\Code\Base\Loader;
use Wordpress\Type\Code\Base\Plugin;
use Wordpress\Type\Code\PluginInterface;
use Wordpress\Type\Code\Exception\Plugin as PluginException;
use ArgumentCountError;
use ReflectionClass;

class PluginTest extends TestCase
{
    protected Plugin $plugin;
    protected Logger $logger;
    protected Loader $loader;
    protected Handler $handler;

    public function setUp(): void
    {
        if (! defined('ABSPATH')) {
            define('ABSPATH', sprintf('%s%s', __DIR__, '/../../../../'));
        }
        require_once(sprintf('%s%s', ABSPATH, 'wp-load.php'));
        $this->logger = new Logger('Test');
        $this->loader = new Loader();
        $this->handler = new Handler($this->logger);
        $this->plugin = new Plugin($this->loader, $this->handler, $this->logger);
    }

    public function testShouldBeImplementPluginInterface()
    {
        $this->assertInstanceOf(PluginInterface::class, $this->plugin);
    }

    public function testConstructorShouldAcceptLoaderHandlerLoggerDependencies()
    {
        $plugin = new Plugin($this->loader, $this->handler, $this->logger);
        $this->assertIsObject($plugin);
    }

    public function testConstructorShouldRequireArguments()
    {
        $this->expectException(ArgumentCountError::class);
        new Plugin();
    }

    public function testRunApplication()
    {
        $this->assertNull($this->plugin->run());
    }

    public function testPluginActivate()
    {
        $reflection = new ReflectionClass(get_class($this->plugin));
        $method = $reflection->getMethod('pluginActivate');
        $method->setAccessible(true);
        $this->assertNull($method->invokeArgs($this->plugin, []));
    }

    public function testPluginDeActivate()
    {
        $reflection = new ReflectionClass(get_class($this->plugin));
        $method = $reflection->getMethod('pluginDeactivate');
        $method->setAccessible(true);
        $this->assertNull($method->invokeArgs($this->plugin, []));
    }

    public function testCreateShortCode()
    {
        $reflection = new \ReflectionClass(get_class($this->plugin));
        $method = $reflection->getMethod('addShortCode');
        $method->setAccessible(true);
        $this->assertNull($method->invokeArgs($this->plugin, []));
    }

    public function testTryCreateThePageWithTable()
    {
        $this->assertNull($this->plugin->createPage());
    }

    public function testTryThrowException()
    {
        $reflection = new \ReflectionClass(get_class($this->plugin));
        $method = $reflection->getMethod('throwException');
        $method->setAccessible(true);
        $this->expectException(PluginException::class);
        $method->invokeArgs($this->plugin, [new PluginException('Test Message')]);
    }

    public function testTryCreateNewPage()
    {
        $reflection = new \ReflectionClass(get_class($this->plugin));
        $method = $reflection->getMethod('tryCreatePage');
        $method->setAccessible(true);
        $this->assertNull($method->invokeArgs($this->plugin, [[]]));
    }

    public function testTryGetPageByTitle()
    {
        $reflection = new \ReflectionClass(get_class($this->plugin));
        $method = $reflection->getMethod('getPageByTitle');
        $method->setAccessible(true);
        $this->assertIsObject($method->invokeArgs($this->plugin, [WORDPRESS_TYPE_CODE_SHORT_PAGE_TITLE]));
    }

    public function testTryDeletePageById()
    {
        $reflection = new \ReflectionClass(get_class($this->plugin));
        $method = $reflection->getMethod('tryDeletePage');
        $method->setAccessible(true);
        $this->assertIsBool($method->invokeArgs($this->plugin, [0]));
    }

    public function testTryRemoveThePageWithTable()
    {
        $this->assertNull($this->plugin->removePage());
    }

    public function testDefineHooks()
    {
        $reflection = new \ReflectionClass(get_class($this->plugin));
        $method = $reflection->getMethod('defineHooks');
        $method->setAccessible(true);
        $this->assertNull($method->invokeArgs($this->plugin, []));
    }
}
