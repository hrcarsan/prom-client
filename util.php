<?php

namespace PromClient;

class Util
{
  public static function escapeString($str)
  {
    $str = preg_replace("/\n/", "\\\n", $str);
    //$str = preg_replace("/\\(?!n)/", '\\\\', $str);

    return $str;
  }


  public static function escapeLabelValue($str)
  {
  	if (!is_string($str))
    {
		  return $str;
	  }

	  return preg_replace('/"/', '\\"', Util::escapeString($str));
  }


  public static function getValueAsString($value)
  {
  	if (!is_numeric($value))
    {
		  return 'Nan';
	  }

    return "{$value}";
  }


  public static function hashObject($labels = array())
  {
    $keys = array_keys($labels);
    $size = count($keys);

    if ($size === 0)
    {
      return '';
    }

    if ($size > 1)
    {
      sort($keys);
    }

    for ($i = 0, $hash = ''; $i < $size - 1; $i++)
    {
      $hash .= $keys[$i].":".$labels[$keys[$i]].",";
    }

    $hash .= $keys[$i].":".$labels[$keys[$i]];

    return $hash;
  }
}
