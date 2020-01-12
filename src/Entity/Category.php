<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Title can\'t be empty!")
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\News", mappedBy="categories")
     */
    private $article;

    public function __construct()
    {
      $this->news = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|News[]
     */

    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $article): self
    {
        if (!$this->news->contains($article)) {
            $this->news[] = $article;
            $news->addCategory($this);
        }
        return $this;
    }

    public function removeNews(News $article): self
    {
        if ($this->news->contains($article)) {
            $this->news->removeElement($article);
            $news->removeCategory($this);
        }
        return $this;
    }
}
