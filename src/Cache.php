<?php

namespace Sferica\Plugins\ProjectAdvInserter;

class Cache
{
    public static function purge_all()
    {
        self::purge_aruba_cache();
        self::purge_wp_rocket();
        self::purge_autoptimize();
        self::purge_w3tc();
    }

    private static function purge_aruba_cache()
    {
        global $aruba_hispeed_cache_purger;
        if ($aruba_hispeed_cache_purger) {
            $aruba_hispeed_cache_purger->purgeAll();
        }
    }

    private static function purge_wp_rocket()
    {
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
    }

    private static function purge_autoptimize()
    {
        if (class_exists('autoptimizeCache')) {
            \autoptimizeCache::clearall();
        }
    }

    private static function purge_w3tc()
    {
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
    }
}