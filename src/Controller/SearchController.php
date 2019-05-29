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
        if($q != null) {
            $query = [
                "query"=> [
                    "match"=> [
                        "name"=> 
                            ["query"=> $q ,
                            "fuzziness"=> 2,
                            "prefix_length"=> 1
                            ]
                          ]
                        ]
                      ];
            $path = 'point/_search';
            $points = $this->client->request($path, Request::METHOD_GET, $query);
die(var_dump($points));
           /* $finalQuery = new BoolQuery();
            $query1 = new \Elastica\Query\Fuzzy();
            $query1->setField('name',$q);
            $finalQuery->addShould($query1);
            $query2 = new \Elastica\Query\Fuzzy();
            $query2->setField('description',"*".$q);
            $finalQuery->addShould($query2);

            $points = $this->client->getIndex('point')->search($query);*/
        }
        return $this->render('search/elastic.html.twig',
            ['q'=>$q,'points'=>$points]
            );
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
        return new \Symfony\Component\HttpFoundation\Response(var_dump($index));
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
