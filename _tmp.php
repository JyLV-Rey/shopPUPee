<?php
$c = file_get_contents('resources/views/home/index.blade.php');
$lines = explode("\n", $c);
for ($i = 44; $i <= 55; $i++) {
    echo ($i + 1) . ': ' . ($lines[$i] ?? '') . "\n";
}
