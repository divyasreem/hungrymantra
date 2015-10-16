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
        $this->getEntityManager();
        $transaction = new \Category\Entity\Transaction($data);
        $transaction->setCreatedDate(date("Y-m-d"));
        $transaction->setSource($data['source']);
        if($data['source'] == 'order') {
            print_r($data);
            $order = $this->calculateOrderAmount();
            if($order['total_amount'] > $this->identity()->getWalletAmount()) {
                return new JsonModel(array('status'=> 'ok','data' => 'Please recharge your wallet to place an order'));
            }
            $transaction->setUser($this->identity());
            $transaction->setAmount($order['total_amount']);
            $transaction->validate($this->em);
            $this->getEntityManager()->persist($transaction);
            $this->getEntityManager()->flush();
            $order_amount = $this->CreateOrderItem($transaction, $order['cart_items']);
            $data['wallet_amount'] = $this->identity()->getWalletAmount() - $order['total_amount'];
            $user_id = $this->identity()->getId();
        } else if($data['source'] == 'wallet_recharge' && $this->identity()->getRole() != 'user') {
            $user = $this->getEntityManager()->getRepository('User\Entity\User')->find($data['user_id']);
            $transaction->setUser($user);
            $transaction->setAmount($data['amount']);
            $transaction->validate($this->em);
            $this->getEntityManager()->persist($transaction);
            $this->getEntityManager()->flush();
            $data['wallet_amount'] = $user->getWalletAmount() + $data['amount'];
            $user_id = $user->getId();
        }
        $helper = $this->CommonHelper();
        $helper->updateUser($user_id, $data);

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

    private function CreateOrderItem($transaction, $cart_items) {
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
                                                        ->getResult();
        $order_items = array_map(function($order_item){
            $order = $order_item->toArray();
            unset($order['transaction']);
            return $order;
        }, $order_items);
        
        return new JsonModel(array('status'=>'ok', "data" => $order_items));
    }

    public function calculateOrderAmount() {
        $helper = $this->CommonHelper();
        $total_amount = 0;
        $cart_items = $helper->savedItems($this->identity()->getId());
        foreach($cart_items as $item) {
            $amount = $item->getItem()->getPrice() * $item->getQuantity();
            $total_amount = $total_amount + $amount;
        }

        return array('total_amount' => $total_amount, 'cart_items' => $cart_items);
    }

    public function getOrdersAction() {
       $em = $this->getEntityManager();
       $queryBuilder = $em->createQueryBuilder();
       $orders = $queryBuilder->select('t')->from('Category\Entity\Transaction', 't')
                                                        ->where('t.user = :user_id')
                                                        ->setParameter('user_id', $this->identity()->getId())
                                                        ->getQuery()
                                                        ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        return new JsonModel(array('status'=>'ok', "data" => $orders));
    }

}
