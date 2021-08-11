<?php
$ip = $_SERVER['REMOTE_ADDR'];
echo gethostbyaddr($ip);

echo $_ENV['HOSTNAME']; 

?>