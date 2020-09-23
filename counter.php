<?php

namespace PromClient;

class Counter extends Metric
{
  public $type = 'counter';


  public function inc($labels = array(), $value = 1)
  {
  	$this->setValue($labels, $this->getValue($labels) + $value);
  }
}
