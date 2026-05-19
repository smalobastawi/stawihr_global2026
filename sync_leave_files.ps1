# PowerShell script to sync leave-related files from source to destination
# Copies files if they are newer in source or don't exist in destination

$sourceBase = "D:\Development\shofcodev - Copy"
$destBase = "d:\Development\dakawou_hr"

# Define leave-related file patterns to match
$patterns = @(
    '*leave*',
    '*Leave*',
    '*holiday*',
    '*Holiday*',
    '*rollover*',
    '*Rollover*',
    '*earnLeave*',
    '*EarnLeave*',
    '*Leavers*',
    '*leavers*'
)

# Additional specific files that contain leave functionality but don't match patterns above
$additionalFiles = @(
    "app\helper.php",
    "app\Helpers\LeaveCalculator.php",
    "app\Models\Employee.php",
    "app\Repositories\LeaveRepository.php",
    "app\Repositories\AttendanceRepository.php",
    "app\Repositories\PayrollRepository.php",
    "app\Repositories\CommonRepository.php",
    "app\Console\Commands\GroupedRoutePermissions.php",
    "app\Console\Commands\AdjustStateMourningLeave.php",
    "app\Console\Commands\ViewStateMourningAdjustments.php",
    "app\Providers\RouteServiceProvider.php",
    "app\Http\Controllers\AccountsController.php",
    "app\Http\Controllers\Api\ApprovalsController.php",
    "app\Http\Controllers\Annalytics\AnnalyticsController.php",
    "app\Http\Controllers\Employee\EmployeeEducationQualificationController.php",
    "routes\leave.php"
)

$copyLog = @()
$newFiles = @()
$updatedFiles = @()
$skippedFiles = @()

Write-Host "Scanning source project for leave-related files..." -ForegroundColor Cyan

# Get all matching files from source
$sourceFiles = Get-ChildItem -Path $sourceBase -Recurse -File | Where-Object {
    $fileName = $_.Name
    $matched = $false
    foreach ($pattern in $patterns) {
        if ($fileName -like $pattern) {
            $matched = $true
            break
        }
    }
    # Exclude vendor and node_modules
    if ($matched -and ($_.FullName -like "*\vendor\*" -or $_.FullName -like "*\node_modules\*")) {
        $matched = $false
    }
    return $matched
}

# Also add additional specific files if they exist in source
foreach ($relPath in $additionalFiles) {
    $fullPath = Join-Path $sourceBase $relPath
    if (Test-Path $fullPath) {
        $file = Get-Item $fullPath
        if ($sourceFiles.FullName -notcontains $file.FullName) {
            $sourceFiles += $file
        }
    }
}

Write-Host "Found $($sourceFiles.Count) leave-related files in source." -ForegroundColor Green
Write-Host "Comparing and copying to destination..." -ForegroundColor Cyan
Write-Host ""

foreach ($sourceFile in $sourceFiles) {
    # Compute relative path
    $relativePath = $sourceFile.FullName.Substring($sourceBase.Length).TrimStart('\', '/')
    $destPath = Join-Path $destBase $relativePath
    
    $destExists = Test-Path $destPath
    
    if (-not $destExists) {
        # File doesn't exist in destination - copy it
        $destDir = Split-Path $destPath -Parent
        if (-not (Test-Path $destDir)) {
            New-Item -ItemType Directory -Path $destDir -Force | Out-Null
        }
        Copy-Item -Path $sourceFile.FullName -Destination $destPath -Force
        $newFiles += $relativePath
        Write-Host "[NEW]      $relativePath" -ForegroundColor Green
    } else {
        $destFile = Get-Item $destPath
        # Compare by LastWriteTime
        if ($sourceFile.LastWriteTime -gt $destFile.LastWriteTime) {
            Copy-Item -Path $sourceFile.FullName -Destination $destPath -Force
            $updatedFiles += $relativePath
            Write-Host "[UPDATED]  $relativePath" -ForegroundColor Yellow
        } else {
            $skippedFiles += $relativePath
            Write-Host "[SKIPPED]  $relativePath (destination is same or newer)" -ForegroundColor DarkGray
        }
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Sync Complete!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "New files copied:     $($newFiles.Count)" -ForegroundColor Green
Write-Host "Updated files copied: $($updatedFiles.Count)" -ForegroundColor Yellow
Write-Host "Files skipped:        $($skippedFiles.Count)" -ForegroundColor DarkGray
Write-Host ""

if ($newFiles.Count -gt 0) {
    Write-Host "New files:" -ForegroundColor Green
    $newFiles | ForEach-Object { Write-Host "  $_" -ForegroundColor Green }
    Write-Host ""
}

if ($updatedFiles.Count -gt 0) {
    Write-Host "Updated files:" -ForegroundColor Yellow
    $updatedFiles | ForEach-Object { Write-Host "  $_" -ForegroundColor Yellow }
    Write-Host ""
}

# Also check for view directories that might have been missed
$sourceViewDirs = Get-ChildItem -Path "$sourceBase\resources\views" -Recurse -Directory | Where-Object { $_.Name -match 'leave|Leave|holiday|Holiday' }
foreach ($srcDir in $sourceViewDirs) {
    $relDir = $srcDir.FullName.Substring($sourceBase.Length).TrimStart('\', '/')
    $destDirPath = Join-Path $destBase $relDir
    if (-not (Test-Path $destDirPath)) {
        New-Item -ItemType Directory -Path $destDirPath -Force | Out-Null
    }
    # Copy any files in these directories that weren't already caught
    $dirFiles = Get-ChildItem -Path $srcDir.FullName -File -Recurse
    foreach ($df in $dirFiles) {
        $relFile = $df.FullName.Substring($sourceBase.Length).TrimStart('\', '/')
        $destFilePath = Join-Path $destBase $relFile
        if (-not (Test-Path $destFilePath)) {
            $destFileDir = Split-Path $destFilePath -Parent
            if (-not (Test-Path $destFileDir)) {
                New-Item -ItemType Directory -Path $destFileDir -Force | Out-Null
            }
            Copy-Item -Path $df.FullName -Destination $destFilePath -Force
            if ($newFiles -notcontains $relFile) {
                $newFiles += $relFile
                Write-Host "[NEW-DIR]  $relFile" -ForegroundColor Green
            }
        } else {
            $destExisting = Get-Item $destFilePath
            if ($df.LastWriteTime -gt $destExisting.LastWriteTime) {
                Copy-Item -Path $df.FullName -Destination $destFilePath -Force
                if ($updatedFiles -notcontains $relFile) {
                    $updatedFiles += $relFile
                    Write-Host "[UPD-DIR]  $relFile" -ForegroundColor Yellow
                }
            }
        }
    }
}

Write-Host "Done." -ForegroundColor Cyan
