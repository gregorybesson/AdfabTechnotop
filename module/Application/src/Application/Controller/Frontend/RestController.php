<?php

namespace Application\Controller\Frontend;

use Application\Entity\TechnoSite;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class RestController extends AbstractRestfulController {

    public function getList() {
        $em = $this
                ->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default');

        $results= $em->createNativeQuery('select * from techno_site INNER JOIN alexa_top_site on techno_site.url=alexa_top_site.url where alexa_top_site.country="FR" and techno_site=:techno order by techno_site.country_rank asc' )
        ->setParameter("techno", $techno)
            ->getArrayResult();


        return new JsonModel(array(
            'data' => $results)
        );
    }

    public function get($id) {

        $em = $this
                ->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default');

        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addScalarResult('url', 'url');
        $rsm->addScalarResult('country_rank', 'country_rank');
        $rsm->addScalarResult('rank', 'rank');

        $results= $em->createNativeQuery('select t.url, a.country_rank, a.rank from techno_site t INNER JOIN alexa_top_site a on t.url=a.url where a.country="FR" and t.techno=:techno order by a.country_rank asc',
            $rsm)
            ->setParameter("techno", $id)
            ->getArrayResult();

        return new JsonModel(array(
            'data' => $results)
        );
    }

    public function create($data) {
        $em = $this
                ->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default');

        $album = new Album();
        $album->setArtist($data['artist']);
        $album->setTitle($data['title']);

        $em->persist($album);
        $em->flush();

        return new JsonModel(array(
            'data' => $album->getId(),
        ));
    }

    public function update($id, $data) {
        $em = $this
                ->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default');

        $album = $em->find('Album\Model\Album', $id);
        $album->setArtist($data['artist']);
        $album->setTitle($data['title']);

        $album = $em->merge($album);
        $em->flush();

        return new JsonModel(array(
            'data' => $album->getId(),
        ));
    }

    public function delete($id) {
        $em = $this
                ->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default');

        $album = $em->find('Album\Model\Album', $id);
        $em->remove($album);
        $em->flush();

        return new JsonModel(array(
            'data' => 'deleted',
        ));
    }

}
