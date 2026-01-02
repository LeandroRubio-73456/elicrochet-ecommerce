<?php

// Download WOW.js from a reliable CDN

$wowUrl = 'https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js';
$baseDir = __DIR__.'/public/assets/js/libs/';

if (! is_dir($baseDir)) {
    mkdir($baseDir, 0777, true);
}

echo "Downloading WOW.js from $wowUrl\n";
$content = file_get_contents($wowUrl);

if ($content) {
    file_put_contents($baseDir.'wow.min.js', $content);
    echo "Saved wow.min.js to $baseDir\n";
} else {
    echo "FAILED to download WOW.js\n";
}
