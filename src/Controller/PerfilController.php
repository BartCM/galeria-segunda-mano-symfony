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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\CambiarPasswordType;
use App\Form\AvatarType;

#[IsGranted('ROLE_USER')]
class PerfilController extends AbstractController
{
    #[Route('/perfil', name: 'app_perfil')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        /** @var \App\Entity\Usuario $usuario */
        $usuario = $this->getUser();


        // Form contraseña
        $passwordForm = $this->createForm(CambiarPasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $usuario->setPassword(
                $passwordHasher->hashPassword(
                    $usuario,
                    $passwordForm->get('password')->getData()
                )
            );
            $em->flush();
            $this->addFlash('success', 'Contraseña actualizada');
            return $this->redirectToRoute('app_perfil');
        }

        // Form avatar
        $avatarForm = $this->createForm(AvatarType::class);
        $avatarForm->handleRequest($request);

        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
            $file = $avatarForm->get('avatar')->getData();

            if ($file) {
                $filename = uniqid().'.'.$file->guessExtension();

                $file->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/avatars',
                    $filename
                );

                $usuario->setAvatar($filename);
                $em->flush();

                $this->addFlash('success', 'Avatar actualizado');
                return $this->redirectToRoute('app_perfil');
            }
        }

        return $this->render('perfil/index.html.twig', [
            'passwordForm' => $passwordForm->createView(),
            'avatarForm' => $avatarForm->createView(),
            'usuario' => $usuario,
        ]);
    }
}