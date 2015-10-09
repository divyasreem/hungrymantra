<?php
namespace Application\Filter\MyFilter;
use Zend\Filter\FilterInterface;

class MyFilter implements FilterInterface {
	public function filter($value) {
		// perform some transformation upon $value to arrive on $valueFiltered
		$valueFiltered = ucwords($value);
		return $valueFiltered;

	}
}
?>