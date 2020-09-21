<?php

namespace Wordpress\Type\Code\Base;

use Wordpress\Type\Code\FactoryInterface;
use Wordpress\Type\Code\SingletonTrait;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * In class-based programming, the factory method pattern is a creational pattern that uses factory methods to deal with
 * the problem of creating objects without having to specify the exact class of the object that will be created.
 * This is done by creating objects by calling a factory method—either specified in an interface and implemented by
 * child classes, or implemented in a base class and optionally overridden by derived classes—rather
 * than by calling a constructor.
 *
 * @see https://en.wikipedia.org/wiki/Factory_method_pattern
 */
class Factory implements FactoryInterface
{
    use SingletonTrait;

    public function getLoader(): Loader
    {
        return new Loader();
    }

    public function getPlugin(): Plugin
    {
        return new Plugin($this->getLoader(), $this->getHandler(), $this->getLogger());
    }

    public function getLogger(): Logger
    {
        $logger = new Logger(WORDPRESS_TYPE_CODE_SHORT_CODE);
        $logger->pushHandler(new StreamHandler(WORDPRESS_TYPE_CODE_LOG, Logger::WARNING));
        return $logger;
    }

    public function getHandler(): Handler
    {
        return new Handler($this->getLogger());
    }
}
