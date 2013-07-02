<?php
$lines = array();
exec('find en_US -type f', $lines);
foreach($lines AS $line) {
    $targetLine = 'zh_TW' . substr($line, 5);
    if(!file_exists($targetLine)) {
        copy($line, $targetLine);
    } elseif(substr($line, -3) === 'csv') {
        $enStack = array();
        $fh = fopen($line, 'r');
        while($row = fgetcsv($fh, 1024)) {
            $enStack[$row[0]] = $row[1];
        }
        fclose($fh);
        $fh = fopen($targetLine, 'r');
        while($row = fgetcsv($fh, 1024)) {
            if(isset($enStack[$row[0]])) {
                $enStack[$row[0]] = $row[1];
            }
        }
        fclose($fh);
        $fh = fopen($targetLine, 'w');
        $search = array(
            '"', '\\""'
        );
        $replace = array(
            '""', '\\"'
        );
        foreach($enStack AS $key => $val) {
            $key = str_replace($search, $replace, $key);
            $val = str_replace($search, $replace, $val);
            fputs($fh, "\"{$key}\",\"{$val}\"\n");
        }
        fclose($fh);
    }
}