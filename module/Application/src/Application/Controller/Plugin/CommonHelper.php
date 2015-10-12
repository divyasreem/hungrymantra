<?php
namespace Application\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\JsonModel;

use Zend\Mail;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class CommonHelper extends AbstractPlugin{

	public $em;
  
	public function getEntityManager(){
	    if (null === $this->em) {
	      $this->em =  $this->getController()->getServiceLocator()->get('doctrine.entitymanager.orm_default'); 
	    } 
	    return $this->em;
  }

  public function savedLoans($logged_user_id) {
    $em = $this->getEntityManager();
    $queryBuilder = $em->createQueryBuilder();
    $cart_loans = $queryBuilder->select('c')->from('Category\Entity\Cart', 'c')
                                           ->where('c.user = :user_id')
                                           ->setParameter('user_id', $logged_user_id)
                                           ->getQuery()
                                           ->getResult();
    return $cart_loans;
  } 

  function send_mail($viewContent, $email, $data, $template_name) {
    $transport = $this->getController()->getServiceLocator()->get('mail.transport');
    $options = new Mail\Transport\SmtpOptions(array(
                'name' => $transport->getOptions()->name,
                'host' => $transport->getOptions()->host,
                'port'=> $transport->getOptions()->getConnectionConfig()['port'],
                'connection_class' => $transport->getOptions()->connection_class,
                'connection_config' => array(
                    'username' => $transport->getOptions()->getConnectionConfig()['username'],
                    'password' => $transport->getOptions()->getConnectionConfig()['password'],
                    'ssl'=> $transport->getOptions()->getConnectionConfig()['ssl'],
                ),
    ));

                     
    $this->renderer = $this->getController()->getServiceLocator()->get('ViewRenderer');
    $viewContent->setTemplate("email/$template_name"); 
    $viewContent = $this->renderer->render($viewContent);


    $textPart = new MimePart($viewContent);
    $textPart->type = "text/plain";


    // make a header as html
    $html = new MimePart($viewContent);
    $html->type = "text/html";
    $body = new MimeMessage();
    $body->setParts(array($textPart,$html));

    // instance mail 
    $mail = new Mail\Message();
    $mail->setEncoding("UTF-8");
    $mail->setBody($body); // will generate our code html from template.phtml
    $mail->getHeaders()->get('content-type')->setType('multipart/alternative');
    $mail->setFrom($transport->getOptions()->getConnectionConfig()['username'],$transport->getOptions()->name);
    $mail->setTo($email);
    $mail->setSubject($data['subject']);

    $transport = new Mail\Transport\Smtp($options);
    $transport->send($mail);

    // $renderer = $this->getController()->getServiceLocator()->get('Zend\View\Renderer\RendererInterface');
    // $viewContent->setTemplate("email/$template_name"); 
    // $content = $renderer->render($viewContent);
   
    // $viewLayout = new \Zend\View\Model\ViewModel(array('content' => $content));
    // $viewLayout->setTemplate('email/layout'); 
    
    // $html = new \Zend\Mime\Part($renderer->render($viewLayout));
    // $html->type = 'text/html';
    // $body =  new \Zend\Mime\Message();
    // $body->setParts(array($html));
    // $transport = $this->getController()->getServiceLocator()->get('mail.transport');
    // $message = new \Zend\Mail\Message();
    // // $this->getRequest()->getServer();  
    // $message->addTo($email)
    //         ->addFrom($transport->getOptions()->getConnectionConfig()['username'])
    //         ->setSubject($data['subject'])
    //         ->setBody($body);
    // $transport->send($message);   

    // if(!$transport->send()){
    //  echo "Mailer Error: " . $transport->ErrorInfo;
    // }else{
    //  echo "E-Mail has been sent";
    // }
  }

  function updateUser($user_id, $data) {
    $user = $this->getEntityManager()->getRepository('User\Entity\User')->find($user_id);
    $user->set($data);
    $user->validate($this->em);
    $this->getEntityManager()->flush();

    return $user;
  }


  function check_img($file) {
       $x = getimagesize($file);

       switch ($x['mime']) {
          case "image/png":
             $response = 'this is a gif image.';
             break;
          case "image/jpeg":
             $response = 'this is a jpeg image.';
             break;
          case "image/jpg":
             $response = 'this is a png image.';
             break;
          default:
             if (!unlink($file))           {
               // echo ("Error deleting $file");
             }  else           {
               // echo ("Deleted $file");
             }
             $response = 'Please upload a valid image';
             break;
       }

       return $response;    
    } 

}

?>