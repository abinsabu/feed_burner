<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services\FeedBurner;

/**
 * Library used to manage Feeders in the feedburner application.
 *
 * @author Abin Sabu <abinsabu@gmail.com>
 */
class FeedBurner
{
    public $cacheTime = 3600;
    private $url;
    private $reader;
    private $current;
    private $remaining;

    /**
     * Create Atom reader object.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->reset();
    }

    /**
     * Reset current item to first RSS item.
     */
    public function reset()
    {
        $this->current = -1;
        $this->remaining = null;
    }

    /**
     * Get the current item in the feed.
     *
     * @return stdClass Object representing the item. Will return null when the list is exhausted.
     */
    public function current()
    {
        return $this->getReader()->item(max(0, $this->current));
    }

    /**
     * Get FeedReader object for the feed.
     *
     * @return FeedReader
     */
    private function getReader()
    {
        if (!$this->reader) {
            $xml = $this->getXML();
            if (RSSReader::canRead($xml)) {
                $this->reader = new RSSReader($xml);
            } else if (AtomReader::canRead($xml)) {
                $this->reader = new AtomReader($xml);
            } else {
                $this->reader = new NullReader($xml);
            }
        }
        return $this->reader;
    }

    /**
     * Get XML element for the feed.
     *
     * @return \SimpleXMLElement
     */
    private function getXML()
    {
        if ($xml = $this->getCacheXML()) {
            return $xml;
        } else if ($xml = $this->getURLXML()) {
            return $xml;
        } else {
            return new \SimpleXMLElement("");
        }
    }

    /**
     * Get XML element for the feed from cache.
     *
     * @return \SimpleXMLElement or null if cache doesn't exist.
     */
    private function getCacheXML()
    {

    }

    /**
     * Get XML element from the feed from the live URL.
     * Will cache XML data to disk.
     *
     * @return \SimpleXMLElement or null if URL is unreachable.
     */
    private function getURLXML()
    {
        if ($data = @file_get_contents($this->url)) {
            try {
                $xml = new \SimpleXMLElement($data);
                file_put_contents($this->getCacheFilename(), $data);
                return $xml;
            } catch (Exception $e) {
                return null;
            }
        }
    }

    /**
     * Name of the cache file for current URL.
     *
     * @return string
     */
    private function getCacheFilename()
    {
        return sys_get_temp_dir() . '/' . md5($this->url) . '.feed.cache';
    }

    /**
     * Get random item from the feed. Will not return an item more than once.
     *
     * @return stdClass Object representing the item. Will return null when the list is exhausted.
     */
    public function random()
    {
        if ($this->remaining === null) {
            $this->remaining = array();
            for ($i = 0; $i < $this->count(); $i++) {
                $this->remaining[] = $i;
            }
        }

        if (count($this->remaining)) {
            $picked = array_rand($this->remaining);
            $index = $this->remaining[$picked];
            unset($this->remaining[$picked]);
            return $this->getReader()->item($index);
        }
    }

    /**
     * Get the number of items in the feed.
     *
     * @return int
     */
    public function count()
    {
        return $this->getReader()->count();
    }

    /**
     * Get X items from feed. Will advance pointer.
     *
     * @param int $count
     * @return array of stdClass
     */
    public function find($count)
    {
        $items = array();

        while ($item = $this->next()) {
            $items[] = $item;
            if (count($items) >= $count) {
                break;
            }
        }

        return $items;
    }

    /**
     * Get the next item in the feed.
     *
     * @return stdClass Object representing the item. Will return null when the list is exhausted.
     */
    public function next()
    {
        if ($this->current < $this->count()) {
            $this->current++;
            $next = $this->getReader()->item($this->current);
            return $next;
        }
    }
}