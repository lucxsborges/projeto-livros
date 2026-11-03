<?php

namespace App\Entity;

use App\Repository\AssuntoRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AssuntoRepository::class)]
#[ORM\Table(name: 'Assunto')]
class Assunto
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'CodAs', type: 'integer')]
    private ?int $codAs = null;

    #[ORM\Column(name: 'Descricao', type: 'string', length: 20)]
    #[Assert\NotBlank(message: 'A descrição do assunto é obrigatória.')]
    #[Assert\Length(
        max: 20,
        maxMessage: 'A descrição deve ter no máximo {{ limit }} caracteres.'
    )]
    private ?string $descricao = null;

    #[ORM\ManyToMany(targetEntity: Livro::class, mappedBy: 'assuntos')]
    private Collection $livros;

    public function __construct()
    {
        $this->livros = new ArrayCollection();
    }

    public function getCodAs(): ?int
    {
        return $this->codAs;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getLivros(): Collection
    {
        return $this->livros;
    }

    public function addLivro(Livro $livro): static
    {
        if (!$this->livros->contains($livro)) {
            $this->livros->add($livro);
            $livro->addAssunto($this);
        }

        return $this;
    }

    public function removeLivro(Livro $livro): static
    {
        if ($this->livros->removeElement($livro)) {
            $livro->removeAssunto($this);
        }

        return $this;
    }
}