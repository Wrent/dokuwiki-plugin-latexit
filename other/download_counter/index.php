<?php
$filename = "counter";
if(file_exists($filename)) {
    $cnt = file_get_contents($filename);
} else {
    $cnt = 0;
}
?>

<div style="text-align: center;">
    <h1>Download count: <?php echo $cnt;?></h1>
</div>