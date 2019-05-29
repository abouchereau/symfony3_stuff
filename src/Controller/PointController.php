<?php

namespace App\Controller;

use App\Entity\Point;
use App\Form\PointType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/point")
 */
class PointController extends Controller
{
    /**
     * @Route("/", name="point_index", methods={"GET"})
     */
    public function index(): Response
    {
        $points = $this->getDoctrine()
            ->getRepository(Point::class)
            ->findAllArray();
        return $this->render('point/index.html.twig', [
            'points' => $points,
        ]);
    }

    /**
     * @Route("/new", name="point_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $point = new Point();
        $form = $this->createForm(PointType::class, $point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($point);
            $entityManager->flush();

            return $this->redirectToRoute('point_index');
        }

        return $this->render('point/new.html.twig', [
            'point' => $point,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="point_show", methods={"GET"})
     */
    public function show(Point $point): Response
    {
        return $this->render('point/show.html.twig', [
            'point' => $point,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="point_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Point $point): Response
    {
        $form = $this->createForm(PointType::class, $point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('point_index', [
                'id' => $point->getId(),
            ]);
        }

        return $this->render('point/edit.html.twig', [
            'point' => $point,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="point_delete",methods={"GET","POST"})
     */
    public function delete(Request $request, Point $point): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($point);
        $entityManager->flush();

        return $this->redirectToRoute('point_index');
    }
}
