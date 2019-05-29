<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class LuckyController extends AbstractController {
    
    
    /**
     * @Route("/lucky/{number}", defaults={"_locale": "en"}, name="en_lucky_number")
     * @Route("/chance/{number}", defaults={"_locale": "fr"}, name="fr_lucky_number")
     */
    public function numberAction($number=100, LoggerInterface $logger) {
        $logger->info('salut');
        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }
    
    /**
     * @Route("/product/{id}", defaults={"_locale": "en"}, name="en_produit", requirements={"id"="\d+"})
     * @Route("/produit/{id}", defaults={"_locale": "fr"}, name="fr_produit", requirements={"id"="\d+"})
     */
    public function productAction($id) {
        
    }
    
    /**
     * @Route("/",  name="home")
     */
    public function indexAction() {
        return $this->render('lucky/home.html.twig');
   }

}
