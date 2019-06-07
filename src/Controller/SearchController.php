<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Algolia\SearchBundle\IndexManagerInterface;
use App\Entity\Point;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Elastica\Document;
use Elastica\Client;
use Elastica\Query;
use Elastica\Query\QueryString;
use Elastica\Query\MultiMatch;
use Elastica\Query\BoolQuery;
use Elastica\Query\Fuzzy;

/**
 * @Route("/search")
 */
class SearchController extends AbstractController {
    
    protected $indexManager;//algolia
    private $client;//elastica

    public function __construct(
            IndexManagerInterface $indexingManager, 
            Client $client
            )
    {
        $this->indexManager = $indexingManager;
        $this->client = $client;
    }
    
//indexer algolia avec php bin/console search:import
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
     * @Route("/algolia_clear",  name="clear_algolia")
     */
    public function algoliaClearAction(Request $request) {        
        $this->indexManager->clear(Point::class);   
        return new Response("OK");
   }
   
   
       /**
     * @Route("/elastic",  name="search_elastic")
     */
    public function elasticAction(Request $request) {        
        $q = $request->query->get('q');        
        $points = [];       
        if($q != null) {
            
            
            
            $query = ["query" => 
                ["multi_match" =>
                    ["query" => $q,
                     "fuzziness" => 1,
                    "fields" => ["name", "description"]
                         ]
                    ]
                ];

            $path = 'point/_search';
            $client = new Client();
            $response = $client->request($path, Request::METHOD_GET, $query);
            $points = $response->getData();
            $points = $points["hits"]["hits"];
            //die(var_dump($points["hits"]["hits"]));
        }
        return $this->render('search/elastic.html.twig',
            ['q'=>$q,'points'=>$points]
            );
   }
   
          /**
     * @Route("/elastic_clear",  name="clear_elastic")
     */
    public function elasticClearAction(Request $request) {        
        $path = 'point';
        $client = new Client();
        $client->request($path, Request::METHOD_DELETE, []);
        return new Response("OK");
   }
   
    /**
     * @Route("/elastic_create",  name="elastic_create")
     */
   public function elasticCreateAction(Request $request) {  
       
        $index = $this->client->getIndex('point');

        $settings = [
            "settings"=>[
                "index"=>["number_of_shards"=>1,"number_of_replicas"=>0]
                ],
            "mappings"=>[
//                "points"=> [
//                    "dynamic"=> false,
                    "properties" => [
                        "name" => ["type" => "text"],
                        "description" => ["type" => "text"]
                    ]
//                ]
            ],
            'analysis' => array(
                'analyzer' => array(
                  'my_awesome_analyzer' => array(
                    'type' => 'custom',
                    'tokenizer' => 'standard',
                    'filter' => array('lowercase', 'asciifolding')
                  ),
                )
              )
        ];

        $index->create($settings, true);

       return new \Symfony\Component\HttpFoundation\Response(var_dump($index));
   }
   
    /**
     * @Route("/elastic_index",  name="elastic_index")
     */
   public function elasticIndexerAction(Request $request) {  
        $rep = $this->getDoctrine()->getRepository('App:Point');
        $allPoints = $rep->findAll();
        $index = $this->client->getIndex('point');

        $documents = [];
        foreach ($allPoints as $point) {
            $documents[] = $this->buildDocument($point);
        }

        $index->addDocuments($documents);
        $index->refresh();     
        return new \Symfony\Component\HttpFoundation\Response(count($documents));
   }
   
   
   private function buildDocument(Point $point) {
        return new Document(
            "point_".$point->getId(), // Manually defined ID
            [
                'name' => $point->getName(),
                'description' => $point->getDescription()
            ]
        );
    }
   

}
