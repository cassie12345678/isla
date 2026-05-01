<?php
declare(strict_types=1);

$recipientEmail = 'casbeumer@gmail.com';
$productOptions = [
    'boeken' => 'Boeken',
    'journals' => 'Journals & stationery',
    'self-care' => 'Self-care',
    'details' => 'Luxe details of wardrobe',
    'selectie' => 'Laat Isla een selectie voorstellen',
];

$values = [
    'naam' => '',
    'email' => '',
    'product' => '',
    'aantal' => '1',
    'verzending' => '',
    'bericht' => '',
];

$errors = [];
$submissionState = null;

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function post_value(string $key): string
{
    $value = $_POST[$key] ?? '';

    return is_string($value) ? trim($value) : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($values) as $field) {
        $values[$field] = post_value($field);
    }

    $honeypot = post_value('company');

    if ($honeypot !== '') {
        $errors['spam'] = 'Ongeldige inzending gedetecteerd.';
    }

    if ($values['naam'] === '') {
        $errors['naam'] = 'Vul je naam in.';
    }

    if ($values['email'] === '' || filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Vul een geldig e-mailadres in.';
    }

    if (!array_key_exists($values['product'], $productOptions)) {
        $errors['product'] = 'Kies wat je wilt aanvragen.';
    }

    if ($values['aantal'] === '' || !ctype_digit($values['aantal']) || (int) $values['aantal'] < 1 || (int) $values['aantal'] > 25) {
        $errors['aantal'] = 'Kies een aantal tussen 1 en 25.';
    }

    if ($values['bericht'] === '') {
        $errors['bericht'] = 'Vertel kort wat je wilt kopen.';
    }

    if ($errors === []) {
        $requestHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $sanitizedHost = preg_replace('/:\d+$/', '', is_string($requestHost) ? $requestHost : 'localhost');
        $sanitizedHost = preg_replace('/[^a-z0-9.-]/i', '', (string) $sanitizedHost);
        $fromHost = 'isla-vayne.test';

        if ($sanitizedHost !== '' && strpos($sanitizedHost, '.') !== false && preg_match('/[a-z]/i', $sanitizedHost) === 1) {
            $fromHost = $sanitizedHost;
        }

        $subject = 'Test aankoop via Isla Vayne';
        $message = implode(PHP_EOL, [
            'Er is een nieuwe test aankoop ingestuurd via de website.',
            '',
            'Naam: ' . $values['naam'],
            'E-mail: ' . $values['email'],
            'Product: ' . $productOptions[$values['product']],
            'Aantal: ' . $values['aantal'],
            'Verzending / voorkeur: ' . ($values['verzending'] !== '' ? $values['verzending'] : 'Niet ingevuld'),
            '',
            'Bericht:',
            $values['bericht'],
            '',
            'Verzonden op: ' . date('d-m-Y H:i:s'),
            'IP-adres: ' . ($_SERVER['REMOTE_ADDR'] ?? 'Onbekend'),
        ]);

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: Isla Vayne website <noreply@' . $fromHost . '>',
            'Reply-To: ' . $values['email'],
            'X-Mailer: PHP/' . PHP_VERSION,
        ];

        $mailSent = mail($recipientEmail, $subject, $message, implode("\r\n", $headers));

        if ($mailSent) {
            $submissionState = 'success';
            $values = [
                'naam' => '',
                'email' => '',
                'product' => '',
                'aantal' => '1',
                'verzending' => '',
                'bericht' => '',
            ];
        } else {
            $submissionState = 'error';
            $errors['mail'] = 'De aanvraag kon niet worden verzonden. Controleer of mail op de server goed staat ingesteld.';
        }
    } else {
        $submissionState = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Aankoop | Isla Vayne</title>
    <meta name="description" content="Doe een test aankoop voor boeken, gifts en luxe details van Isla Vayne. Deze aanvraag wordt per mail doorgestuurd.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Simonetta:wght@400;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script defer src="assets/js/script.js"></script>
</head>
<body>
    <div id="age-modal" class="age-modal" aria-hidden="true">
        <div class="age-modal-content">
            <p class="eyebrow">18+</p>
            <h2>Leeftijdsverificatie</h2>
            <p>Deze website is uitsluitend bedoeld voor volwassenen. Bevestig dat je 18 jaar of ouder bent om verder te gaan.</p>
            <button id="age-confirm-btn" class="btn btn-primary" type="button" onclick="confirmAge()">Ik ben 18+</button>
        </div>
    </div>

    <header class="site-header">
        <div class="container header-bar">
            <a href="index.html" class="logo">Isla Vayne</a>
            <nav class="nav" aria-label="Hoofdnavigatie">
                <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu</button>
                <ul class="nav-menu" id="site-nav">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="over.html">Bio</a></li>
                    <li><a href="diensten.html">Diensten</a></li>
                    <li><a href="wishlist.html">Wishlist</a></li>
                    <li><a href="merchandise.html" class="active">Shop</a></li>
                    <li><a href="galerij.html">Galerij</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </nav>
            <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Schakel naar lichte modus">
                <span>Licht</span>
            </button>
        </div>
    </header>

    <main class="page-shell">
        <section class="page-hero">
            <div class="container hero-grid">
                <div class="hero-copy">
                    <p class="eyebrow">Test aankoop</p>
                    <h1>Wil jij een boek of gift kopen, laat het hier direct en stijlvol achter.</h1>
                    <p>Deze test aankoop is bedoeld om jouw interesse meteen concreet te maken. De aanvraag wordt doorgestuurd naar <strong>casbeumer@gmail.com</strong>, zodat je niet eerst via een losse contactstap hoeft te gaan.</p>
                    <div class="hero-actions">
                        <a href="#purchase-form" class="btn btn-primary">Vul de aankoop in</a>
                        <a href="merchandise.html" class="btn btn-ghost">Terug naar shop</a>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="portrait-card portrait-card-home">
                        <img src="images/isla-gallery-17.jpeg" alt="Portret van Isla Vayne">
                        <div class="hero-portrait-note">
                            <p class="eyebrow">Direct naar mail</p>
                            <p>Voor boeken, gifts en luxe details zonder extra omweg.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section-tight">
            <div class="container contact-layout">
                <div class="contact-panel">
                    <div class="section-card">
                        <p class="eyebrow">Zo werkt het</p>
                        <h2>Helder, volwassen en zonder ruis.</h2>
                        <p>Vul hieronder in wat je wilt kopen, hoeveel je ongeveer zoekt en of je liever een gericht item wilt of juist een selectie in mijn stijl.</p>
                        <div class="contact-steps">
                            <article class="step-card">
                                <h3>Kies gericht</h3>
                                <p>Boeken, journals, self-care of luxe details.</p>
                            </article>
                            <article class="step-card">
                                <h3>Omschrijf het</h3>
                                <p>Geef je voorkeur, hoeveelheid en eventuele verzendwens door.</p>
                            </article>
                            <article class="step-card">
                                <h3>Verstuur direct</h3>
                                <p>De test aankoop gaat meteen naar het e-mailadres van de sitebeheerder.</p>
                            </article>
                        </div>
                        <p class="micro-copy">Dit is een testflow zonder betaalmodule. Het doel is dat jij een echte aankoopintentie netjes kunt insturen via PHP-mail.</p>
                    </div>
                </div>

                <div class="contact-form-card">
                    <form id="purchase-form" class="contact-form" method="post" action="test-aankoop.php">
                        <?php if ($submissionState === 'success'): ?>
                            <div class="form-status form-status-success" role="status" aria-live="polite">
                                Je test aankoop is verstuurd naar casbeumer@gmail.com.
                            </div>
                        <?php elseif (isset($errors['mail'])): ?>
                            <div class="form-status form-status-error" role="alert">
                                <?= escape_html($errors['mail']) ?>
                            </div>
                        <?php elseif ($submissionState === 'error'): ?>
                            <div class="form-status form-status-error" role="alert">
                                Controleer je gegevens even. Ik mis nog een paar velden.
                            </div>
                        <?php endif; ?>

                        <div class="honeypot-field" aria-hidden="true">
                            <label for="company">Bedrijf</label>
                            <input type="text" id="company" name="company" tabindex="-1" autocomplete="off">
                        </div>

                        <label for="naam">Naam</label>
                        <input type="text" id="naam" name="naam" autocomplete="name" required value="<?= escape_html($values['naam']) ?>">
                        <?php if (isset($errors['naam'])): ?>
                            <p class="field-error"><?= escape_html($errors['naam']) ?></p>
                        <?php endif; ?>

                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" autocomplete="email" required value="<?= escape_html($values['email']) ?>">
                        <?php if (isset($errors['email'])): ?>
                            <p class="field-error"><?= escape_html($errors['email']) ?></p>
                        <?php endif; ?>

                        <label for="product">Wat wil je kopen</label>
                        <select id="product" name="product" required>
                            <option value="">Maak een keuze</option>
                            <?php foreach ($productOptions as $key => $label): ?>
                                <option value="<?= escape_html($key) ?>" <?= $values['product'] === $key ? 'selected' : '' ?>>
                                    <?= escape_html($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['product'])): ?>
                            <p class="field-error"><?= escape_html($errors['product']) ?></p>
                        <?php endif; ?>

                        <label for="aantal">Aantal</label>
                        <input type="number" id="aantal" name="aantal" min="1" max="25" inputmode="numeric" required value="<?= escape_html($values['aantal']) ?>">
                        <p class="field-help">Voor testdoeleinden kun je hier aangeven hoeveel stuks of sets je ongeveer zoekt.</p>
                        <?php if (isset($errors['aantal'])): ?>
                            <p class="field-error"><?= escape_html($errors['aantal']) ?></p>
                        <?php endif; ?>

                        <label for="verzending">Verzendwens of voorkeur</label>
                        <input type="text" id="verzending" name="verzending" placeholder="Bijvoorbeeld: NL verzending, luxe editie, discreet verpakt" value="<?= escape_html($values['verzending']) ?>">

                        <label for="bericht">Bericht</label>
                        <textarea id="bericht" name="bericht" rows="7" placeholder="Omschrijf kort wat je wilt kopen of welke sfeer je zoekt." required><?= escape_html($values['bericht']) ?></textarea>
                        <?php if (isset($errors['bericht'])): ?>
                            <p class="field-error"><?= escape_html($errors['bericht']) ?></p>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary">Verstuur test aankoop</button>
                        <p class="micro-copy">Na verzenden gaat deze aanvraag naar <strong>casbeumer@gmail.com</strong> via de mailfunctie van je PHP-server.</p>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <p class="eyebrow">Isla Vayne</p>
                <p class="footer-text">Een luxe, volwassen setting voor bio, diensten, shop, galerij, tribute en contact.</p>
            </div>
            <div class="footer-links">
                <a href="over.html">Bio</a>
                <a href="diensten.html">Diensten</a>
                <a href="merchandise.html">Shop</a>
                <a href="galerij.html">Galerij</a>
                <a href="wishlist.html">Wishlist</a>
                <a href="contact.html">Contact</a>
            </div>
            <div class="footer-note">
                <p>18+ only</p>
                <p>Alle aanvragen uitsluitend voor volwassenen.</p>
            </div>
        </div>
    </footer>
</body>
</html>
