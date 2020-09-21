<?php

namespace Wordpress\Type\Code\Test;

use PHPUnit\Framework\TestCase;
use Wordpress\Type\Code\Base\Loader;
use Wordpress\Type\Code\LoaderInterface;
use ArgumentCountError;

class LoaderTest extends TestCase
{
    private Loader $loader;

    public function setUp(): void
    {
        $this->loader = new Loader();
    }

    public function testShouldBeImplementLoaderInterface()
    {
        $this->assertInstanceOf(LoaderInterface::class, $this->loader);
    }

    public function testPropertiesShouldBeTypesArray()
    {
        $reflection = new \ReflectionObject($this->loader);
        $actions = $reflection->getProperty('actions');
        $actions->setAccessible(true);
        $filters = $reflection->getProperty('filters');
        $filters->setAccessible(true);
        $this->assertIsArray($actions->getValue($this->loader));
        $this->assertIsArray($filters->getValue($this->loader));
    }

    public function testAddNewActionWithException()
    {
        $this->expectException(ArgumentCountError::class);
        $this->loader->addAction();
    }

    public function testAddAction()
    {
        $this->assertNull($this->loader->addAction('test', [], 10, 1));
    }

    public function testAddNewFilterWithException()
    {
        $this->expectException(ArgumentCountError::class);
        $this->loader->addFilter();
    }

    public function testAddFilter()
    {
        $this->assertNull($this->loader->addFilter('test', [], 10, 1));
    }

    public function estAddNewActionsOrFilterToStoreWithException()
    {
        $this->expectException(ArgumentCountError::class);
        $reflection = new \ReflectionObject($this->loader);
        $method = $reflection->getMethod('add');
        $method->setAccessible(true);
        $method->invokeArgs($this->loader, []);
    }

    public function testAddNewActionsOrFilterToStore()
    {
        $reflection = new \ReflectionObject($this->loader);
        $method = $reflection->getMethod('add');
        $method->setAccessible(true);
        $this->assertIsArray($method->invokeArgs($this->loader, [
            'hooks'          => [],
            'hook'           => 'hook',
            'callable'       => [],
            'priority'       => 10,
            'accepted_args'  => 1,
        ]));
    }

    public function testAddedHooksAndFilters()
    {
        $this->assertNull($this->loader->run());
    }
}
