<?php

namespace Wordpress\Type\Code;

interface LoaderInterface
{
    public function addAction(string $tag, array $callable, int $priority, int $accepted_args): void;
    public function addFilter(string $tag, array $callable, int $priority, int $accepted_args): void;
}
