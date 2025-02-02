<?php

namespace Sokeio\Laravel;

use Closure;
use Illuminate\Support\Arr;

trait WithHookListener
{
    /**
     * Holds the event listeners
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * Adds a listener
     *
     * @param  string|array  $hook Hook name
     * @param  mixed  $callback Function to execute
     * @param  int  $priority Priority of the action
     * @param  int  $arguments Number of arguments to accept
     */
    public function addListener($hook, $callback, int $priority = 20, int $arguments = 1)
    {
        if (!is_array($hook)) {
            $hook = [$hook];
        }

        foreach ($hook as $hookName) {
            while (isset($this->listeners[$hookName][$priority])) {
                $priority += 1;
            }

            $this->listeners[$hookName][$priority] = compact('callback', 'arguments');
        }
    }

    /**
     * @param  string  $hook
     * @return $this
     */
    public function removeListener(string $hook): self
    {
        Arr::forget($this->listeners, $hook);

        return $this;
    }

    /**
     * Gets a sorted list of all listeners
     *
     * @return array
     */
    public function getListeners(): array
    {
        foreach ($this->listeners as $listeners) {
            uksort($listeners, function ($param1, $param2) {
                return strnatcmp($param1, $param2);
            });
        }

        return $this->listeners;
    }

    /**
     * Gets the function
     *
     * @param  mixed  $callback Callback
     * @return array|Closure|false|string A closure, an array if "class@method" or a string if "function_name"
     */
    protected function getFunction($callback)
    {
        if (is_string($callback) || $callback instanceof Closure || is_array($callback)) {
            if (is_string($callback) && strpos($callback, '@')) {
                $callback = explode('@', $callback);
                $callback = [app('\\' . $callback[0]), $callback[1]];
            }
            return $callback;
        }
        return false;
    }

    /**
     * Fires a new action
     *
     * @param  string  $action Name of action
     * @param  array  $args Arguments passed to the action
     */
    public function fire(string $action, array $args)
    {
    }
}
