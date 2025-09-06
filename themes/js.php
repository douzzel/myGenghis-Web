<?php
header('Content-type: text/javascript');
include('../classes/JavaScriptPacker.class.php');
$script = file_get_contents('assets/js/'.$_GET['file'].'.js');
$packer = new JavaScriptPacker($script, 'Numeric', true, false);
echo $packer->pack();
?>