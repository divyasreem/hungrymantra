<?php
namespace User\Controller;

use User\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class UserController extends AbstractRestfulJsonController{

    protected $em;

    public function getEntityManager(){
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function indexAction(){
        $users = $this->getEntityManager()->getRepository('User\Entity\User')->findAll();
        
        $users = array_map(function($user){
            return $user->toArray();
        }, $users);
        return new JsonModel($users);
    }
	
    public function getList(){   
        // Action used for GET requests without resource Id
        if($this->identity()->getRole() != 'user') {
            $users = $this->getEntityManager()->getRepository('User\Entity\User')->findAll();
            $users = array_map(function($user){
                return $user->toArray();
            }, $users);
            $this->getResponse()->setStatusCode(200);
            return new JsonModel($users);
        }
        $this->getResponse()->setStatusCode(401);
        return new JsonModel(array('status'=> 'error','data'=>"Unautorized access/not a valid user"));
    }

    public function get($id){   
        // Action used for GET requests with resource Id
        if($this->identity()->getRole() != 'user' || $this->identity()->getId() == $id) {
            $user = $this->getEntityManager()->getRepository('User\Entity\User')->find($id);
            $this->getResponse()->setStatusCode(200);
            return new JsonModel(
        		$user->toArray()
        	);
        } 
         $this->getResponse()->setStatusCode(401);
         return new JsonModel(array('status'=> 'error','data'=>"Unautorized access/not a valid user"));
    }

    public function getMyUsersAction(){
        return $this->getList();
    }

    public function create($data){
        $this->getEntityManager();
        $user = new \User\Entity\User($data);
        $user->validate($this->em);
        $user->setPassword($this->encriptPassword(
                           $this->getStaticSalt(), 
                           $user->getPassword()
        ));
        $user->setCreatedDate(date('Y-m-d'));
        
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        $this->getResponse()->setStatusCode(200);
        return new JsonModel($user->toArray());
    }

    public function getStaticSalt() {
        $staticSalt = '';
        $config = $this->getServiceLocator()->get('Config');
        $staticSalt = $config['static_salt'];       
        return $staticSalt;
    }

    public function encriptPassword($staticSalt, $password) {
        $password      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $staticSalt ), $password, MCRYPT_MODE_CBC, md5( md5( $staticSalt ) ) ) );
        // return $password = md5($staticSalt . $password);
        return $password;
    }

    public function update($id, $data){
        // Action used for PUT requests
        if(!empty($id) && !empty($data)) {
            if($this->identity()->getId() == $id){
                $user = $this->getEntityManager()->getRepository('User\Entity\User')->find($id);
                $user->set($data);
                $user->validate($this->em);
                
                $this->getEntityManager()->flush();
                $this->getResponse()->setStatusCode(200);
                return new JsonModel($user->toArray());
            }
            $this->getResponse()->setStatusCode(401);
            return new JsonModel(array('status'=> 'error','data'=>"Unautorized access/not a valid user"));
        }
        $this->getResponse()->setStatusCode(400);
        return new JsonModel(array('status'=> 'error','data'=>"Post data is required"));
    }


    public function loginAction() {
       $request = $this->getRequest();
       $data = $this->getRequest()->getContent(); 
        if ($request->isPost() && !empty($data)) {
            $data = (!empty($data))? get_object_vars(json_decode($data)) : '';
            $data = $this->commonLogin($data, true);
            if($data['status'] == 'ok') {
                $this->getResponse()->setStatusCode(200);
            } else {
                $this->getResponse()->setStatusCode(400);
            }
            return new JsonModel($data);
        }
        $this->getResponse()->setStatusCode(400);
        return new JsonModel(array('status'=> 'error','data'=>"Post data is required"));
    }

    function commonLogin($data, $has_encrypt) {
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        $adapter = $authService->getAdapter();
        $adapter->setIdentityValue($data['email']); 
        if($has_encrypt)
            $data['password'] = $this->encriptPassword($this->getStaticSalt(),  $data['password']);
        $adapter->setCredentialValue($data['password']); 
        $authResult = $authService->authenticate();
        if ($authResult->isValid()) {
           $identity = $authResult->getIdentity();
           $sessionManager = new \Zend\Session\SessionManager();
           $sessionManager->regenerateId();

           $user = $identity->toArray();
           unset($user['password']);
           
           return array('status'=>'ok', 'data' => $user);
        } else {
            return array('status'=> 'error','data'=>"Invalid Credentials");
        }
    }

    public function logoutAction() {
        $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        $auth->clearIdentity();
        $this->getResponse()->setStatusCode(200);
        return new JsonModel(array('status'=>'successfully logged out'));
    }

    public function invalidAccessAction() {
        $this->getResponse()->setStatusCode(401);
        return new JsonModel();
    }

    public function walletRechargeAction($amount) {
        $logged_user_id = $this->identity()->getId();
        $user = $this->get($logged_user_id);
        $user->setWalletAmount($user->getWalletAmount() + $amount);
        $user->validate($this->em);
        
        $this->getEntityManager()->flush();
        
        return new JsonModel($user->toArray());
        
    }

}
