[CmdletBinding(SupportsShouldProcess)]
param()

$ErrorActionPreference = 'Stop'
$source = $PSScriptRoot
$target = Join-Path $env:APPDATA 'Microsoft\Signatures'
$files = @(
    'RailTime-Signatur.htm',
    'RailTime-Signatur.rtf',
    'RailTime-Signatur.txt'
)

if ($PSCmdlet.ShouldProcess($target, 'RailTime-Signaturdateien kopieren')) {
    New-Item -ItemType Directory -Path $target -Force | Out-Null
    foreach ($file in $files) {
        Copy-Item -LiteralPath (Join-Path $source $file) -Destination $target -Force
    }
    Copy-Item -LiteralPath (Join-Path $source 'RailTime-Signatur_files') -Destination $target -Recurse -Force
    Write-Output "Signatur nach $target kopiert. Die Standardzuordnung bitte anschließend in Outlook auswählen."
}
