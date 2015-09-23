<?php
  
namespace User\Entity;
  
use Doctrine\ORM\Mapping as ORM;

use Zend\InputFilter\InputFilter;

use User\Entity\Base;
  
/**
 * A User.
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @property string $fname
 * @property string $lname
 * 
 */
class User extends Base
{
    /**
     * @ORM\Column(type="string", name="full_name")
    **/
    protected $full_name;

    /**
     * @ORM\Column(type="string", name="first_name", nullable= TRUE)
    **/
    protected $first_name;

    /**
     * @ORM\Column(type="string", name="last_name", nullable= TRUE)
    **/
    protected $last_name;


    /**
     * @ORM\Column(type="string")
    **/
    protected $email;

    /**
     * @ORM\Column(type="string", length=100)
    **/
    protected $password;

    /**
     * @ORM\Column(type="string", length=20, nullable= TRUE)
    **/
    protected $contact_no;

    /**
     * @ORM\Column(type="string", length=20)
    **/
    public $role = 'user';

    /**
     * @ORM\Column(type="decimal", scale=2)
    **/
    public $wallet_amount = 0.00;

    /**
     * @ORM\Column(type="boolean")
    **/
    protected $is_admin = false;
  
    /**
     * Convert the object to an array.
     *
     * @return array
     */
    
    public function __construct($data){
        parent::__construct($data);
    }

    public function getFullName(){
        return $this->fname . " " . $this->lname;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setCreatedDate($created_date) {
      $this->created_date = $created_date;
    }    

    public function getWalletAmount() {
        return $this->wallet_amount;
    }

    public function getInputFilter($em){
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
 
            $inputFilter->add(array(
                'name'     => 'first_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 5,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));
 
            $inputFilter->add(array(
                'name'     => 'last_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));
 
            $inputFilter->add(array(
                'name'     => 'password',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'EmailAddress',
                    ),
                    array(
                        'name'  => 'User\Validator\NoEntityExists',
                        'options'=>array(
                            'entityManager' =>$em,
                            'class' => 'User\Entity\User',
                            'property' => 'email',
                            'exclude' => array(
                                array('property' => 'id', 'value' => $this->getId())
                            )
                        )
                    )
                ),
            ));
 
            $this->inputFilter = $inputFilter;
        }
 
        return $this->inputFilter;
    }
}