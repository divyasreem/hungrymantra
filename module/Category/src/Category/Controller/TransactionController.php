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
        $this->getEntityManager();
        $item = new \Category\Entity\Transaction($data);
        $transaction = $this->getEntityManager()->getRepository('Category\Entity\Transaction')->find($data['category_id']);
        $item->setCategory($transaction);
        $item->validate($this->em);
        
        $this->getEntityManager()->persist($item);
        $this->getEntityManager()->flush();
        
        return new JsonModel($item->toArray());
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

}
