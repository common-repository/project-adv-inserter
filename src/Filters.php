<?php

namespace Sferica\Plugins\ProjectAdvInserter;

class Filters
{
    private $content;

    private $paragraphs;

    private function insert_before_content(Slots $builder)
    {
        $builder_clone = clone $builder;
        foreach ($builder_clone->start()->get() as $slot) {
            $this->content = $slot['content'] . $this->content;
        }
    }

    private function insert_after_content(Slots $builder)
    {
        $builder_clone = clone $builder;
        foreach ($builder_clone->end()->get() as $slot) {
            $this->content .= $slot['content'];
        }
    }

    private function get_paragraphs()
    {
        if (!empty($this->paragraphs)) {
            return $this->paragraphs;
        }

        $paragraphs = null;
        preg_match_all('/<p.*<\/p>/', $this->content, $matches);
        if (count($matches[0]) > 0) {
            $paragraphs = $matches[0];
        }
        return $paragraphs;
    }

    private function insert_after_paragraph(Slots $builder)
    {
        $paragraphs = $this->get_paragraphs();

        $builder_clone = clone $builder;
        foreach ($builder_clone->after()->get() as $slot) {
            $position = $slot['paragraph'];
            switch ($slot['paragraph']) {
                case 'first':
                    $position = 0;
                    break;
                case 'last':
                    $position = count($paragraphs) - 1;
                    break;
            }
            if (isset($paragraphs[$position])) {
                $replace = $paragraphs[$position] . $slot['content'];
                $this->content = str_replace($paragraphs[$position], $replace, $this->content);
            }
        }
    }

    private function insert_before_paragraph(Slots $builder)
    {
        $paragraphs = $this->get_paragraphs();

        $builder_clone = clone $builder;
        foreach ($builder_clone->before()->get() as $slot) {
            switch ($slot['paragraph']) {
                case 'first':
                    $position = 0;
                    break;
                case 'last':
                    $position = count($paragraphs) - 1;
                    break;
                default:
                    $position = $slot['paragraph'];
                    break;
            }
            if (isset($paragraphs[$position])) {
                $replace = $slot['content'] . $paragraphs[$position];
                $this->content = str_replace($paragraphs[$position], $replace, $this->content);
            }
        }
    }

    public function the_content($content)
    {
        $this->content = $content;

        if (is_single()) {
            $builder = (new Slots())->article();
            if (wp_is_mobile()) {
                $builder = $builder->mobile();
            } else {
                $builder = $builder->desktop();
            }
            $builder = $builder->content();

            $this->insert_before_content($builder);
            $this->insert_after_content($builder);
            $this->insert_after_paragraph($builder);
            $this->insert_before_paragraph($builder);
        }

        return $this->content;
    }
}