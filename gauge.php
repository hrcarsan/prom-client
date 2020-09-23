<?php

namespace PromClient;

class Gauge extends Metric
{
  public $type = 'gauge';


  public function set($labels = array(), $value)
  {
    $this->setValue($labels, $value);
  }


  public function inc($labels = array(), $value = 1)
  {
  	$this->set($labels, $this->getValue($labels) + $value);
  }


  public function dec($labels = array(), $value = 1)
  {
  	$this->set($labels, $this->getValue($labels) - $value);
  }
}
