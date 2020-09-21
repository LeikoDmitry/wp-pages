<?php

namespace Wordpress\Type\Code;

interface FactoryInterface
{
    public function getLoader(): LoaderInterface;
    public function getPlugin(): PluginInterface;
    public static function getInstance(): FactoryInterface;
}
