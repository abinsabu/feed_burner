<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\FeedsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FeedsRepository::class)
 *
 * @author Abin Sabu <abinsabu@gmail.com>
 */
class Feeds
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $feed_data = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $feed_url;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $custom_url;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $image_url;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hidden;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $unique_id;

    /**
     * @ORM\ManyToOne(targetEntity=Feeder::class, inversedBy="feeds")
     */
    private $feeder;

    /**
     * @ORM\OneToMany(targetEntity=FeedRating::class, mappedBy="feed")
     */
    private $feedRatings;

    public function __construct()
    {
        $this->feedRatings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFeedData()
    {
        return $this->feed_data;
    }

    public function setFeedData($feed_data): self
    {
        $this->feed_data = $feed_data;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFeedUrl(): ?string
    {
        return $this->feed_url;
    }

    public function setFeedUrl(?string $feed_url): self
    {
        $this->feed_url = $feed_url;

        return $this;
    }

    public function getCustomUrl(): ?string
    {
        return $this->custom_url;
    }

    public function setCustomUrl(?string $custom_url): self
    {
        $this->custom_url = $custom_url;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(?string $image_url): self
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(?bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getUniqueId(): ?string
    {
        return $this->unique_id;
    }

    public function setUniqueId(?string $unique_id): self
    {
        $this->unique_id = $unique_id;

        return $this;
    }

    public function getFeeder(): ?Feeder
    {
        return $this->feeder;
    }

    public function setFeeder(?Feeder $feeder): self
    {
        $this->feeder = $feeder;

        return $this;
    }

    /**
     * @return Collection|FeedRating[]
     */
    public function getFeedRatings(): Collection
    {
        return $this->feedRatings;
    }

    public function addFeedRating(FeedRating $feedRating): self
    {
        if (!$this->feedRatings->contains($feedRating)) {
            $this->feedRatings[] = $feedRating;
            $feedRating->setFeed($this);
        }

        return $this;
    }

    public function removeFeedRating(FeedRating $feedRating): self
    {
        if ($this->feedRatings->contains($feedRating)) {
            $this->feedRatings->removeElement($feedRating);
            // set the owning side to null (unless already changed)
            if ($feedRating->getFeed() === $this) {
                $feedRating->setFeed(null);
            }
        }

        return $this;
    }
}
