<?php
$path = 'app/Services/Enterprise/LicenseGuard.php';
$content = file_get_contents($path);
if (!preg_match('/base64_decode\(\'([^\']+)\'\)/', $content, $m)) {
    echo "no match 1\n";
    exit(1);
}
$decoded = gzinflate(base64_decode($m[1]));

if (!preg_match('/hex2bin\(\'([^\']+)\'\)/', $decoded, $m2)) {
    echo "no match 2\n";
    exit(1);
}
$a = hex2bin($m2[1]);
$b = strrev($a);
$c = base64_decode($b);
$final = gzinflate($c);
echo $final;
