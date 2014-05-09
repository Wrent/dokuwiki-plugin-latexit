<?php
$filename = "counter";
if(file_exists($filename)) {
    $cnt = file_get_contents($filename);
} else {
    $cnt = 0;
}
$cnt++;
file_put_contents($filename, $cnt);
Header("Location: latexit-v1.0.zip");