<?php

echo "=== TESTING LOGIN BUTTON FIX ===\n\n";

// Test if CSS file exists and contains proper styling
$cssPath = 'public/css/landing-new.css';
if (file_exists($cssPath)) {
    echo "✅ CSS file exists: $cssPath\n";
    
    $cssContent = file_get_contents($cssPath);
    
    // Check for btn-login styling with !important
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
    
    // Check for border !important
    if (strpos($cssContent, 'border:1px solid #693158 !important') !== false) {
        echo "✅ Border !important declaration found\n";
    } else {
        echo "❌ Border !important declaration not found\n";
    }
    
    // Check for font-weight
    if (strpos($cssContent, 'font-weight:600') !== false) {
        echo "✅ Font-weight declaration found\n";
    } else {
        echo "❌ Font-weight declaration not found\n";
    }
    
    // Check for text-decoration none
    if (strpos($cssContent, 'text-decoration:none !important') !== false) {
        echo "✅ Text-decoration none !important found\n";
    } else {
        echo "❌ Text-decoration none !important not found\n";
    }
    
    // Check for hover state with !important
    if (strpos($cssContent, '.btn-login:hover{background:#693158 !important;color:#fff !important') !== false) {
        echo "✅ Login button hover state with !important found\n";
    } else {
        echo "❌ Login button hover state with !important not found\n";
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

echo "\n=== STYLING PRIORITY SUMMARY ===\n";
echo "🔧 Button Classes: btn btn-outline btn-login\n";
echo "🔧 Text Color: #693158 !important (dark purple with priority)\n";
echo "🔧 Background: #fff !important (white with priority)\n";
echo "🔧 Border: 1px solid #693158 !important (with priority)\n";
echo "🔧 Font Weight: 600 (semi-bold)\n";
echo "🔧 Text Decoration: none !important (no underline)\n";
echo "🔧 Hover: Background becomes #693158 !important, text becomes white !important\n";
echo "🔧 Priority: All important declarations override conflicting styles\n";

echo "\n=== END TEST ===\n";

