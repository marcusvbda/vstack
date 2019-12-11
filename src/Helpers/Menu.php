<?php
class Menu
{
    static function isActive($route, $compare = null)
    {
        return strpos(($compare ? $compare : Route::currentRouteName()), $route) !== false;
    }

    static function ResourceIsActive($id)
    {
        $current = explode("/", url()->current());
        $resp = array_filter($current, function ($a) use ($id) {
            if ($a == $id) return $a;
        });
        return count($resp) > 0;
    }
}
