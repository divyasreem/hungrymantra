<?php
  
namespace Category\Entity;
  
use Doctrine\ORM\Mapping as ORM;

use Zend\InputFilter\InputFilter;

use User\Entity\Base;
  
/**
 * A Order Item.
 *
 * @ORM\Entity
 * @ORM\Table(name="order_item")
 * @property string $name
 * 
 */
class OrderItem extends Base
{

    /**
     * @ORM\Column(type="decimal", scale=2)
    **/
    protected $unit_price;

    /**
     * @ORM\Column(type="decimal", scale=2)
    **/
    protected $sub_total;

    /**
     * @ORM\Column(type="integer")
    **/
    protected $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="Category\Entity\Item")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
    **/
    protected $item;

    /**
     * @ORM\ManyToOne(targetEntity="Category\Entity\Transaction")
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="id")
    **/
    protected $transaction;
  
    /**
     * Convert the object to an array.
     *
     * @return array
     */
    
    public function __construct($data = array()){
        parent::__construct($data);
    }

    public function getAmount(){
        return $this->amount;
    }

    public function setUnitPrice($unit_price){
        $this->unit_price = $unit_price;
    }

    public function setItem($item){
        $this->item = $item;
    }

    public function setTransaction($transaction){
        $this->transaction = $transaction;
    }

    public function setSubTotal($sub_total){
        $this->sub_total = $sub_total;
    }

    public function setQuantity($quantity){
        $this->quantity = $quantity;
    }

    public function getSubTotal(){
        return $this->sub_total;
    }

    public function getInputFilter($em){
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
 
            $this->inputFilter = $inputFilter;
        }
 
        return $this->inputFilter;
    }
}