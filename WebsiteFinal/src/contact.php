<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function wf_contact_redirect(array $state): never
{
    wf_start_session();
    $_SESSION['contact_form'] = $state;
    header('Location: ' . wf_route_url('contact') . '#anfrage', true, 303);
    exit;
}

function wf_handle_contact_submission(): never
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        header('Allow: POST');
        header('Content-Type: text/plain; charset=UTF-8');
        http_response_code(405);
        exit('Diese Route akzeptiert ausschließlich Formularanfragen per POST.');
    }

    wf_start_session();
    $config = wf_config();
    $input = [
        'name' => trim((string)($_POST['name'] ?? '')),
        'email' => trim((string)($_POST['email'] ?? '')),
        'phone' => trim((string)($_POST['phone'] ?? '')),
        'location' => trim((string)($_POST['location'] ?? '')),
        'service' => trim((string)($_POST['service'] ?? '')),
        'message' => trim((string)($_POST['message'] ?? '')),
    ];

    if (trim((string)($_POST['company_website'] ?? '')) !== '') {
        wf_contact_redirect(['errors' => [], 'old' => [], 'status' => 'success']);
    }

    $errors = [];
    $token = (string)($_POST['csrf_token'] ?? '');
    if ($token === '' || empty($_SESSION['csrf_token']) || !hash_equals((string)$_SESSION['csrf_token'], $token)) {
        $errors[] = 'Die Sitzung ist abgelaufen. Bitte senden Sie das Formular erneut.';
    }
    if (mb_strlen($input['name']) < 2 || mb_strlen($input['name']) > 120) {
        $errors[] = 'Bitte geben Sie einen gültigen Namen ein.';
    }
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($input['email']) > 190) {
        $errors[] = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    }
    if ($input['phone'] !== '' && (mb_strlen($input['phone']) > 60 || !preg_match('/^[0-9+()\/ .-]+$/u', $input['phone']))) {
        $errors[] = 'Bitte prüfen Sie die angegebene Telefonnummer.';
    }
    if (mb_strlen($input['location']) > 160) {
        $errors[] = 'Der Einsatzort ist zu lang.';
    }
    $serviceNames = array_column($config['services'], 'title');
    if (!in_array($input['service'], $serviceNames, true)) {
        $errors[] = 'Bitte wählen Sie eine gültige Leistung aus.';
    }
    if (mb_strlen($input['message']) < 10 || mb_strlen($input['message']) > 5000) {
        $errors[] = 'Die Nachricht muss zwischen 10 und 5.000 Zeichen lang sein.';
    }
    if (empty($_POST['privacy'])) {
        $errors[] = 'Bitte bestätigen Sie die Datenschutzhinweise.';
    }
    $lastSubmission = (int)($_SESSION['last_contact_submission'] ?? 0);
    if ($lastSubmission > 0 && time() - $lastSubmission < 20) {
        $errors[] = 'Bitte warten Sie einen Moment, bevor Sie eine weitere Anfrage senden.';
    }

    if ($errors) {
        wf_contact_redirect(['errors' => $errors, 'old' => $input, 'status' => 'error']);
    }

    $mail = $config['mail'];
    if (!$mail['enabled']) {
        wf_contact_redirect(['errors' => ['Der Formularversand ist auf diesem Server noch nicht aktiviert. Bitte nutzen Sie E-Mail oder Telefon.'], 'old' => $input, 'status' => 'error']);
    }

    $requestId = strtoupper(bin2hex(random_bytes(4)));
    $subject = $mail['subject_prefix'] . ' ' . $input['service'] . ' · ' . $requestId;
    $body = implode("\r\n", [
        'Neue Anfrage über rail-time.de',
        'Vorgangsnummer: ' . $requestId,
        '',
        'Name: ' . $input['name'],
        'E-Mail: ' . $input['email'],
        'Telefon: ' . ($input['phone'] ?: 'nicht angegeben'),
        'Einsatzort: ' . ($input['location'] ?: 'nicht angegeben'),
        'Leistung: ' . $input['service'],
        '',
        'Nachricht:',
        $input['message'],
        '',
        'Datenschutzhinweis wurde bestätigt.',
    ]);
    $headers = [
        'From: RT Rail Time Website <' . $mail['from'] . '>',
        'Reply-To: ' . $input['email'],
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'X-Mailer: RT-RailTime-Website',
    ];

    $sent = @mail(
        (string)$mail['recipient'],
        mb_encode_mimeheader($subject, 'UTF-8'),
        $body,
        implode("\r\n", $headers)
    );

    if (!$sent) {
        wf_contact_redirect(['errors' => ['Die Anfrage konnte technisch nicht versendet werden. Bitte nutzen Sie E-Mail oder Telefon.'], 'old' => $input, 'status' => 'error']);
    }

    $_SESSION['last_contact_submission'] = time();
    unset($_SESSION['csrf_token']);
    wf_contact_redirect(['errors' => [], 'old' => [], 'status' => 'success']);
}

function wf_render_contact_form(array $rt): void
{
    $state = wf_form_state();
    $old = is_array($state['old'] ?? null) ? $state['old'] : [];
    $errors = is_array($state['errors'] ?? null) ? $state['errors'] : [];
    $status = (string)($state['status'] ?? '');
    $value = static fn(string $key): string => wf_escape((string)($old[$key] ?? ''));
    ?>
<form action="<?= wf_escape(wf_route_url('contact_submit')) ?>" method="post" id="anfrage" novalidate>
    <header><b>RT / EINSATZANFRAGE</b><span>Pflichtfelder *</span></header>
    <div class="rt-form-status full" role="status" aria-live="polite">
        <?php if ($status === 'success'): ?><p class="is-success">Vielen Dank. Ihre Anfrage wurde erfolgreich übermittelt.</p><?php endif ?>
        <?php if ($errors): ?><div class="is-error"><strong>Bitte prüfen Sie Ihre Angaben:</strong><ul><?php foreach ($errors as $error): ?><li><?= wf_escape((string)$error) ?></li><?php endforeach ?></ul></div><?php endif ?>
    </div>
    <input type="hidden" name="csrf_token" value="<?= wf_escape(wf_csrf_token()) ?>">
    <label class="rt-honeypot" aria-hidden="true">Website<input name="company_website" tabindex="-1" autocomplete="off"></label>
    <label for="contact-name">Name *<input id="contact-name" name="name" value="<?= $value('name') ?>" autocomplete="name" maxlength="120" required></label>
    <label for="contact-email">E-Mail *<input id="contact-email" name="email" type="email" value="<?= $value('email') ?>" autocomplete="email" maxlength="190" required></label>
    <label for="contact-phone">Telefon<input id="contact-phone" name="phone" type="tel" value="<?= $value('phone') ?>" autocomplete="tel" maxlength="60"></label>
    <label for="contact-location">Einsatzort<input id="contact-location" name="location" value="<?= $value('location') ?>" autocomplete="address-level2" maxlength="160"></label>
    <label class="full" for="contact-service">Gewünschte Leistung *<select id="contact-service" name="service" required><?php foreach ($rt['services'] as $service): ?><option<?= ($old['service'] ?? '') === $service['title'] ? ' selected' : '' ?>><?= wf_escape($service['title']) ?></option><?php endforeach ?></select></label>
    <label class="full" for="contact-message">Nachricht *<textarea id="contact-message" name="message" rows="7" minlength="10" maxlength="5000" required><?= $value('message') ?></textarea></label>
    <label class="rt-consent full" for="contact-privacy"><input id="contact-privacy" name="privacy" type="checkbox" value="1" required> <span>Ich habe die <a href="<?= wf_escape(wf_route_url('privacy')) ?>">Datenschutzerklärung</a> gelesen und stimme der Verarbeitung meiner Angaben zur Bearbeitung der Anfrage zu. *</span></label>
    <button class="ig-button full" type="submit">Anfrage sicher senden <b class="rt-action-arrow" aria-hidden="true"></b></button>
    <p class="rt-form-fallback full">Alternativ erreichen Sie uns unter <a href="mailto:<?= wf_escape($rt['email']) ?>"><?= wf_escape($rt['email']) ?></a> oder <a href="tel:<?= wf_escape($rt['phone_href']) ?>"><?= wf_escape($rt['phone']) ?></a>.</p>
</form>
<?php
}
