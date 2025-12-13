<?php

echo "=== TESTING PREFERENSI HARI FIX ===\n\n";

// Test if SuperAdminController.php exists and contains the fix
$controllerPath = 'app/Http/Controllers/SuperAdminController.php';
if (file_exists($controllerPath)) {
    echo "✅ SuperAdminController.php exists\n";
    
    $controllerContent = file_get_contents($controllerPath);
    
    // Check for the old incorrect variable usage
    if (strpos($controllerContent, '$preferensiHari') !== false) {
        echo "❌ Old incorrect variable \$preferensiHari still found\n";
    } else {
        echo "✅ Old incorrect variable \$preferensiHari removed\n";
    }
    
    // Check for the correct variable usage
    if (strpos($controllerContent, '$hariTersedia') !== false) {
        echo "✅ Correct variable \$hariTersedia found\n";
    } else {
        echo "❌ Correct variable \$hariTersedia not found\n";
    }
    
    // Check for the specific fix in the error message
    if (strpos($controllerContent, 'implode(\', \', $hariTersedia)') !== false) {
        echo "✅ Fixed error message with correct variable found\n";
    } else {
        echo "❌ Fixed error message with correct variable not found\n";
    }
    
    // Check for function signature
    if (strpos($controllerContent, 'function createSingleJadwalWithSpecificDays($mk, $ruangan, $hariTersedia, $jamSesuaiSKS, &$jadwalGenerated, $tipeKelas = \'\')') !== false) {
        echo "✅ Function signature with correct parameter found\n";
    } else {
        echo "❌ Function signature with correct parameter not found\n";
    }
    
} else {
    echo "❌ SuperAdminController.php not found\n";
}

echo "\n=== ERROR ANALYSIS ===\n";
echo "🔧 Problem: Undefined variable \$preferensiHari in line 706\n";
echo "🔧 Root Cause: Variable name mismatch between parameter and usage\n";
echo "🔧 Parameter: \$hariTersedia (correct)\n";
echo "🔧 Usage: \$preferensiHari (incorrect)\n";
echo "🔧 Fix: Changed \$preferensiHari to \$hariTersedia in error message\n";

echo "\n=== FUNCTION CONTEXT ===\n";
echo "🔧 Function: createSingleJadwalWithSpecificDays\n";
echo "🔧 Parameters: \$mk, \$ruangan, \$hariTersedia, \$jamSesuaiSKS, &\$jadwalGenerated, \$tipeKelas\n";
echo "🔧 Issue Location: Line 706-707 in error message construction\n";
echo "🔧 Fix Applied: Use correct parameter name \$hariTersedia instead of undefined \$preferensiHari\n";

echo "\n=== END TEST ===\n";

