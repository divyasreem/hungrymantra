<?php

namespace Category\Entity;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use User\Entity\Base;
use User\Entity\User;

/** Transaction
 * @ORM\Entity
 * @ORM\Table(name="transaction")
 * @property string $amount
 * @property string $user_id
 * @property string $created_date
 * @property string $type
 * @property string $status
*/
class Transaction extends Base {

  /**
   * @ORM\Column(type="decimal", scale=2, nullable= TRUE)
   *
  **/
  protected $amount;

  /**
   * @ORM\Column(type="string")
   *
  **/
  protected $created_date;

  /**
   * @ORM\Column(type="string")
   *
  **/
  protected $type;

  /**
   * @ORM\Column(type="string")
   *
  **/
  protected $status;

  /**
   * @ORM\Column(type="string")
   *
  **/
  protected $source = 'item';
 
  /**
   * @ORM\ManyToOne(targetEntity="User\Entity\User", cascade={"persist", "remove"})
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
  **/
  protected $user;

  public function __construct($data = array()) {
      parent::__construct($data);
  }

  public function getAmount() {
    return $this->amount;
  }
   
  public function setAmount($amount) {
    $this->amount = $amount;
  }

  public function getLoan() {
      return $this->loan;
  }

  public function setLoan($loan) {
      $this->loan = $loan;
  }

  public function getCreatedDate() {
    return $this->created_date;
  }
   
  public function setCreatedDate($created_date) {
    $this->created_date = $created_date;
  }

  public function getType() {
    return $this->type;
  }

  public function setType($type) {
    $this->type = $type;
  }

  public function getStatus() {
    return $this->status;
  }
   
  public function setStatus($status) {
    $this->status = $status;
  }

  public function getUser() {
      return $this->user;
  }

  public function setUser($user) {
      $this->user = $user;
  }

  public function getSource() {
      return $this->source;
  }

  public function setSource($source) {
      $this->source = $source;
  }
}