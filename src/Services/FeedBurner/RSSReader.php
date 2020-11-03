<?php

namespace App\Services\FeedBurner;

/**
 * Concrete implementation of FeedReader that will read an RSS feed.
 */
class RSSReader implements FeedReader
{

    private $root;

    public function __construct(\SimpleXMLElement $root)
    {
        $this->root = $root;
    }

    public static function canRead(\SimpleXMLElement $root)
    {
        //RSS feeds name their root node 'rss'.
        return $root->getName() == 'rss';
    }

    public function count()
    {
        return count($this->root->channel->item);
    }

    public function item($index)
    {
        $node = $this->root->channel->item[$index];
        if (!$node) {
            return null;
        }
        $item['link'] = null;
        $converted = \json_decode(\json_encode($node), true);
        if (!empty($converted)) {
            foreach ($converted as $key => $value) {
                $fullEntries[$key] = (string)$node->{$key};
            }
            $fullEntries['link'] = (string)$node->link;
        }
        return (object)[
            'type' => 'RSS',
            'unique_id' => (string)str_replace(' ', '_', strtolower($node->title)),
            'title' => (string)$node->title,
            'description' => (string)$node->description,
            'link' => (string)$node->link,
            'image' => null,
            'date' => strtotime($node->pubDate),
            'json' => \json_encode($fullEntries)
        ];
    }
}