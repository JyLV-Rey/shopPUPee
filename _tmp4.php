<?php
$c = file_get_contents('resources/views/home/index.blade.php');
$lines = explode("\n", $c);
for ($i = 39; $i <= 50; $i++) {
    echo ($i + 1) . ': ' . ($lines[$i] ?? '') . "\n";
}
