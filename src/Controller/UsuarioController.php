<?php

namespace App\Controller\Admin;

use App\Entity\Usuario;
use App\Repository\UsuarioRepositorio;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/usuarios')]
class UsuarioController extends AbstractController
{
    #[Route('', name: 'admin_usuario_index')]
    public function index(UsuarioRepositorio $usuarioRepositorio): Response
    {
        return $this->render('admin/usuario/index.html.twig', [
            'usuarios' => $usuarioRepositorio->findAll(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_usuario_delete', methods: ['POST'])]
    public function delete(
        Usuario $usuario,
        EntityManagerInterface $em
    ): Response {
        if ($usuario === $this->getUser()) {
            $this->addFlash('error', 'No puedes eliminar tu propio usuario.');
            return $this->redirectToRoute('admin_usuario_index');
        }

        if (in_array('ROLE_ADMIN', $usuario->getRoles(), true)) {
            $this->addFlash('error', 'No puedes eliminar a otro administrador.');
            return $this->redirectToRoute('admin_usuario_index');
        }

        $em->remove($usuario);
        $em->flush();

        return $this->redirectToRoute('admin_usuario_index');
    }
}