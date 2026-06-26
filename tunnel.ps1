$logPath = "c:\xampp\htdocs\udenshop\storage\logs\tunnel.log"
$errPath = "c:\xampp\htdocs\udenshop\storage\logs\tunnel_err.log"
$urlPath = "c:\xampp\htdocs\udenshop\storage\logs\current_url.txt"

# Ensure log directory exists
$logDir = [System.IO.Path]::GetDirectoryName($logPath)
if (-not (Test-Path $logDir)) {
    New-Item -ItemType Directory -Force -Path $logDir
}

# Clear any old files
Clear-Content -Path $logPath -ErrorAction SilentlyContinue
Clear-Content -Path $errPath -ErrorAction SilentlyContinue
Remove-Item -Path $urlPath -ErrorAction SilentlyContinue

while ($true) {
    Write-Output "$(Get-Date): Connecting to Serveo..."
    
    # Start SSH process with separate stdout and stderr redirects
    $process = Start-Process -FilePath "ssh" -ArgumentList "-o StrictHostKeyChecking=no -o ServerAliveInterval=30 -R 80:127.0.0.1:8000 serveo.net" -NoNewWindow -PassThru -RedirectStandardOutput $logPath -RedirectStandardError $errPath
    
    # Monitor the log files to extract the URL
    $foundUrl = $false
    for ($i = 0; $i -lt 15; $i++) {
        Start-Sleep -Seconds 1
        # Check stdout
        if (Test-Path $logPath) {
            $content = Get-Content $logPath -ErrorAction SilentlyContinue
            if ($content) {
                foreach ($line in $content) {
                    if ($line -match "Forwarding HTTP traffic from\s+(https://[a-zA-Z0-9.-]+)") {
                        $url = $matches[1]
                        Write-Output "Found URL in stdout: $url"
                        $url | Out-File -FilePath $urlPath -Force -Encoding utf8
                        $foundUrl = $true
                        break
                    }
                }
            }
        }
        # Check stderr as well (just in case)
        if (-not $foundUrl -and (Test-Path $errPath)) {
            $contentErr = Get-Content $errPath -ErrorAction SilentlyContinue
            if ($contentErr) {
                foreach ($line in $contentErr) {
                    if ($line -match "Forwarding HTTP traffic from\s+(https://[a-zA-Z0-9.-]+)") {
                        $url = $matches[1]
                        Write-Output "Found URL in stderr: $url"
                        $url | Out-File -FilePath $urlPath -Force -Encoding utf8
                        $foundUrl = $true
                        break
                    }
                }
            }
        }
        if ($foundUrl) { break }
    }
    
    # Wait for the process to exit
    $process.WaitForExit()
    
    Write-Output "$(Get-Date): SSH process exited with code $($process.ExitCode). Reconnecting in 5 seconds..."
    Start-Sleep -Seconds 5
}
