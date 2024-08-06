<?php

namespace Dv\Heads\Widget\Main;


class Events
{
    public static function onProlog(): void
    {
        global $APPLICATION;
        if (\CSite::InDir('/stream/index.php')) {
            ob_start();
            $APPLICATION->IncludeComponent(
                "dv:heads.widget",
                ".default", [],
                false
            );
            $APPLICATION->AddViewContent('sidebar', ob_get_clean(), 500);
        }
    }
}