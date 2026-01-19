<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\PerfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PerfilController extends AbstractController
{
    #[Route('/perfil', name: 'app_perfil')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var Usuario $usuario */
        $usuario = $this->getUser();

        $form = $this->createForm(PerfilType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Perfil actualizado correctamente.');

            return $this->redirectToRoute('app_perfil');
        }

        return $this->render('perfil/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}