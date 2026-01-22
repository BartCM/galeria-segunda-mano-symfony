<?php

namespace App\Controller\Admin;

use App\Entity\Usuario;
use App\Repository\UsuarioRepositorio;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/usuarios')]
class UsuarioController extends AbstractController
{
    #[Route('', name: 'admin_usuario_index')]
    public function index(
        Request $request,
        UsuarioRepositorio $usuarioRepositorio
    ): Response {
        $rol = $request->query->get('rol');
        $usuarios = $usuarioRepositorio->findAll();

        if ($rol === 'ROLE_USER') {
            $usuarios = array_filter($usuarios, function ($usuario) {
                return in_array('ROLE_USER', $usuario->getRoles(), true)
                    && !in_array('ROLE_ADMIN', $usuario->getRoles(), true);
            });
        }

        if ($rol === 'ROLE_ADMIN') {
            $usuarios = array_filter($usuarios, function ($usuario) {
                return in_array('ROLE_ADMIN', $usuario->getRoles(), true);
            });
        }


        return $this->render('admin/usuario/index.html.twig', [
            'usuarios' => $usuarios,
            'rol_actual' => $rol,
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

        if (!$usuario->puedeEliminarse()) {
            $this->addFlash(
                'error',
                'No se puede eliminar el usuario porque tiene artículos asociados.'
            );
            return $this->redirectToRoute('admin_usuario_index');
        }

        $em->remove($usuario);
        $em->flush();

        return $this->redirectToRoute('admin_usuario_index');
    }

    #[Route('/{id}/rol', name: 'admin_usuario_rol', methods: ['POST'])]
    public function cambiarRol(Usuario $usuario, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('rol'.$usuario->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $rol = $request->request->get('rol');

        if (!in_array($rol, ['ROLE_USER', 'ROLE_ADMIN'], true)) {
            $this->addFlash('error', 'Rol no válido.');
            return $this->redirectToRoute('admin_usuario_index');
        }

        $usuario->setRoles([$rol]);
        $em->flush();

        $this->addFlash('success', 'Tipo de usuario actualizado.');
        return $this->redirectToRoute('admin_usuario_index');
    }
}