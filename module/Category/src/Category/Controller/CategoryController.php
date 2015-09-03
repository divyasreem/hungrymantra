<?php
namespace Category\Controller;

use User\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

class CategoryController extends AbstractRestfulJsonController{

    protected $em;

    public function getEntityManager(){
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }
	
    public function getList(){   
        // Action used for GET requests without resource Id
        $categories = $this->getEntityManager()->getRepository('Category\Entity\Category')->findAll();
        $categories = array_map(function($category){
            return $category->toArray();
        }, $categories);
        return new JsonModel($categories);
    }

     public function getMyCategoriesAction(){
        return $this->getList();
    }

    public function get($id){   
        // Action used for GET requests with resource Id
        $category = $this->getEntityManager()->getRepository('Category\Entity\Category')->find($id);
        return new JsonModel(
    		$category->toArray()
    	);
    }

    public function create($data){
        $this->getEntityManager();
        $category = new \User\Entity\Category($data);
        $category->validate($this->em);
        
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
        
        return new JsonModel($category->toArray());
    }

    public function update($id, $data){
        // Action used for PUT requests
        $category = $this->getEntityManager()->getRepository('Category\Entity\Category')->find($id);
        $category->set($data);
        $category->validate($this->em);
        
        $this->getEntityManager()->flush();
        
        return new JsonModel($category->toArray());
    }

    public function delete($id){
        // Action used for DELETE requests
        $category = $this->getEntityManager()->getRepository('Category\Entity\Category')->find($id);
        $this->getEntityManager()->remove($category);
        
        $this->getEntityManager()->flush();
        
        return new JsonModel($category->toArray());
    }

}
