$content = Get-Content 'D:\Coding\sistem-genta\resources\views\pengawas\laporan\create.blade.php' -Raw
$start = $content.IndexOf('// Populate hidden inputs on submit')
if ($start -ge 0) {
    $second = $content.IndexOf('// Populate hidden inputs on submit', $start + 1)
    if ($second -ge 0) {
        $end = $content.IndexOf('});', $second)
        if ($end -ge 0) {
            $end += 2
            $content = $content.Remove($second, $end - $second + 1)
            Set-Content 'D:\Coding\sistem-genta\resources\views\pengawas\laporan\create.blade.php' $content
            Write-Host 'Removed duplicate submit handler'
        }
    }
}