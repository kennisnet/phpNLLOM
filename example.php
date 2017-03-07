<?php
require('vendor/autoload.php');

use Kennisnet\NLLOM;

$lom = new NLLOM(true, true);

$lom->saveAsXML();