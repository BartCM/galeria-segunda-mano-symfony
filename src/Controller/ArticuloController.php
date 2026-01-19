<?php

namespace App\Controller;

use App\Entity\Articulo;
use App\Form\ArticuloType;
use App\Repository\ArticuloRepositorio;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/articulo')]
final class ArticuloController extends AbstractController
{
    #[Route(name: 'app_articulo_index', methods: ['GET'])]
    public function index(ArticuloRepositorio $articuloRepositorio): Response
    {
        $usuario = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $articulos = $articuloRepositorio->findAll();
        } else {
            $articulos = $articuloRepositorio->findBy([
                'usuario' => $usuario
            ]);
        }

        return $this->render('articulo/index.html.twig', [
            'articulos' => $articulos,
        ]);
    }

    #[Route('/new', name: 'app_articulo_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $articulo = new Articulo();
        $form = $this->createForm(ArticuloType::class, $articulo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Asignar propietario
            $articulo->setUsuario($this->getUser());

            $imagenFile = $form->get('imagen')->getData();

            if ($imagenFile) {
                $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imagenFile->guessExtension();

                $imagenFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );

                $articulo->setImagen($newFilename);
            }

            $entityManager->persist($articulo);
            $entityManager->flush();

            return $this->redirectToRoute('app_articulo_index');
        }

        return $this->render('articulo/new.html.twig', [
            'articulo' => $articulo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_articulo_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Articulo $articulo,
        EntityManagerInterface $entityManager
    ): Response {
        $usuario = $this->getUser();

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            $articulo->getUsuario() !== $usuario
        ) {
            throw $this->createAccessDeniedException('No puedes editar este artículo.');
        }

        $form = $this->createForm(ArticuloType::class, $articulo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_articulo_index');
        }

        return $this->render('articulo/edit.html.twig', [
            'articulo' => $articulo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_articulo_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Articulo $articulo,
        EntityManagerInterface $entityManager
    ): Response {
        $usuario = $this->getUser();

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            $articulo->getUsuario() !== $usuario
        ) {
            throw $this->createAccessDeniedException('No puedes eliminar este artículo.');
        }

        if ($this->isCsrfTokenValid('delete'.$articulo->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($articulo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_articulo_index');
    }
}