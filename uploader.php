<?php

$postData = hex2bin($_POST['data']);
$f = fopen("./attachments/tmp.mid", "wb");
fwrite($f, $postData);
fclose($f);
echo 'succ';

?>