<?php
/**
 * Plugin Name: Project Adv Inserter
 * Plugin URI: https://www.projectadv.it/
 * Description: Il plugin ufficiale di Projectadv Srl
 * Version: 1.3.0
 * License: GPLv2 or later
 */

namespace Sferica\Plugins;

use Sferica\Plugins\ProjectAdvInserter\Config;

class ProjectAdvInserter
{
    public function __construct()
    {
        $this->autoload();
        $this->setup_classes();
        $this->actions();
        $this->filters();
        $this->rest();
        $this->admin();

        register_activation_hook(__FILE__, [$this->AdsTxt, 'handle_ads_file']);
        register_deactivation_hook(__FILE__, [$this->AdsTxt, 'restore_ads_file']);
    }

    private function autoload()
    {
        spl_autoload_register(function ($class) {
            if (strpos($class, __CLASS__) !== false) {
                $class = str_replace(__CLASS__, '', $class);
                $class = stripslashes($class);
                if (file_exists(__DIR__ . '/src/' . $class . '.php')) {
                    include 'src/' . $class . '.php';
                }
            }
        });
    }

    private function setup_classes()
    {
        Config::getInstance()->get_config();

        $classes = [
            'Actions',
            'Admin',
            'AdsTxt',
            'Filters',
            'Rest'
        ];
        foreach ($classes as $class) {
            $classname = __CLASS__ . '\\' . $class;
            $this->$class = new $classname();
        }
    }

    private function actions()
    {
        if (Config::can_embed()) {
            add_action('wp_head', [$this->Actions, 'wp_head'], 99);
            add_action('wp_footer', [$this->Actions, 'wp_footer']);
            add_action('wp_enqueue_scripts', [$this->Actions, 'wp_enqueue_scripts']);
        }
    }

    private function filters()
    {
        if (Config::can_embed()) {
            add_filter('the_content', [$this->Filters, 'the_content']);
        }
    }

    private function rest()
    {
        add_action('rest_api_init', [$this->Rest, 'rest_api_init']);
    }

    private function admin()
    {
        add_action('admin_init', [$this->Admin, 'admin_init']);
        add_action('admin_menu', [$this->Admin, 'admin_menu']);
        add_action('update_option_'.Config::OPTIONS, [$this->Admin, 'update_option'], 10, 2);
    }
}

new ProjectAdvInserter;