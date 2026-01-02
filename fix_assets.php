<?php

// Fix missing fonts and clean up assets

$baseDir = __DIR__.'/public/assets/';
$cssDir = $baseDir.'css/libs/';
$fontsDir = $cssDir.'fonts/';

// Ensure directory exists
if (! is_dir($fontsDir)) {
    mkdir($fontsDir, 0777, true);
    echo "Created fonts directory: $fontsDir\n";
}

// 1. Download Tabler Icons Fonts
$fontFiles = [
    'tabler-icons.woff2' => 'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/fonts/tabler-icons.woff2',
    'tabler-icons.woff' => 'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/fonts/tabler-icons.woff',
    'tabler-icons.ttf' => 'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/fonts/tabler-icons.ttf',
];

echo "Downloading Tabler fonts...\n";
foreach ($fontFiles as $filename => $url) {
    $content = @file_get_contents($url);
    if ($content) {
        file_put_contents($fontsDir.$filename, $content);
        echo "Downloaded: $filename\n";
    } else {
        echo "Failed to download: $filename from $url\n";
    }
}

// 2. Fix CSS file to ensure paths are correct
$cssFile = $cssDir.'tabler-icons.min.css';
if (file_exists($cssFile)) {
    $cssContent = file_get_contents($cssFile);
    // The CSS might have version query strings like ?v=2.47.0 we need to handle or remove in the CSS file
    // Or we just ensure the CSS points to simple filenames and we save them as such.

    // Replace logic:
    // url("fonts/tabler-icons.woff2?v=2.47.0") -> url("fonts/tabler-icons.woff2")
    // We already simplified download names, so let's simplify CSS references.

    $cleanCss = preg_replace('/url\((["\']?)fonts\/tabler-icons\.(\w+)\?v=[\d\.]+(["\']?)\)/', 'url($1fonts/tabler-icons.$2$3)', $cssContent);

    // Also ensure font-display: swap is present
    if (strpos($cleanCss, 'font-display:swap') === false) {
        $cleanCss = str_replace('@font-face{', '@font-face{font-display:swap;', $cleanCss);
    }

    file_put_contents($cssFile, $cleanCss);
    echo "Updated tabler-icons.min.css paths and font-display.\n";
} else {
    echo "tabler-icons.min.css not found!\n";
}

// 3. WOW.js (User reported 404)
// It seems it's missing locally. Let's download it.
$jsLibsDir = $baseDir.'js/libs/';
if (! is_dir($jsLibsDir)) {
    mkdir($jsLibsDir, 0777, true);
}

$wowUrl = 'https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js';
echo "Downloading WOW.js...\n";
$wowContent = @file_get_contents($wowUrl);
if ($wowContent) {
    file_put_contents($jsLibsDir.'wow.min.js', $wowContent);
    echo "Downloaded wow.min.js\n";
} else {
    echo "Failed to download wow.min.js\n";
}

echo "Done fixing assets.\n";
