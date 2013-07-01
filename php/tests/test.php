<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require('../formulatrix.php');

$form = new FX();
$form -> setFields('name', 'description');
echo $form -> getForm();

?>