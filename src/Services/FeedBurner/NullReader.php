<?php

namespace App\Services\FeedBurner;

/**
 * Concrete implementation of FeedReader that will never return an item.
 */
class NullReader implements FeedReader
{

    public function __construct(\SimpleXMLElement $root)
    {
        //Nothing
    }

    public static function canRead(\SimpleXMLElement $root)
    {
        return true;
    }

    public function count()
    {
        return null;
    }

    public function item($index)
    {
        return null;
    }
}