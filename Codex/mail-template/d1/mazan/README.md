# RailTime Mail-Variante Mazan

Eigenständige Mazan-Version des D1-Mailpakets. Übernommen sind die allgemeinen Firmendaten der RT Rail Time GmbH:

- Borsteler Weg 29–31, 21423 Winsen (Luhe)
- 0160 1881848
- kontakt@rail-time.de
- Notfalldienst 24/7

Da keine separate Funktion, Durchwahl oder persönliche E-Mail-Adresse für Mazan hinterlegt ist, verwendet diese Variante bewusst die zentralen Kontaktdaten. Geschäftsführung, Registergericht, HRB und USt-ID bleiben als Platzhalter erhalten und müssen vor produktiver Nutzung ergänzt werden.

## Dateien

- `email-body.html`, `email-body.txt`, `RailTime-Mazan-E-Mailvorlage.eml`
- `signature-dark.html`, `signature-light.html` und die selbstständigen Inline-Versionen
- `outlook/RailTime-Mazan-E-Mailvorlage.oft/.msg`
- `outlook/RailTime-Signatur-Mazan.htm/.rtf/.txt`
- `outlook/install-signature.ps1`

Die Outlook-Signatur kann mit folgendem Befehl in den klassischen Signaturordner kopiert werden:

```powershell
powershell -ExecutionPolicy Bypass -File outlook/install-signature.ps1
```

Die Mazan-Vorlage und die HTML-Signatur verwenden `100%` der verfügbaren Nachrichtenbreite. Nach Änderungen das Installationsskript erneut ausführen und die OFT-Datei neu öffnen.
