<?php

namespace Application\Controller\Rest;

use Application\Entity\TechnoSite;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class CategoryController extends AbstractRestfulController {

    public function getList() {
        $em = $this
                ->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default');

        $results= $em->createQuery('select c from \Application\Entity\TechnoCategory c WHERE c.displayMenu=1 order by c.id asc' )
            ->getArrayResult();

        return new JsonModel(array(
            'data' => $results)
        );
    }

    public function get($id) {
        $em = $this
        ->getServiceLocator()
        ->get('doctrine.entitymanager.orm_default');

        /*$results= $em->createQuery('select t from \Application\Entity\Techno t JOIN t.categories c WHERE c.id=:id GROUP BY t.techno order by t.techno asc' )
            ->setParameter("id", $id)
            ->getArrayResult();
        */

        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addScalarResult('techno', 'techno');
        //$rsm->addScalarResult('website', 'website');
        $rsm->addScalarResult('count', 'count');

        $results= $em->createNativeQuery('
            select t.techno, t.website, count(ts.techno) as count from techno as t
            inner join technos_categories as tc on tc.techno = t.techno
            inner join techno_site as ts on ts.techno = t.techno
            where tc.category_id=:id
            group by t.techno
            order by t.techno ASC',
            $rsm)
            ->setParameter("id", $id)
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
