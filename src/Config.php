<?php

namespace Sferica\Plugins\ProjectAdvInserter;

class Config
{
    const TRANSIENT = 'projectadv_inserter_config';
    const TRANSIENT_EXPIRATION = 60 * 60 * 12;
    const OPTIONS = 'projectadvinserter_options';
    const OPTION_CONFIG_URL = 'config_url';
    const OPTION_ADSTXT = 'custom_adstxt';
    const OPTION_DISABLE_EMBED = 'prjadv_disable_embed';
    const REST_PREFIX = 'project-adv-inserter/v1';
    const ADSTXT_BCK = 'adstxt_bck';

    /**
     * Configuration url prefix.
     * Url contains configurations that allow the correct delivery of ProjectAdv services.
     * Please contact ProjectAdv https://www.projectadv.it/ to be able to receive ADV services,
     * if you are not authorized to receive our service, plugin will not make any changes to the website.
     * 
     * Example: https://d27gtglsu4f4y2.cloudfront.net/advinserter/5751ec3e9a4feab575962e78e006250d.json
     * 
     * Json format:
     * {
     *   "scripts":[
     *     {
     *       "position":"header",
     *       "content":"<script src="__projectadv_cdn_link__" async></script>",
     *       "order":0
     *     },
     *     ...
     *   ],
     *   "slots":[
     *     {
     *       "is_mobile":true,
     *       "in_content":false,
     *       "page_type":"index",
     *       "position":"after",
     *       "paragraph":null,
     *       "selector":".header",
     *       "alignment":"center",
     *       "content":"<div id='xyz' ><\/div>",
     *       "style":null,
     *       "order":0
     *     },
     *     ...
     *   ],
     *   "ads_txt":"Lorem ipsum dolor sit amet, consectetur adipiscing elit"
     * }
     */
    const CDN_URL = 'https://d27gtglsu4f4y2.cloudfront.net/advinserter/';

    public $config;

    private static $instances = [];

    public static function getInstance()
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    public function get_config()
    {
        $config = get_option(self::TRANSIENT);
        if (!$config) {
            $config = $this->download_config();
        }
        $config = json_decode($config, true);
        $config['scripts'] = $config['scripts'] ? $config['scripts'] : [];
        $config['slots'] = $config['slots'] ? $config['slots'] : [];
        $this->config = $config;
    }

    private function download_config()
    {
        $config = null;
        $config_url = self::CDN_URL . md5($_SERVER['HTTP_HOST']) . '.json';
        $response = wp_remote_get($config_url, ['timeout' => 2]);
        $response_code = wp_remote_retrieve_response_code($response);
        if (!is_wp_error($response) && $response_code == 200) {
            $config = $response['body'];
            update_option(self::TRANSIENT, $config);

            $adstxt = new AdsTxt;
            $adstxt->update_ads_txt(json_decode($config, true));
        } else {
            $config = [
                "scripts" => [],
                "slots" => [],
                "ads_txt" => ""
            ];
            $config = json_encode($config);
            update_option(self::TRANSIENT, $config);
        }
        return $config;
    }

    public static function can_embed()
    {
        return !get_option(self::OPTION_DISABLE_EMBED);
    }
}