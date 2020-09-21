<?php

namespace Wordpress\Type\Code;

/**
 * In software engineering, the singleton pattern is a software design pattern
 * that restricts the instantiation of a class to one "single" instance.
 *
 * @see https://en.wikipedia.org/wiki/Singleton_pattern
 */
trait SingletonTrait
{
    protected static FactoryInterface $instance;

    /**
     * Provides a single slot to hold an instance interchanble between all child classes.
     */
    public static function getInstance(): FactoryInterface
    {
        return isset(static::$instance) ? static::$instance : static::$instance = new static();
    }

    /**
     * Make constructor private to force protected implementation of the __constructor() method,
     * so that nobody can call directly "new Class()".
     */
    private function __construct()
    {
    }

    /**
     * Make wakeup magic method private, so nobody can unserialize instance.
     */
    private function __wakeup()
    {
    }

    /**
     * Make clone magic method private, so nobody can clone instance.
     */
    private function __clone()
    {
    }
}
