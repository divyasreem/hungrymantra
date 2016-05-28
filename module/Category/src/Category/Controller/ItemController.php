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
        $this->getResponse()->setStatusCode(200);
        return new JsonModel($items);
    }

     public function getMyCategoriesAction(){
        return $this->getList();
    }

    public function get($id){   
        // Action used for GET requests with resource Id
        if(!empty($id)) {
            $item = $this->getEntityManager()->getRepository('Category\Entity\Item')->find($id);
            if(empty($item)) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("status" => "error", "data" => "Category item not found"));
            }
            return new JsonModel(
                $item->toArray()
            );
        }
        $this->getResponse()->setStatusCode(400);
        return new JsonModel(array("status" => "error", "data" => "Id is required"));
    }

    public function create($data){
        if(!empty($data)) {
            $this->getEntityManager();
            $item = new \Category\Entity\Item($data);
            $category = $this->getEntityManager()->getRepository('Category\Entity\Category')->find($data['category_id']);
            $item->setCategory($category);
            $item->validate($this->em);
            
            $this->getEntityManager()->persist($item);
            $this->getEntityManager()->flush();

            if(!empty($data['image'])) {
                $res = $this->uploadImage($data, $item);
            }
            
            return new JsonModel($item->toArray());
        }
        $this->getResponse()->setStatusCode(400);
        return new JsonModel(array("status" => "error", "data" => "post data is required"));
    }

    public function update($id, $data){
        // Action used for PUT requests
        $item = $this->getEntityManager()->getRepository('Category\Entity\Item')->find($id);
        $item->set($data);
        $item->validate($this->em);
        
        $this->getEntityManager()->flush();

        if(!empty($data['image'])) {
            $res = $this->uploadImage($data, $item);
        }
        
        return new JsonModel($item->toArray());
    }

    public function delete($id){
        // Action used for DELETE requests
        $item = $this->getEntityManager()->getRepository('Category\Entity\Item')->find($id);
        $this->getEntityManager()->remove($item);
        
        $this->getEntityManager()->flush();
        
        return new JsonModel($item->toArray());
    }

    public function categoryItemsAction() {
         $category_id = $this->params()->fromQuery('category_id');
         $em = $this->getEntityManager();
         $queryBuilder = $em->createQueryBuilder();
         $items = $queryBuilder->select('i')->from('Category\Entity\Item', 'i')
                                                            ->where('i.category = :category_id')
                                                            ->setParameter('category_id', $category_id)
                                                            ->getQuery()
                                                            ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        $this->getResponse()->setStatusCode(200);
        return new JsonModel($items);                                                  
    }

    function uploadImage($data, $item) {
        if(empty($data) || empty($item)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel();
        }
        $ext = $data['extention'];
        $file_data = $data['image'];
        $img = base64_decode($file_data);
        if (!$img) {
            $res = 'Please upload a valid image';
            return $res;
        }
        $data_file = explode(';', $file_data);
        if(strpos($data_file[0],'image') === false) {
            $res = 'Please upload a valid image';
            return $res;
        }
        list($type, $file_data) = explode(';', $file_data);
        list(, $file_data)      = explode(',', $file_data);
        $file_data = base64_decode($file_data);
        if (!file_exists(PUBLIC_PATH.'images')) {
            mkdir(PUBLIC_PATH.'images', 0777, true);
        }
         if (!file_exists(PUBLIC_PATH.'images/item')) {
            mkdir(PUBLIC_PATH.'images/item', 0777, true);
        }
        $image_path = PUBLIC_PATH.'images/item/';  
        fopen($image_path.$item->getId().$ext,"w");
        file_put_contents($image_path.$item->getId().$ext, $file_data);

        $helper = $this->CommonHelper();
        $res = $helper->check_img($image_path.$item->getId().$ext);
       
        return $res;
    }

}
