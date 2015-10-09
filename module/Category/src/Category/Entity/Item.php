<?php
  
namespace Category\Entity;
  
use Doctrine\ORM\Mapping as ORM;

use Zend\InputFilter\InputFilter;

use User\Entity\Base;
  
/**
 * A Categorym.
 *
 * @ORM\Entity
 * @ORM\Table(name="item")
 * @property string $name
 * 
 */
class Item extends Base
{
    /**
     * @ORM\Column(type="string", name="name")
    **/
    protected $name;

    /**
     * @ORM\Column(type="decimal", scale=2)
    **/
    protected $price;

    /**
     * @ORM\Column(type="integer", nullable= TRUE)
    **/
    protected $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
    **/
    protected $category;
  
    /**
     * Convert the object to an array.
     *
     * @return array
     */
    
    public function __construct($data){
        parent::__construct($data);
    }

    public function getName(){
        return $this->name;
    }

    public function getPrice(){
        return $this->price;
    }

    public function getQuantity(){
        return $this->quantity;
    }

    public function setCategory($category){
        $this->category = $category;
    }

    public function getInputFilter($em){
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
 
            $inputFilter->add(array(
                'name'     => 'name',
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
                            'min'      => 3,
                            'max'      => 100,
                        ),
                    ),array(
                        'name'  => 'User\Validator\NoEntityExists',
                        'options'=>array(
                            'entityManager' =>$em,
                            'class' => 'Category\Entity\Item',
                            'property' => 'name',
                            'exclude' => array(
                                array('property' => 'id', 'value' => $this->getId())
                            )
                        )
                    )
                )
            ));
            $inputFilter->add(array(
                'name' => 'price',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\I18n\Validator\Float',
                    ),
                    array(
                        'name' => 'GreaterThan',
                        'options' => array(
                          'min' => 0
                        )                    
                    ) 
                ),            
            ));
            $inputFilter->add(array(
                'name' => 'category',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
               
            ));    
 
            $this->inputFilter = $inputFilter;
        }
 
        return $this->inputFilter;
    }
}