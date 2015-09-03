<?php
  
namespace Category\Entity;
  
use Doctrine\ORM\Mapping as ORM;

use Zend\InputFilter\InputFilter;

use User\Entity\Base;
  
/**
 * A Categorym.
 *
 * @ORM\Entity
 * @ORM\Table(name="cart")
 * @property string $name
 * 
 */
class Cart extends Base
{

    /**
     * @ORM\Column(type="decimal", scale=2)
    **/
    protected $amount;

    /**
     * @ORM\ManyToOne(targetEntity="Cart")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
    **/
    protected $item;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    **/
    protected $user;
  
    /**
     * Convert the object to an array.
     *
     * @return array
     */
    
    public function __construct($data){
        parent::__construct($data);
    }

    public function getAmount(){
        return $this->amount;
    }

    public function setUser($user){
        $this->user = $user;
    }

    public function setItem($item){
        $this->item = $item;
    }

    public function getInputFilter($em){
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
 
            $this->inputFilter = $inputFilter;
        }
 
        return $this->inputFilter;
    }
}