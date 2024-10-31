<?php

namespace Sferica\Plugins\ProjectAdvInserter;

use WP_REST_Server;

class Rest
{
    public function rest_api_init()
    {
        register_rest_route(Config::REST_PREFIX, '/purge-cache', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'purge_cache'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route(Config::REST_PREFIX, '/toggle-embed/(?P<action>(enable|disable)+)', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'toggle_embed'],
            'permission_callback' => '__return_true'
        ]);
    }

    public function purge_cache()
    {
        delete_option(Config::TRANSIENT);
        Cache::purge_all();
        return ['message' => 'Project Adv cache cleared'];
    }

    public function toggle_embed($request)
    {
        $option = $request['action'] == 'disable' ? true : false;
        update_option(Config::OPTION_DISABLE_EMBED, boolval($option));
        return ['message' => 'Embed ' . (!$option ? 'enabled' : 'disabled')];
    }
}