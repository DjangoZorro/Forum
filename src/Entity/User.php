<?php
// src/Entity/User.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $post_status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="status_updated_by")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="user")
     */
    private $status_updated_by;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="author", orphanRemoval=true)
     */
    private $posts;

    public function __construct()
    {
        parent::__construct();
        $this->status_updated_by = new ArrayCollection();
        $this->posts = new ArrayCollection();
        // your own logic
    }

    public function getPostStatus(): ?string
    {
        return $this->post_status;
    }

    public function setPostStatus(string $post_status): self
    {
        $this->post_status = $post_status;

        return $this;
    }

    public function getUser(): ?self
    {
        return $this->user;
    }

    public function setUser(?self $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getStatusUpdatedBy(): Collection
    {
        return $this->status_updated_by;
    }

    public function addStatusUpdatedBy(self $statusUpdatedBy): self
    {
        if (!$this->status_updated_by->contains($statusUpdatedBy)) {
            $this->status_updated_by[] = $statusUpdatedBy;
            $statusUpdatedBy->setUser($this);
        }

        return $this;
    }

    public function removeStatusUpdatedBy(self $statusUpdatedBy): self
    {
        if ($this->status_updated_by->contains($statusUpdatedBy)) {
            $this->status_updated_by->removeElement($statusUpdatedBy);
            // set the owning side to null (unless already changed)
            if ($statusUpdatedBy->getUser() === $this) {
                $statusUpdatedBy->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }
}
