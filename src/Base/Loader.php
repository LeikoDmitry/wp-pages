<?php

namespace Wordpress\Type\Code\Base;

use Wordpress\Type\Code\LoaderInterface;

class Loader implements LoaderInterface
{
    protected array $actions;
    protected array $filters;

    public function __construct()
    {
        $this->actions = [];
        $this->filters = [];
    }

    public function addAction(string $tag, array $callable, int $priority, int $accepted_args): void
    {
        $this->actions = $this->add($this->actions, $tag, $callable, $priority, $accepted_args);
    }

    public function addFilter(string $tag, array $callable, int $priority, int $accepted_args): void
    {
        $this->filters = $this->add($this->filters, $tag, $callable, $priority, $accepted_args);
    }

    private function add(array $hooks, string $hook, array $callable, int $priority, int $accepted_args): array
    {
        $hooks[] = [
            'hook'          => $hook,
            'callback'      => $callable,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        ];
        return $hooks;
    }

    public function run(): void
    {
        if ($this->filters) {
            foreach ($this->filters as $hook) {
                add_filter($hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args']);
            }
        }
        if ($this->actions) {
            foreach ($this->actions as $hook) {
                add_action($hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args']);
            }
        }
    }
}
