<?php

namespace App\Controller;

use App\Entity\Articulo;
use App\Entity\Operacion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[IsGranted('ROLE_USER')]
class OperacionController extends AbstractController
{
    #[Route('/articulo/{id}/comprar', name: 'articulo_comprar', methods: ['POST'])]
    public function comprar(
        Articulo $articulo,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {

        if (!$this->isCsrfTokenValid('comprar'.$articulo->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        /** @var \App\Entity\Usuario $usuario */
        $usuario = $this->getUser();

        if ($usuario === $articulo->getUsuario()) {
            $this->addFlash('error', 'No puedes comprar tu propio artículo.');
            return $this->redirectToRoute('front_articulo_detalle', [
                'id' => $articulo->getId()
            ]);
        }

        // Evita compras duplicadas
        $existeOperacion = $em->getRepository(Operacion::class)->findOneBy([
            'usuario' => $usuario,
            'articulo' => $articulo,
        ]);

        if ($existeOperacion) {
            $this->addFlash('error', 'Ya has comprado este artículo.');
            return $this->redirectToRoute('front_articulo_detalle', [
                'id' => $articulo->getId()
            ]);
        }

        $operacion = new Operacion();
        $operacion->setUsuario($usuario);
        $operacion->setArticulo($articulo);

        $em->persist($operacion);
        $em->flush();

        $email = (new Email())
            ->from('no-reply@galeria.com')
            ->to($articulo->getUsuario()->getEmail())
            ->subject('Tu artículo ha sido comprado')
            ->text(sprintf(
                "Hola,\n\nEl usuario %s ha comprado tu artículo \"%s\".\n\nUn saludo,\nGalería Segunda Mano",
                $usuario->getNombreUsuario(),
                $articulo->getTitulo()
            ));

        $mailer->send($email);

        $this->addFlash('success', 'Has comprado el artículo correctamente.');

        return $this->redirectToRoute('front_articulo_detalle', [
            'id' => $articulo->getId()
        ]);
    }
}