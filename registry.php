<?php

namespace PromClient;

class Registry
{
	const HEADER_CONTENT_TYPE = 'Content-Type: text/plain; version=0.0.4; charset=utf-8';
  const XCACHE_ID = "PROMETHEUS_METRICS";

  /** @var Registry $globalRegistry */
  protected static $globalRegistry;
  protected $metrics = array();


  /*
   * @return Registry
   */
  public static function get()
  {
    if (!self::$globalRegistry)
    {
      self::$globalRegistry = new Registry();
    }

    return self::$globalRegistry;
  }


  function __construct()
  {
    $this->load();
  }


  public function load()
  {
    $metrics = xcache_get(self::XCACHE_ID);
    $this->metrics = array();

    foreach ($metrics as $metric)
    {
      $this->metric($metric);
    }
  }


  public function loadAll()
  {
    $this->load();

    foreach ($this->metrics as $metric)
    {
      $metric->load();
    }
  }


  public function save()
  {
    $metrics = array();

    foreach ($this->metrics as $metric)
    {
      $metrics[] = array(
        'help' => $metric->help,
        'name' => $metric->name,
        'type' => $metric->type,
      );
    }

    xcache_set(self::XCACHE_ID, $metrics, 3600);
  }


  public function saveAll()
  {
    $this->save();

    foreach ($this->metrics as $metric)
    {
      $metric->save();
    }
  }


  public function clear()
  {
    foreach ($this->metrics as $metric)
    {
      $metric->reset();
    }

    $this->metrics = array();
    $this->save();
  }


  public function metric($options)
  {
    if (!$name = $options['name']) return;

    if ($this->metrics[$name])
    {
      $metric = $this->metrics[$name];
      $metric->help = $options['help'];
    }
    else
    {
      switch ($options['type'])
      {
        case 'counter': $metric = new Counter($options); break;
        case 'gauge':   $metric = new Gauge($options);   break;
        default: return;
      }

		  $this->metrics[$metric->name] = $metric;
    }

    return $this->metrics[$metric->name];
  }


  public function counter($options)
  {
    $options['type'] = 'counter';
    $metric = $this->metric($options);

    $this->save();
    return $metric;
  }


  /**
   * @return Gauge
   */
  public function gauge($options)
  {
    $options['type'] = 'gauge';
    $metric = $this->metric($options);

    $this->save();
    return $metric;
  }


	public function metrics()
  {
		$metrics = '';

		foreach ($this->metrics as $metric)
    {
			$metrics .= $this->getMetricAsPrometheusString($metric)."\n\n";
		}

		return substr($metrics, 0, -1);;
	}


  public function getMetricAsPrometheusString($metric)
  {
		$item = $metric->get();
		$name = Util::escapeString($item['name']);
		$help = "# HELP {$name} ".Util::escapeString($item['help']);
		$type = "# TYPE {$name} {$item['type']}";

		$values = '';

		foreach ($item['values'] as $val)
    {
			$labels = '';

			foreach ($val['labels'] as $label_key => $label_val)
      {
				$labels .= $label_key."=\"".Util::escapeLabelValue($label_val)."\",";
			}

			$metricName = $item['name'];

			if ($labels)
      {
				$metricName .= "{".substr($labels, 0, -1)."}";
			}

			$values .= "{$metricName} ".Util::getValueAsString($val['value'])."\n";
		}

		return trim("{$help}\n{$type}\n{$values}");
	}


  public function exposeMetrics()
  {
    header(Registry::HEADER_CONTENT_TYPE);
    print $this->metrics();
  }
}
