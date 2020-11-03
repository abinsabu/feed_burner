<?php

namespace App\Services\FeedBurner;

/**
 * Interface for reading items from feed.
 */
interface FeedReader
{

    /**
     * Create reader from \SimpleXMLElement.
     *
     * @param \SimpleXMLElement $root
     */
    public function __construct(\SimpleXMLElement $root);

    /**
     * Can this reader understand the XML file?
     *
     * @param \SimpleXMLElement $root
     * @return bool
     */
    public static function canRead(\SimpleXMLElement $root);

    /**
     * Get single node.
     *
     * @return array or null.
     */
    public function item($index);

    /**
     * Get number of items.
     *
     * @return int.
     */
    public function count();

}