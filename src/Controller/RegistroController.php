<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistroType;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistroController extends AbstractController
{
    #[Route('/registro', name: 'app_registro')]
    public function registro(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginAuthenticator $authenticator
    ): Response {
        // Si ya está logueado, no permitir registro
        if ($this->getUser()) {
            return $this->redirectToRoute('front_index');
        }

        $usuario = new Usuario();

        // ✅ Generar CAPTCHA SOLO en GET
        if (!$request->isMethod('POST')) {
            $captchaCode = random_int(10000, 99999);
            $request->getSession()->set('captcha_code', $captchaCode);
        }

        $form = $this->createForm(RegistroType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Validar CAPTCHA
            $captchaIntroducido = $form->get('captcha')->getData();
            $captchaSession = $request->getSession()->get('captcha_code');

            if ($captchaIntroducido != $captchaSession) {
                $this->addFlash('error', 'El código CAPTCHA no es correcto.');
                return $this->redirectToRoute('app_registro');
            }

            // Hashear contraseña
            $usuario->setPassword(
                $passwordHasher->hashPassword(
                    $usuario,
                    $form->get('plainPassword')->getData()
                )
            );

            $usuario->setRoles(['ROLE_USER']);

            $entityManager->persist($usuario);
            $entityManager->flush();

            // Login automático tras registro
            return $userAuthenticator->authenticateUser(
                $usuario,
                $authenticator,
                $request
            );
        }

        return $this->render('registro/registro.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}