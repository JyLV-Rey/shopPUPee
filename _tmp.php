<?php
$c = file_get_contents('resources/views/common/navbar.blade.php');
$lines = explode("\n", $c);
for ($i = 38; $i <= 55; $i++) {
    echo ($i + 1) . ': ' . ($lines[$i] ?? '') . "\n";
}
