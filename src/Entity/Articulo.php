<?php

namespace App\Entity;

use App\Repository\ArticuloRepositorio;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ArticuloRepositorio::class)]
class Articulo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $titulo = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descripcion = null;

    #[ORM\Column]
    private ?float $precio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaCreacion = null;

    #[ORM\ManyToOne(inversedBy: 'articulos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'articulos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;
    
    /**
     * @var Collection<int, Operacion>
     */
    #[ORM\OneToMany(mappedBy: 'articulo', targetEntity: Operacion::class)]
    private Collection $operaciones;

    public function __construct()
    {
        $this->fechaCreacion = new \DateTimeImmutable();
        $this->operaciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): static
    {
        $this->precio = $precio;
        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(string $imagen): static
    {
        $this->imagen = $imagen;
        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeImmutable
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeImmutable $fechaCreacion): static
    {
        $this->fechaCreacion = $fechaCreacion;
        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): static
    {
        $this->categoria = $categoria;
        return $this;
    }

    /**
     * @return Collection<int, Operacion>
     */
    public function getOperaciones(): Collection
    {
        return $this->operaciones;
    }

    public function tieneOperaciones(): bool
    {
        return !$this->operaciones->isEmpty();
    }

}

