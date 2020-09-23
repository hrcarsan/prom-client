<?php

include "vendor/prom-client/autoload.php";

use PromClient\Registry as PromRegistry;

$promRegistry = PromRegistry::get();
$promRegistry->exposeMetrics();

