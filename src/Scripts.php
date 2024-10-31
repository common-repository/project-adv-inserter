<?php

namespace Sferica\Plugins\ProjectAdvInserter;

use Exception;

class Scripts
{
    private $scripts;

    public function __construct()
    {
        $configClass = Config::getInstance();
        $this->scripts = $configClass->config['scripts'];
        $this->methods = [
            'header' => 'position',
            'footer' => 'position',
        ];
    }

    public function __call($name, $arguments)
    {        
        if (array_key_exists($name, $this->methods)) {
            $this->scripts = array_filter($this->scripts, function($script) use ($name) {
                return $script[$this->methods[$name]] == $name;
            });
            return $this;
        } else {
            throw new Exception("Method $name not found in class Slots");
        }
    }

    public function get()
    {
        usort($this->scripts, function($a, $b) {
            $order_a = $a['order'] ? $a['order'] : 0;
            $order_b = $b['order'] ? $b['order'] : 0;
            return $order_a < $order_b;
        });

        return $this->scripts;
    }
}