<?php

namespace Mindy\Http;

use Mindy\Helper\Collection;

/**
 * Class FilesCollection
 * @package Mindy\Http
 */
class FilesCollection extends Collection
{
    public $uploadClass = '\Mindy\Http\UploadedFile';

    public function __construct(array $data = [])
    {
//        $newData = [];
//        $tmp = PrepareData::fixFiles($data);
//        foreach ($tmp as $item) {
//            $newData[] = Creator::createObject($this->uploadClass, $item);
//        }
//        d($tmp);
//        parent::__construct($newData);
        parent::__construct($data);
    }
}
