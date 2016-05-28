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
        $this->getResponse()->setStatusCode(200);
        return new JsonModel(array("status" => "ok", "data" => $categories));
    }

     public function getMyCategoriesAction(){
        return $this->getList();
    }

    public function get($id){   
        // Action used for GET requests with resource Id
        if(!empty($id)) {
            $category = $this->getEntityManager()->getRepository('Category\Entity\Category')->find($id);
            if(empty($category)) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("status" => "error", "data" => "Category not found"));
            }
            $this->getResponse()->setStatusCode(200);
            return new JsonModel(
        		$category->toArray()
        	);
        }
        $this->getResponse()->setStatusCode(400);
        return new JsonModel(array("status" => "error", "data" => "post data is required"));
    }

    public function create($data){
        $this->getEntityManager();
        $category = new \Category\Entity\Category($data);
        $category->validate($this->em);
        
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
        $this->getResponse()->setStatusCode(200);
        return new JsonModel($category->toArray());
    }

    public function update($id, $data){
        // Action used for PUT requests
        if(!empty($id) && !empty($data)) {
            $category = $this->getEntityManager()->getRepository('Category\Entity\Category')->find($id);
            if(empty($id)) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("status" => "error", "data" => "Category not found"));
            }
            $category->set($data);
            $category->validate($this->em);
            
            $this->getEntityManager()->flush();
            $this->getResponse()->setStatusCode(200);
            return new JsonModel($category->toArray());
        }
        $this->getResponse()->setStatusCode(400);
        return new JsonModel(array("status" => "error", "data" => "post data is required"));
    }

    public function delete($id){
        // Action used for DELETE requests
        if(!empty($id) && $this->identity()->getRole() != 'user') {
            $category = $this->getEntityManager()->getRepository('Category\Entity\Category')->find($id);
            if(empty($category)) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("status" => "error", "data" => "Category not found"));
            }
            $this->getEntityManager()->remove($category);
            
            $this->getEntityManager()->flush();
            
            return new JsonModel($category->toArray());
        }
        $this->getResponse()->setStatusCode(400);
        return new JsonModel(array("status" => "error", "data" => "get data is required"));
    }

}
