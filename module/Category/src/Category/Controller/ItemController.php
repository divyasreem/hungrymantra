<?php
namespace Category\Controller;

use User\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

class ItemController extends AbstractRestfulJsonController{

    protected $em;

    public function getEntityManager(){
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }
	
    public function getList(){   
        // Action used for GET requests without resource Id
        $items = $this->getEntityManager()->getRepository('Category\Entity\Item')->findAll();
        $items = array_map(function($item){
            return $item->toArray();
        }, $items);
        return new JsonModel($items);
    }

     public function getMyCategoriesAction(){
        return $this->getList();
    }

    public function get($id){   
        // Action used for GET requests with resource Id
        $item = $this->getEntityManager()->getRepository('Category\Entity\Item')->find($id);
        if(!empty($item)) {
            return new JsonModel(
                $item->toArray()
            );
        }
        $this->getResponse()->setStatusCode(400);
        return new JsonModel();
    }

    public function create($data){
        $this->getEntityManager();
        $item = new \Category\Entity\Item($data);
        $category = $this->getEntityManager()->getRepository('Category\Entity\Category')->find($data['category_id']);
        $item->setCategory($category);
        $item->validate($this->em);
        
        $this->getEntityManager()->persist($item);
        $this->getEntityManager()->flush();
        
        return new JsonModel($item->toArray());
    }

    public function update($id, $data){
        // Action used for PUT requests
        $item = $this->getEntityManager()->getRepository('Category\Entity\Item')->find($id);
        $item->set($data);
        $item->validate($this->em);
        
        $this->getEntityManager()->flush();
        
        return new JsonModel($item->toArray());
    }

    public function delete($id){
        // Action used for DELETE requests
        $item = $this->getEntityManager()->getRepository('Category\Entity\Item')->find($id);
        $this->getEntityManager()->remove($item);
        
        $this->getEntityManager()->flush();
        
        return new JsonModel($item->toArray());
    }

}
