<?php
header('Content-type: text/css');
ob_start('ob_gzhandler');
header('Cache-Control: max-age=31536000, must-revalidate');

if(!isset($_GET['file']) || preg_match("[^a-zA-Z0-9_-]", $_GET['file'])) die();
include('../classes/CSS.class.php');
$css = file_get_contents('assets/css/'.$_GET['file'].'.css');
$compressor = new CSSmin();
// Override any PHP configuration options before calling run() (optional)
$compressor->set_memory_limit('256M');
$compressor->set_max_execution_time(120);
// Compress the CSS code in 1 long line and store the result in a variable
$output_css = $compressor->run($css);
// You can change any PHP configuration option between run() calls
// and those will be applied for that run
$compressor->set_pcre_backtrack_limit(3000000);
$compressor->set_pcre_recursion_limit(150000);
// Do whatever you need with the compressed CSS code
echo $output_css;
?>