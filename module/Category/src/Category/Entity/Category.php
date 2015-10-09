<?php
  
namespace Category\Entity;
  
use Doctrine\ORM\Mapping as ORM;

use Zend\InputFilter\InputFilter;

use User\Entity\Base;
  
/**
 * A Categorym.
 *
 * @ORM\Entity
 * @ORM\Table(name="category")
 * @property string $name
 * 
 */
class Category extends Base
{
    /**
     * @ORM\Column(type="string", name="name")
    **/
    protected $name;
  
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

    public function getInputFilter($em){
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
 
            $inputFilter->add(array(
                'name'     => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'Application\Filter\MyFilter')
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 5,
                            'max'      => 100,
                        ),
                    ),array(
                        'name'  => 'User\Validator\NoEntityExists',
                        'options'=>array(
                            'entityManager' =>$em,
                            'class' => 'Category\Entity\Category',
                            'property' => 'name',
                            'exclude' => array(
                                array('property' => 'id', 'value' => $this->getId())
                            )
                        )
                    )
                )
            ));
 
            $this->inputFilter = $inputFilter;
        }
 
        return $this->inputFilter;
    }
}