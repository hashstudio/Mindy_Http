<?php

namespace Mindy\Http;

/**
 * Class FlashSessionStorage
 * @package Mindy\Http
 */
class FlashSessionStorage implements IFlashStorage
{
    const KEY = 'flash';

    /**
     * Yii compatibility
     */
    public function __construct()
    {
        if(!isset($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = [];
        }
    }

    public function add($key, $value)
    {
        $_SESSION[self::KEY][$key] = $value;
    }

    public function count()
    {
        return count(isset($_SESSION[self::KEY]) ? $_SESSION[self::KEY] : $_SESSION[self::KEY] = []);
    }

    public function clear()
    {
        $_SESSION[self::KEY] = [];
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return isset($_SESSION[self::KEY]) ? $_SESSION[self::KEY] : $_SESSION[self::KEY] = [];
    }
}
