<?php
$path = 'app/Services/Enterprise/LicenseGuard.php';
$content = file_get_contents($path);
if (!preg_match('/base64_decode\(\'([^\']+)\'\)/', $content, $m)) {
    echo "no match";
    exit(1);
}
$decoded = gzinflate(base64_decode($m[1]));
echo $decoded;
