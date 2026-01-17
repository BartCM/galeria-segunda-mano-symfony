<?php

namespace App\Entity;

use App\Repository\CategoriaRepositorio;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(fields: ['nombre'], message: 'Esta categoría ya existe')]
#[ORM\Entity(repositoryClass: CategoriaRepositorio::class)]
class Categoria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaCreacion = null;

    /**
     * @var Collection<int, Articulo>
     */
    #[ORM\OneToMany(mappedBy: 'categoria', targetEntity: Articulo::class)]
    private Collection $articulos;

    public function __construct()
    {
        $this->fechaCreacion = new \DateTimeImmutable();
        $this->articulos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
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

    /**
     * @return Collection<int, Articulo>
     */
    public function getArticulos(): Collection
    {
        return $this->articulos;
    }

    public function addArticulo(Articulo $articulo): static
    {
        if (!$this->articulos->contains($articulo)) {
            $this->articulos->add($articulo);
            $articulo->setCategoria($this);
        }

        return $this;
    }

    public function removeArticulo(Articulo $articulo): static
    {
        if ($this->articulos->removeElement($articulo)) {
            if ($articulo->getCategoria() === $this) {
                $articulo->setCategoria(null);
            }
        }

        return $this;
    }
}

