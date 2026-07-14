[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
$root = $PSScriptRoot
$outlookDir = Join-Path $root 'outlook'
$htmlPath = Join-Path $outlookDir 'RailTime-E-Mailvorlage.html'
$mazanOutlookDir = Join-Path $root 'mazan\outlook'
$mazanHtmlPath = Join-Path $mazanOutlookDir 'RailTime-Mazan-E-Mailvorlage.html'
$logoPath = Join-Path $root 'assets\logo-mail-dark.png'
$heroPath = Join-Path $root 'assets\hero-railtime.jpg'

if (-not (Test-Path -LiteralPath $htmlPath)) {
    throw 'Bitte zuerst build-formats.py ausführen.'
}

$html = [System.IO.File]::ReadAllText($htmlPath, [System.Text.Encoding]::UTF8)
$mazanHtml = [System.IO.File]::ReadAllText($mazanHtmlPath, [System.Text.Encoding]::UTF8)
function Get-OutlookApplication {
    $lastError = $null
    for ($attempt = 1; $attempt -le 5; $attempt++) {
        try {
            return [Runtime.InteropServices.Marshal]::GetActiveObject('Outlook.Application')
        }
        catch {
            try {
                return New-Object -ComObject Outlook.Application
            }
            catch {
                $lastError = $_
                if ($attempt -lt 5) {
                    Start-Sleep -Milliseconds 1200
                }
            }
        }
    }
    throw $lastError
}

$outlook = Get-OutlookApplication

function Add-InlineAttachment {
    param(
        [Parameter(Mandatory)] $Mail,
        [Parameter(Mandatory)] [string] $Path,
        [Parameter(Mandatory)] [string] $ContentId,
        [Parameter(Mandatory)] [string] $MimeType
    )
    $attachment = $Mail.Attachments.Add($Path)
    $accessor = $attachment.PropertyAccessor
    $accessor.SetProperty('http://schemas.microsoft.com/mapi/proptag/0x3712001F', $ContentId)
    $accessor.SetProperty('http://schemas.microsoft.com/mapi/proptag/0x370E001F', $MimeType)
    $accessor.SetProperty('http://schemas.microsoft.com/mapi/proptag/0x3716001F', 'inline')
    $accessor.SetProperty('http://schemas.microsoft.com/mapi/proptag/0x7FFE000B', $true)
}

function New-RailTimeMail {
    param([Parameter(Mandatory)] [string] $BodyHtml)
    $mail = $outlook.CreateItem(0)
    $mail.Subject = '{{BETREFF}} | RT Rail Time GmbH'
    $mail.HTMLBody = $BodyHtml
    Add-InlineAttachment -Mail $mail -Path $logoPath -ContentId 'railtime-logo' -MimeType 'image/png'
    Add-InlineAttachment -Mail $mail -Path $heroPath -ContentId 'railtime-hero' -MimeType 'image/jpeg'
    return $mail
}

try {
    $formats = @(
        @{ Path = (Join-Path $outlookDir 'RailTime-E-Mailvorlage.oft'); Type = 2; Html = $html },
        @{ Path = (Join-Path $outlookDir 'RailTime-E-Mailvorlage.msg'); Type = 9; Html = $html },
        @{ Path = (Join-Path $mazanOutlookDir 'RailTime-Mazan-E-Mailvorlage.oft'); Type = 2; Html = $mazanHtml },
        @{ Path = (Join-Path $mazanOutlookDir 'RailTime-Mazan-E-Mailvorlage.msg'); Type = 9; Html = $mazanHtml }
    )
    foreach ($format in $formats) {
        $mail = New-RailTimeMail -BodyHtml $format.Html
        try {
            $mail.SaveAs($format.Path, $format.Type)
        }
        finally {
            $mail.Close(1)
            [void][Runtime.InteropServices.Marshal]::FinalReleaseComObject($mail)
        }
    }
}
finally {
    [void][Runtime.InteropServices.Marshal]::FinalReleaseComObject($outlook)
    [GC]::Collect()
    [GC]::WaitForPendingFinalizers()
}

Write-Output 'Outlook-OFT und Unicode-MSG wurden lokal für die allgemeine und die Mazan-Variante erzeugt. Es wurde keine E-Mail versendet.'
