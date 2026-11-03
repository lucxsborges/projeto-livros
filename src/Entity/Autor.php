<?php

namespace App\Entity;

use App\Repository\AutorRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AutorRepository::class)]
#[ORM\Table(name: 'Autor')]
class Autor
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'CodAu', type: 'integer')]
    private ?int $codAu = null;

    #[ORM\Column(name: 'Nome', type: 'string', length: 40)]
    #[Assert\NotBlank(message: 'O nome do autor é obrigatório.')]
    #[Assert\Length(
        max: 40,
        maxMessage: 'O nome do autor deve ter no máximo {{ limit }} caracteres.'
    )]
    private ?string $nome = null;

    #[ORM\ManyToMany(targetEntity: Livro::class, mappedBy: 'autores')]
    private Collection $livros;

    public function __construct()
    {
        $this->livros = new ArrayCollection();
    }

    public function getCodAu(): ?int
    {
        return $this->codAu;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

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
            $livro->addAutor($this);
        }

        return $this;
    }

    public function removeLivro(Livro $livro): static
    {
        if ($this->livros->removeElement($livro)) {
            $livro->removeAutor($this);
        }

        return $this;
    }
}
