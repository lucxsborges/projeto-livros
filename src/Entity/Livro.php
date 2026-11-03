<?php

namespace App\Entity;

use App\Repository\LivroRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LivroRepository::class)]
#[ORM\Table(name: 'Livro')]
class Livro
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'Codl', type: 'integer')]
    private ?int $codl = null;

    #[ORM\Column(name: 'Titulo', type: 'string', length: 40)]
    #[Assert\NotBlank(message: 'O título é obrigatório.')]
    #[Assert\Length(
        max: 40,
        maxMessage: 'O título deve ter no máximo {{ limit }} caracteres.'
    )]
    private ?string $titulo = null;

    #[ORM\Column(name: 'Editora', type: 'string', length: 40)]
    #[Assert\NotBlank(message: 'A editora é obrigatória.')]
    #[Assert\Length(
        max: 40,
        maxMessage: 'A editora deve ter no máximo {{ limit }} caracteres.'
    )]
    private ?string $editora = null;

    #[ORM\Column(name: 'Edicao', type: 'integer')]
    #[Assert\NotNull(message: 'A edição é obrigatória.')]
    #[Assert\Positive(message: 'A edição deve ser maior que zero.')]
    private ?int $edicao = null;

    #[ORM\Column(name: 'AnoPublicacao', type: 'string', length: 4)]
    #[Assert\NotBlank(message: 'O ano de publicação é obrigatório.')]
    #[Assert\Regex(
        pattern: '/^[0-9]{4}$/',
        message: 'Informe um ano válido com quatro dígitos.'
    )]
    private ?string $anoPublicacao = null;

    #[ORM\Column(name: 'Valor', type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'O valor é obrigatório.')]
    #[Assert\Positive(message: 'O valor deve ser maior que zero.')]
    #[Assert\LessThanOrEqual(
        value: '99999999.99',
        message: 'O valor excede o limite permitido.'
    )]
    private ?string $valor = null;

    #[ORM\ManyToMany(targetEntity: Autor::class, inversedBy: 'livros')]
    #[ORM\JoinTable(name: 'Livro_Autor')]
    #[ORM\JoinColumn(name: 'Livro_Codl', referencedColumnName: 'Codl')]
    #[ORM\InverseJoinColumn(name: 'Autor_CodAu', referencedColumnName: 'CodAu')]
    #[Assert\Count(
        min: 1,
        minMessage: 'Selecione pelo menos um autor.'
    )]
    private Collection $autores;

    #[ORM\ManyToMany(targetEntity: Assunto::class, inversedBy: 'livros')]
    #[ORM\JoinTable(name: 'Livro_Assunto')]
    #[ORM\JoinColumn(name: 'Livro_Codl', referencedColumnName: 'Codl')]
    #[ORM\InverseJoinColumn(name: 'Assunto_CodAs', referencedColumnName: 'CodAs')]
    #[Assert\Count(
        min: 1,
        minMessage: 'Selecione pelo menos um assunto.'
    )]
    private Collection $assuntos;

    public function __construct()
    {
        $this->autores = new ArrayCollection();
        $this->assuntos = new ArrayCollection();
    }

    public function getCodl(): ?int
    {
        return $this->codl;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;
        return $this;
    }

    public function getEditora(): ?string
    {
        return $this->editora;
    }

    public function setEditora(string $editora): static
    {
        $this->editora = $editora;
        return $this;
    }

    public function getEdicao(): ?int
    {
        return $this->edicao;
    }

    public function setEdicao(int $edicao): static
    {
        $this->edicao = $edicao;
        return $this;
    }

    public function getAnoPublicacao(): ?string
    {
        return $this->anoPublicacao;
    }

    public function setAnoPublicacao(string $anoPublicacao): static
    {
        $this->anoPublicacao = $anoPublicacao;
        return $this;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(string $valor): static
    {
        $this->valor = $valor;
        return $this;
    }

    
    public function getAutores(): Collection
    {
        return $this->autores;
    }

    public function addAutor(Autor $autor): static
    {
        if (!$this->autores->contains($autor)) {
            $this->autores->add($autor);
            $autor->addLivro($this);
        }

        return $this;
    }

    public function removeAutor(Autor $autor): static
    {
        if ($this->autores->removeElement($autor)) {
            $autor->removeLivro($this);
        }

        return $this;
    }

    public function getAssuntos(): Collection
    {
        return $this->assuntos;
    }

    public function addAssunto(Assunto $assunto): static
    {
        if (!$this->assuntos->contains($assunto)) {
            $this->assuntos->add($assunto);
            $assunto->addLivro($this);
        }

        return $this;
    }

    public function removeAssunto(Assunto $assunto): static
    {
        if ($this->assuntos->removeElement($assunto)) {
            $assunto->removeLivro($this);
        }

        return $this;
    }
}