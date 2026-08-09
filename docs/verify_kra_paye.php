<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Payroll\PayrollConfiguration;

$taxable = 95000;
$tax = 0;
$rem = $taxable;
foreach (PayrollConfiguration::getPayeSlices() as $s) {
    $w = $s['width'];
    $portion = $w === null ? $rem : min($rem, $w);
    $tax += $portion * $s['rate'];
    $rem -= $portion;
}
$paye = max(0, $tax - 2400);
echo 'Gross tax on 95000: '.round($tax, 2).PHP_EOL;
echo 'PAYE after personal relief: '.round($paye, 2).PHP_EOL;
echo 'KRA example expects ~20883.25'.PHP_EOL;

$lim = PayrollConfiguration::getNssfLimitsForDate('2026-08-01');
echo 'NSSF Aug2026 LEL='.$lim['lel'].' UEL='.$lim['uel'].PHP_EOL;
$lim3 = PayrollConfiguration::getNssfLimitsForDate('2025-06-01');
echo 'NSSF Jun2025 LEL='.$lim3['lel'].' UEL='.$lim3['uel'].PHP_EOL;

$t1 = 0.06 * $lim['lel'];
$t2 = 0.06 * ($lim['uel'] - $lim['lel']);
echo 'Max employee NSSF Y4: '.($t1 + $t2).' (expect 6480)'.PHP_EOL;

// First-band width check: 24000 @10% = 2400
$tax24 = 0;
$rem = 24000;
foreach (PayrollConfiguration::getPayeSlices() as $s) {
    $w = $s['width'];
    $portion = $w === null ? $rem : min($rem, $w);
    $tax24 += $portion * $s['rate'];
    $rem -= $portion;
}
echo 'Tax on exactly 24000: '.round($tax24, 2).' (expect 2400.00)'.PHP_EOL;
