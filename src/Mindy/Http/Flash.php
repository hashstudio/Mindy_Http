<?php

namespace Mindy\Http;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Mindy\Base\ApplicationComponent;

/**
 * Class Flash
 * @package Mindy\Http
 */
class Flash extends ApplicationComponent implements IteratorAggregate, Countable
{
    const FLASH_KEY_PREFIX = 'flash.';

    const SUCCESS = 'success';

    const ERROR = 'error';

    const INFO = 'info';

    const WARNING = 'warning';

    /**
     * @var IFlashStorage
     */
    public $storage;

    public function init()
    {
        $this->storage = new FlashSessionStorage();
    }

    public function success($message)
    {
        return $this->add(self::SUCCESS, $message);
    }

    public function info($message)
    {
        return $this->add(self::INFO, $message);
    }

    public function error($message)
    {
        return $this->add(self::ERROR, $message);
    }

    public function warning($message)
    {
        return $this->add(self::WARNING, $message);
    }

    public function add($key, $message)
    {
        $this->storage->add($key, $message);
        return $this;
    }

    public function set(array $messages)
    {
        foreach ($messages as $key => $message) {
            $this->add($key, $message);
        }
        return $this;
    }

    public function count()
    {
        return $this->storage->count();
    }

    public function clear()
    {
        return $this->storage->clear();
    }

    public function getData()
    {
        $data = $this->storage->getData();
        $this->storage->clear();
        return $data;
    }

    public function all()
    {
        return $this->getData();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getData());
    }
}
