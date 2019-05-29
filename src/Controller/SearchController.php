<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Algolia\SearchBundle\IndexManagerInterface;
use App\Entity\Point;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/search")
 */
class SearchController extends AbstractController {
    
    protected $indexManager;

    public function __construct(IndexManagerInterface $indexingManager)
    {
        $this->indexManager = $indexingManager;
    }
    
    
    /**
     * @Route("/algolia",  name="search_algolia")
     */
    public function algoliaAction(Request $request) {        
        $q = $request->query->get('q');
        $points = [];
        if($q != null) {
            $points = $this->indexManager->rawSearch($q, Point::class);            
        }
        //die(var_dump($points));
        return $this->render('search/algolia.html.twig',
            ['q'=>$q,'points'=>$points]
            );
   }
   
       /**
     * @Route("/elastic",  name="search_elastic")
     */
    public function elasticAction(Request $request) {        
        $q = $request->query->get('q');
        $points = [];

        //die(var_dump($points));
        return $this->render('search/elastic.html.twig',
            ['q'=>$q,'points'=>$points]
            );
   }
   

}
