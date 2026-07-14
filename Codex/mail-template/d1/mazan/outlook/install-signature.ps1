[CmdletBinding(SupportsShouldProcess)]
param()

$ErrorActionPreference = 'Stop'
$source = $PSScriptRoot
$target = Join-Path $env:APPDATA 'Microsoft\Signatures'
$files = @(
    'RailTime-Signatur-Mazan.htm',
    'RailTime-Signatur-Mazan.rtf',
    'RailTime-Signatur-Mazan.txt'
)

if ($PSCmdlet.ShouldProcess($target, 'RailTime-Signatur für Mazan kopieren')) {
    New-Item -ItemType Directory -Path $target -Force | Out-Null
    foreach ($file in $files) {
        Copy-Item -LiteralPath (Join-Path $source $file) -Destination $target -Force
    }
    Copy-Item -LiteralPath (Join-Path $source 'RailTime-Signatur-Mazan_files') -Destination $target -Recurse -Force
    Write-Output "Mazan-Signatur nach $target kopiert. Die Standardzuordnung bitte anschließend in Outlook auswählen."
}
