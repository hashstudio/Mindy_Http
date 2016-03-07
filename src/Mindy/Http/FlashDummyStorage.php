<?php

namespace Mindy\Http;

/**
 * Class FlashDummyStorage
 * @package Mindy\Http
 */
class FlashDummyStorage implements IFlashStorage
{
    private $_data = [];

    public function add($key, $value)
    {
        $this->_data[] = [$key => $value];
        return $this;
    }

    public function count()
    {
        return count($this->_data);
    }

    public function clear()
    {
        $this->_data = [];
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
}
