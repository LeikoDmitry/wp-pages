<?php

namespace Wordpress\Type\Code;

interface HandlerInterface
{
    public const CALLBACK_PRIORITY = 10;
    public const CALLBACK_ACCEPTED_ARGS = 1;
    public function ajax(): string;
}
