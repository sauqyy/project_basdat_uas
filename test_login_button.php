<?php

echo "=== TESTING LOGIN BUTTON STYLING ===\n\n";

// Test if CSS file exists and contains proper styling
$cssPath = 'public/css/landing-new.css';
if (file_exists($cssPath)) {
    echo "✅ CSS file exists: $cssPath\n";
    
    $cssContent = file_get_contents($cssPath);
    
    // Check for btn-login styling
    if (strpos($cssContent, '.btn-login{') !== false) {
        echo "✅ .btn-login styling found\n";
    } else {
        echo "❌ .btn-login styling not found\n";
    }
    
    // Check for important declarations
    if (strpos($cssContent, 'color:#693158 !important') !== false) {
        echo "✅ Color !important declaration found\n";
    } else {
        echo "❌ Color !important declaration not found\n";
    }
    
    // Check for background !important
    if (strpos($cssContent, 'background:#fff !important') !== false) {
        echo "✅ Background !important declaration found\n";
    } else {
        echo "❌ Background !important declaration not found\n";
    }
    
    // Check for font-weight
    if (strpos($cssContent, 'font-weight:600') !== false) {
        echo "✅ Font-weight declaration found\n";
    } else {
        echo "❌ Font-weight declaration not found\n";
    }
    
    // Check for hover state
    if (strpos($cssContent, '.btn-login:hover{') !== false) {
        echo "✅ Login button hover state found\n";
    } else {
        echo "❌ Login button hover state not found\n";
    }
    
} else {
    echo "❌ CSS file not found: $cssPath\n";
}

echo "\n=== TESTING HTML STRUCTURE ===\n";

// Test index.blade.php
$indexPath = 'resources/views/landing/index.blade.php';
if (file_exists($indexPath)) {
    $indexContent = file_get_contents($indexPath);
    
    // Check for login button structure
    if (strpos($indexContent, 'class="btn btn-outline btn-login"') !== false) {
        echo "✅ Login button classes found in index\n";
    } else {
        echo "❌ Login button classes not found in index\n";
    }
    
    // Check for login text
    if (strpos($indexContent, '>Login</a>') !== false) {
        echo "✅ Login button text found in index\n";
    } else {
        echo "❌ Login button text not found in index\n";
    }
    
} else {
    echo "❌ Index file not found: $indexPath\n";
}

// Test learn-more.blade.php
$learnMorePath = 'resources/views/landing/learn-more.blade.php';
if (file_exists($learnMorePath)) {
    $learnMoreContent = file_get_contents($learnMorePath);
    
    // Check for login button structure
    if (strpos($learnMoreContent, 'class="btn btn-outline btn-login"') !== false) {
        echo "✅ Login button classes found in learn-more\n";
    } else {
        echo "❌ Login button classes not found in learn-more\n";
    }
    
    // Check for login text
    if (strpos($learnMoreContent, '>Login</a>') !== false) {
        echo "✅ Login button text found in learn-more\n";
    } else {
        echo "❌ Login button text not found in learn-more\n";
    }
    
} else {
    echo "❌ Learn-more file not found: $learnMorePath\n";
}

echo "\n=== STYLING SUMMARY ===\n";
echo "🔧 Button Classes: btn btn-outline btn-login\n";
echo "🔧 Text Color: #693158 (dark purple)\n";
echo "🔧 Background: #fff (white)\n";
echo "🔧 Border: 1px solid #693158\n";
echo "🔧 Font Weight: 600 (semi-bold)\n";
echo "🔧 Hover: Background becomes #693158, text becomes white\n";
echo "🔧 Important Declarations: Used to override conflicting styles\n";

echo "\n=== END TEST ===\n";

