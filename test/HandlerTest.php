<?php

namespace Wordpress\Type\Code\Test;

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Wordpress\Type\Code\Base\Handler;
use Wordpress\Type\Code\HandlerInterface;
use ReflectionClass;

class HandlerTest extends TestCase
{
    private HandlerInterface $handler;

    public function setUp(): void
    {
        if (! defined('ABSPATH')) {
            define('ABSPATH', sprintf('%s%s', __DIR__, '/../../../../'));
        }
        require_once(sprintf('%s%s', ABSPATH, 'wp-load.php'));
        $this->handler = new Handler(new Logger('Test'));
    }

    public function testShouldBeImplementHandlerInterface()
    {
        $this->assertInstanceOf(HandlerInterface::class, $this->handler);
    }

    public function testAdminNotice()
    {
        $this->assertNull($this->handler->adminNotice());
    }

    public function testShortCode()
    {
        $this->assertIsString($this->handler->addShortCode([]));
    }

    public function testGetData()
    {
        $reflection = new ReflectionClass(get_class($this->handler));
        $method = $reflection->getMethod('getResourceData');
        $method->setAccessible(true);
        $this->assertIsString($method->invokeArgs($this->handler, [WORDPRESS_TYPE_CODE_BASE_URL]));
    }

    public function testLoadTemplate()
    {
        $reflection = new ReflectionClass(get_class($this->handler));
        $method = $reflection->getMethod('loadTemplate');
        $method->setAccessible(true);
        $this->assertIsString($method->invokeArgs($this->handler, ['users', ['user' => 'test']]));
    }

    public function testEnqueueScripts()
    {
        $this->assertNull($this->handler->enqueueScripts());
    }
}
