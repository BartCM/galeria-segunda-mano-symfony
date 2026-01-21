<?php

namespace App\Controller;

use App\Repository\OperacionRepositorio;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class MisComprasController extends AbstractController
{
    #[Route('/mis-compras', name: 'app_mis_compras')]
    public function index(OperacionRepositorio $operacionRepositorio): Response
    {
        $usuario = $this->getUser();

        $operaciones = $operacionRepositorio->findBy(
            ['usuario' => $usuario],
            ['fecha' => 'DESC']
        );

        return $this->render('mis_compras/index.html.twig', [
            'operaciones' => $operaciones,
        ]);
    }
}