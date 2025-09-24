<?php

namespace App\Entity;

use App\Repository\SujetRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: SujetRepository::class)]
class Sujet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'datetime', nullable: false, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTime $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Category::class)] // Relation correcte
    #[ORM\JoinColumn(nullable: false)] // La catégorie ne peut pas être NULL
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: Comment::class, orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(["date" => "DESC"])] // Tri des commentaires selon la date décroissante
    private Collection $comments;

    public function __construct()
    {
        // Cette ligne garantit que `createdAt` est défini à la création de l'instance
        $this->createdAt = new \DateTime();
        $this->comments = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist()
    {
        // Met à jour la date avant de sauvegarder (par précaution)
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setSubject($this); // Associer le sujet au commentaire
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // Set the owning side to null (unless already changed)
            if ($comment->getSubject() === $this) {
                $comment->setSubject(null); // Dissocier le sujet du commentaire
            }
        }

        return $this;
    }
}