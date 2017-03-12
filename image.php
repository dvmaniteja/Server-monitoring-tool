<?php
$abc=($_GET['value']);
header("Content-Type: image/png");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . filesize("$abc"));

$fp = fopen($abc, 'rb');
fpassthru($fp);
unlink($abc);
exit();
?>
