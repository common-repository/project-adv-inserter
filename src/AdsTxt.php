<?php

namespace Sferica\Plugins\ProjectAdvInserter;

class AdsTxt
{
    private $options;

    public function __construct()
    {
        $this->options = get_option(Config::OPTIONS);
    }

    private function get_ads_txt_path()
    {
        if (
            @file_exists(dirname(ABSPATH) . '/wp-config.php') && 
            !@file_exists(dirname(ABSPATH) . '/wp-settings.php')
        ) {
            return dirname(ABSPATH);
        }        
        return ABSPATH;
    }

    public function handle_ads_file()
    {
        $path = $this->get_ads_txt_path();
        if (file_exists($path . '/ads.txt')) {
            $content = file_get_contents($path . '/ads.txt');
            $this->options[Config::OPTION_ADSTXT] = $content;
            $this->options[Config::ADSTXT_BCK] = $content;
            update_option(Config::OPTIONS, $this->options);
        }
    }

    public function restore_ads_file()
    {
        $path = $this->get_ads_txt_path();
        $content = $this->options[Config::ADSTXT_BCK];
        file_put_contents($path . '/ads.txt', $content);
    }

    public function update_ads_txt($config)
    {
        $path = $this->get_ads_txt_path();
        $content = $config['ads_txt'];
        $content .= "\r\n";
        $content .= $this->options[Config::OPTION_ADSTXT];
        file_put_contents($path . '/ads.txt', $content);
    }
}