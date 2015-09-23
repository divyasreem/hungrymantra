<?php
namespace Category\Controller;

use User\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

class TransactionController extends AbstractRestfulJsonController{

    protected $em;

    public function getEntityManager(){
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }
	
    public function getList(){
        // Action used for GET requests without resource Id
        $transactions = $this->getEntityManager()->getRepository('Category\Entity\Transaction')->findAll();
        $transactions = array_map(function($transaction){
            return $transaction->toArray();
        }, $transactions);
        return new JsonModel($transactions);
    }

     public function getMyCategoriesAction(){
        return $this->getList();
    }

    public function get($id){  
        // Action used for GET requests with resource Id
        $transaction = $this->getEntityManager()->getRepository('Category\Entity\Transaction')->find($id);
        if(!empty($transaction)) {
            return new JsonModel(
                $transaction->toArray()
            );
        }
        $this->getResponse()->setStatusCode(400);
        return new JsonModel();
    }

    public function create($data){
        $order = $this->calculateOrderAmount();
        
        $this->getEntityManager();
        $transaction = new \Category\Entity\Transaction($data);
        $transaction->setUser($this->identity());
        $transaction->setCreatedDate(date("Y-m-d"));
        $transaction->validate($this->em);
        $this->getEntityManager()->persist($transaction);
        $this->getEntityManager()->flush();
        $order_amount = $this->CreateOrderItem($transaction);
        
        $transaction_update['amount'] = $order_amount;
        $this->update($transaction->getId(), $transaction_update);

        return new JsonModel($transaction->toArray());
    }

    public function update($id, $data){
        // Action used for PUT requests
        $transaction = $this->getEntityManager()->getRepository('Category\Entity\Transaction')->find($id);
        $transaction->set($data);
        $transaction->validate($this->em);
        
        $this->getEntityManager()->flush();
        
        return new JsonModel($transaction->toArray());
    }

    public function delete($id){
        // Action used for DELETE requests
        $transaction = $this->getEntityManager()->getRepository('Category\Entity\Transaction')->find($id);
        $this->getEntityManager()->remove($transaction);
        
        $this->getEntityManager()->flush();
        
        return new JsonModel($transaction->toArray());
    }

    private function CreateOrderItem($transaction) {
        $helper = $this->CommonHelper();
        $cart_items = $helper->savedLoans($this->identity()->getId());
        $total_amount = 0;
       
        foreach($cart_items as $item) {
            $order_item = new \Category\Entity\OrderItem();
            $order_item->setQuantity($item->getQuantity());
            $order_item->setItem($item->getItem());
            $order_item->setUnitPrice($item->getItem()->getPrice());
            $order_item->setSubTotal($item->getItem()->getPrice() * $item->getQuantity());
            $order_item->setTransaction($transaction );
            $this->getEntityManager()->persist($order_item);
            $this->getEntityManager()->flush();
            $total_amount = $total_amount + $order_item->getSubTotal();

            $this->forward()->dispatch('Category\Controller\Cart', array(
                'action' => 'deleteAllCartItems'
            ));
        }

        return $total_amount;
    }

    public function getOrderDetailsAction() {
       $transaction_id = $this->params()->fromQuery('transaction_id');
       $em = $this->getEntityManager();
       $queryBuilder = $em->createQueryBuilder();
       $order_items = $queryBuilder->select('o')->from('Category\Entity\OrderItem', 'o')
                                                        ->where('o.transaction = :transaction_id')
                                                        ->setParameter('transaction_id', $transaction_id)
                                                        ->getQuery()
                                                        ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        // $order_items = array_map(function($order_item){
        //     return $order_item->toArray();
        // }, $order_items);
        
        return new JsonModel(array('status'=>'ok', "data" => $order_items));
    }

    public function calculateOrderAmount() {
        $helper = $this->CommonHelper();
        $total_amount = 0;
        $cart_items = $helper->savedLoans($this->identity()->getId());
        foreach($cart_items as $item) {
            $amount = $item->getItem()->getPrice() * $item->getQuantity();
            $total_amount = $total_amount + $amount;
        }

        return array('total_amount' => $total_amount, 'cart_items' => $cart_items);
    }

}
