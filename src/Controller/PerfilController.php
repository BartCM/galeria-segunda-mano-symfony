<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\CambiarPasswordType;
use App\Form\AvatarType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_USER')]
class PerfilController extends AbstractController
{
    #[Route('/perfil', name: 'app_perfil')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        /** @var Usuario $usuario */
        $usuario = $this->getUser();

        $passwordForm = $this->createForm(CambiarPasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $plainPassword = $passwordForm->get('password')->getData();

            $usuario->setPassword(
                $passwordHasher->hashPassword($usuario, $plainPassword)
            );

            $em->flush();
            $this->addFlash('success', 'Contraseña actualizada');

            return $this->redirectToRoute('app_perfil');
        }

        $avatarForm = $this->createForm(AvatarType::class);
        $avatarForm->handleRequest($request);

        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
            $file = $avatarForm->get('avatar')->getData();

            if ($file) {
                $image = imagecreatefromstring(
                    file_get_contents($file->getPathname())
                );

                $resized = imagecreatetruecolor(100, 100);
                imagecopyresampled(
                    $resized,
                    $image,
                    0, 0, 0, 0,
                    100, 100,
                    imagesx($image),
                    imagesy($image)
                );

                $filename = uniqid() . '.png';
                $path = $this->getParameter('kernel.project_dir')
                    . '/public/uploads/avatars/' . $filename;

                imagepng($resized, $path);

                unset($image, $resized);

                $usuario->setAvatar($filename);
                $em->flush();

                $this->addFlash('success', 'Avatar actualizado');

                return $this->redirectToRoute('app_perfil');
            }
        }

        return $this->render('perfil/index.html.twig', [
            'usuario' => $usuario,
            'passwordForm' => $passwordForm->createView(),
            'avatarForm' => $avatarForm->createView(),
        ]);
    }
}