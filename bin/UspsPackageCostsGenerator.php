<?php

chdir(dirname(__DIR__));

$filePath = $argv[1] ?? 'data/ParselSelectGround.csv';

if (!file_exists($filePath)) {
    echo "File $filePath not exist.";
}

$rows = array_map(function ($row) {
    return explode(",", $row);
}, explode("\n", file_get_contents($filePath)));

$str = "[\n";
foreach ($rows as $values) {
    $values = array_map(function ($v) {
        return (float)$v;
    }, $values);

    $str .= ("[" . implode(", ", $values) . "],\n");
}
$str .= '];';

echo $str;