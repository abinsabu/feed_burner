<?php

namespace App\Services\FeedBurner;

/**
 * Concrete implementation of FeedReader that will read an Atom feed.
 */
class AtomReader implements FeedReader
{

    private $root;

    public function __construct(\SimpleXMLElement $root)
    {
        $this->root = $root;
    }

    public static function canRead(\SimpleXMLElement $root)
    {
        //Check for Atom namespace.
        return in_array('http://www.w3.org/2005/Atom', $root->getNamespaces());
    }

    public function count()
    {
        return count($this->root->entry);
    }

    public function item($index)
    {
        $node = $this->root->entry[$index];
        if (!$node) {
            return null;
        }
        $item = [];
        $item['link'] = $itemLink = null;
        $item['image'] = $itemImage = null;
        //Iterate through link nodes getting content URL and images.
        foreach ($node->link as $link) {
            if (strpos($link['type'], 'text') === 0 || $item['link'] === null) {
                $itemLink = (string)$link['href'];
            }
            if (strpos($link['type'], 'image') === 0) {
                $itemImage = (string)$link['href'];
            }
        }

        $converted = \json_decode(\json_encode($node), true);
        if (!empty($converted)) {
            foreach ($converted as $key => $value) {
                $fullEntries[$key] = (string)$node->{$key};
            }
            $fullEntries['link'] = $itemLink;
            $fullEntries['image'] = $itemImage;
        }
        $item = [
            'type' => 'Atom',
            'unique_id' => (string)$node->id,
            'title' => (string)$node->title,
            'description' => (string)$node->description,
            'link' => $itemLink,
            'image' => $itemImage,
            'date' => strtotime($node->published),
            'json' => \json_encode($fullEntries)
        ];
        return (object)$item;
    }
}