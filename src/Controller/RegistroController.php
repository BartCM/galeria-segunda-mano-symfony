<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistroType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistroController extends AbstractController
{
    #[Route('/registro', name: 'app_registro')]
    public function registro(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $usuario = new Usuario();
        $form = $this->createForm(RegistroType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuario->setPassword(
                $passwordHasher->hashPassword(
                    $usuario,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($usuario);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registro/registro.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
