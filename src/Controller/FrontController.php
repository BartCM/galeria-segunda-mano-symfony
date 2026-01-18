<?php

namespace App\Controller;

use App\Repository\ArticuloRepositorio;
use App\Repository\ArticuloRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'front_index')]
    public function index(ArticuloRepositorio $articuloRepositorio): Response
    {
        return $this->render('front/index.html.twig', [
            'articulos' => $articuloRepositorio->findAll(),
        ]);
    }

    #[Route('/articulo/{id}', name: 'front_articulo_detalle')]
    public function detalle(ArticuloRepositorio $articuloRepositorio, int $id): Response
    {
        $articulo = $articuloRepositorio->find($id);

        if (!$articulo) {
            throw $this->createNotFoundException('Artículo no encontrado');
        }

        return $this->render('front/detalle.html.twig', [
            'articulo' => $articulo,
        ]);
    }

}
