<?php
$filename = "counter";
if(file_exists($filename)) {
    $cnt = file_get_contents($filename);
} else {
    $cnt = 0;
}
$cnt++;
file_put_contents($filename, $cnt);
Header("Location: https://github.com/Wrent/dokuwiki-plugin-latexit/archive/master.zip");