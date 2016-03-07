<?php

namespace Mindy\Http;

/**
 * Interface IFlashStorage
 * @package Mindy\Http
 */
interface IFlashStorage
{
    public function add($key, $value);

    public function count();

    public function clear();

    /**
     * @return array
     */
    public function getData();
}
