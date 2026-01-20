<?php

namespace App\Controller;

use App\Repository\ArticuloRepositorio;
use App\Repository\CategoriaRepositorio;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends AbstractController
{
    #[Route('/', name: 'front_index')]
    public function index(Request $request, ArticuloRepositorio $articuloRepositorio,
        CategoriaRepositorio $categoriaRepositorio
    ): Response {
        $categoriaId = $request->query->get('categoria');
        $desde = $request->query->get('desde');
        $hasta = $request->query->get('hasta');
        $q = $request->query->get('q');
        $usuarioId = $request->query->get('usuario');

        $categoriaId = $categoriaId !== null && $categoriaId !== '' ? (int) $categoriaId : null;

        $desdeFecha = null;
        if ($desde) {
            $desdeFecha = \DateTimeImmutable::createFromFormat('Y-m-d', $desde);
        }

        $hastaFecha = null;
        if ($hasta) {
            $hastaFecha = \DateTimeImmutable::createFromFormat('Y-m-d', $hasta);
        }

        $articulos = $articuloRepositorio->filtrarPorCategoriaYFecha($categoriaId, $desdeFecha, $hastaFecha, 
            $q, $usuarioId);

        return $this->render('front/index.html.twig', [
            'articulos' => $articulos,
            'categorias' => $categoriaRepositorio->findAll(),
            'categoriaSeleccionada' => $categoriaId,
            'desde' => $desde,
            'hasta' => $hasta,
            'q' => $q,
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
