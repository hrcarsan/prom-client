<?php

namespace PromClient;

class Metric
{
  public $name = '';
  public $type = '';
  public $help = '';
  public $hashMap = array();


  function __construct($options = array())
  {
    $this->name = $options['name'];
    $this->help = $options['help'];
  }


  public function load()
  {
    $values = xcache_get(Registry::XCACHE_ID."_".$this->name);

    if (!$values) return;

    foreach ($values as $value)
    {
      $this->setValue($value['labels'], $value['value']);
    }
  }


  public function save()
  {
    xcache_set(Registry::XCACHE_ID."_".$this->name, array_values($this->hashMap), 3600);
  }


  public function setValue($labels = array(), $value)
  {
	  $hash = Util::hashObject($labels);
	  $this->hashMap[$hash] = array(
      'value'  => is_numeric($value)? $value : 0,
      'labels' => is_array($labels)? $labels: array(),
	  );

    $this->save();
  }


  public function get()
  {
    $this->load();

		return array(
			'help' => $this->help,
			'name' => $this->name,
			'type' => $this->type,
			'values' => array_values($this->hashMap),
		);
	}


  public function getValue($labels)
  {
		$hash  = Util::hashObject($labels);
    $this->load();

    return $this->hashMap[$hash]? $this->hashMap[$hash]['value']: 0;
	}


  public function reset()
  {
    $this->hashMap = array();
    $this->save();
  }
}
