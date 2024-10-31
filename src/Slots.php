<?php

namespace Sferica\Plugins\ProjectAdvInserter;

use Exception;

class Slots
{
    private $slots;

    private $methods;

    public function __construct()
    {
        $configClass = Config::getInstance();
        $this->slots = $configClass->config['slots'];
        $this->methods = [
            'index' => 'page_type',
            'article' => 'page_type',
            'archive' => 'page_type',
            'start' => 'position',
            'end' => 'position',
            'before' => 'position',
            'after' => 'position'
        ];
    }

    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->methods)) {
            $need_paragraph = ['before', 'after'];
            $this->slots = array_filter($this->slots, function($slot) use ($name, $need_paragraph) {
                if (in_array($name, $need_paragraph)) {
                    return $slot[$this->methods[$name]] == $name && isset($slot['paragraph']);
                }
                return $slot[$this->methods[$name]] == $name;
            });
            return $this;
        } else {
            throw new Exception("Method $name not found in class Slots");
        }
    }

    public function content()
    {
        $this->slots = array_filter($this->slots, function($slot) {
            return $slot['in_content'];
        });
        return $this;
    }

    public function mobile()
    {
        $this->slots = array_filter($this->slots, function($slot) {
            return $slot['is_mobile'];
        });
        return $this;
    }

    public function desktop()
    {
        $this->slots = array_filter($this->slots, function($slot) {
            return !$slot['is_mobile'];
        });
        return $this;
    }

    public function injectable()
    {
        $this->slots = array_filter($this->slots, function($slot) {
            return isset($slot['selector']) && $slot['selector'] != '';
        });
        return $this;
    }

    public function get()
    {
        $slots = array_map(function($slot) {
            $slot = $this->apply_class($slot);
            $slot = $this->apply_style($slot);
            return $slot;
        }, $this->slots);

        usort($slots, function($a, $b) {
            $order_a = $a['order'] ? $a['order'] : 0;
            $order_b = $b['order'] ? $b['order'] : 0;
            return $order_a < $order_b;
        });

        return $slots;
    }

    private function apply_style($slot)
    {
        $content = $slot['content'];
        preg_match('/style="([^"]*)"/', $content, $matches);
        if (count($matches) > 0) {
            $content = str_replace($matches[1], "{$matches[1]}text-align:{$slot['alignment']};{$slot['style']}", $content);
        } else {
            $style = "{$slot['alignment']};{$slot['style']}";
            $replace = '${1} style="text-align:' . $style . '"';
            $content = preg_replace('/(<div[^>]+)/', $replace, $content);
        }
        $slot['content'] = $content;
        return $slot;
    }

    private function apply_class($slot)
    {
        $content = $slot['content'];
        preg_match('/class="([^"]*)"/', $content, $matches);
        if (count($matches) > 0) {
            $content = str_replace($matches[1], $matches[1] . ' project-adv-inserter', $content);
        } else {
            $replace = '${1} class="project-adv-inserter"';
            $content = preg_replace('/(<div[^>]+)/', $replace, $content);
        }
        $slot['content'] = $content;
        return $slot;
    }
}