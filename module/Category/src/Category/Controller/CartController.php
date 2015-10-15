<?php
namespace Category\Controller;

use User\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

class CartController extends AbstractRestfulJsonController{

    protected $em;

    public function getEntityManager(){
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    function create($data) { 
        $user_id = $this->identity()->getId();
        $check_cart = $this->checkCartLoggedUser($data['item_id'], $user_id);
        $item = $this->getEntityManager()->getRepository('Category\Entity\Item')->find($data['item_id']);
        if(empty($item)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel();
        }
        if(empty($check_cart)) {
            if(empty($item)) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel();
            }
            $cart = new \Category\Entity\Cart();
            $cart->setUnitPrice($item->getPrice());
            $cart->setSubTotal($item->getPrice() * $data['quantity']);
            $cart->setQuantity($data['quantity']);
            $cart->setItem($item);
            $cart->setUser($this->identity());

            $this->getEntityManager()->persist($cart);
            $this->getEntityManager()->flush();

            return new JsonModel(array('status'=>'ok','data'=>$cart->toArray()));
        } else {
                $cart = $this->update( $check_cart[0]['id'], $data['quantity'], $item->getPrice());
                return new JsonModel(array('status'=>'ok','data'=>$cart));
        }
    }

    function checkCartLoggedUser($item_id, $user_id) {
        if(empty($item_id) || empty($user_id)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel();
        }
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
        $cart_loan = $queryBuilder->select('c.id')->from('Category\Entity\Cart', 'c')
                                                  ->leftJoin('c.item', 'i')
                                               ->where('c.item = :item_id and c.user = :user_id')
                                               ->setParameter('item_id', $item_id)
                                               ->setParameter('user_id', $user_id)
                                               ->getQuery()
                                               ->getResult();

        return $cart_loan;
    }
	
    public function getList(){   
        // Action used for GET requests without resource Id
        $cart_items = $this->getEntityManager()->getRepository('Category\Entity\Cart')->findAll();
        $cart_items = array_map(function($cart_item){
            return $cart_item->toArray();
        }, $cart_items);
        return new JsonModel($cart_items);
    }

     public function getMyCategoriesAction(){
        return $this->getList();
    }

    public function get($id){   
        // Action used for GET requests with resource Id
        $cart_item = $this->getEntityManager()->getRepository('Category\Entity\Cart')->find($id);
        if(empty($cart_item)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel();
        }
        return new JsonModel(
    		$cart_item->toArray()
    	);
    }

    public function update($id, $quantity, $price){
        // Action used for PUT requests
        if(empty($id) || empty($quantity)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel();
        }
        $cart = $this->getEntityManager()->getRepository('Category\Entity\Cart')->find($id);
        if(empty($cart)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel();
        }
        $cart->setSubTotal($quantity * $price);
        $cart->setQuantity($quantity);
        $cart->validate($this->em);
        $this->getEntityManager()->flush();
    
        return $cart->toArray();
    }

    public function delete($id){
        // Action used for DELETE requests
        $cart = $this->getEntityManager()->getRepository('Category\Entity\Cart')->find($id);
        if(!empty($cart)) {
            $this->getEntityManager()->remove($cart);
            $this->getEntityManager()->flush();
            return new JsonModel($cart->toArray());
        }
        $this->getResponse()->setStatusCode(400);
        return new JsonModel();
    }

    function getSavedItemsLoggedUserAction() {
        $helper = $this->CommonHelper();
        $cart_items = $helper->savedItems($this->identity()->getId());
        if(!empty($cart_items)) {    
             $cart_items = array_map(function($cart_item){
                return $cart_item->toArray();
            }, $cart_items); 
            return  new JsonModel(array('status'=>'ok','data'=>$cart_items));
           
        }
        return new JsonModel(array('status'=>'ok','data'=>"Cart is empty"));

    }

    function deleteAllCartItemsAction() {
        $helper = $this->CommonHelper();
        $cart_items = $helper->savedItems($this->identity()->getId());
        if(!empty($cart_items)) {  
            foreach ($cart_items as $key => $cart) {
                $id = $cart->getId();
                $this->delete($id);
            }
        }  
    }
}
