<?php

namespace Sferica\Plugins\ProjectAdvInserter;

class Actions
{
    public function wp_head()
    {
        if (isset($_GET['projectadv_debug'])) {
            echo '<!-- Host: '. $_SERVER['HTTP_HOST'] .' -->' . "\r\n";
            echo '<!-- ProjectAdv Config: '. Config::CDN_URL . md5($_SERVER['HTTP_HOST']) .'.json -->' . "\r\n";
        }

        $builder = new Scripts;
        $scripts = $builder->header()->get();
        foreach ($scripts as $script) {
            echo $script['content'] . "\r\n";
        }
    }

    public function wp_footer()
    {
        $builder = new Scripts;
        $scripts = $builder->footer()->get();
        foreach ($scripts as $script) {
            echo $script['content'] . "\r\n";
        }
    }

    public function wp_enqueue_scripts()
    {
        $slots = [];
        $builder = (new Slots())->injectable();
        if (is_home() || is_front_page()) {
            $builder->index();
        }
        if (is_single()) {
            $builder->article();
        }
        if (is_archive()) {
            $builder->archive();
        }
        $slots = $builder->get();
        wp_enqueue_script('project-adv-inserter', plugin_dir_url( __FILE__ ) . 'assets/dist/main.min.js', [], '1.2.1', true);
        wp_localize_script('project-adv-inserter', 'projectadvinserter', ['slots' => $slots]);
    }
}