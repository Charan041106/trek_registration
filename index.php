<?php
session_start();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$mountains = [
    'Skandagiri' => [
        'location' => 'Chikkaballapur, Karnataka',
        'difficulty' => 'Moderate',
        'image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=85'
    ],
    'Nandi Hills' => [
        'location' => 'Chikkaballapur, Karnataka',
        'difficulty' => 'Easy',
        'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=85'
    ],
    'Mullayanagiri' => [
        'location' => 'Chikkamagaluru, Karnataka',
        'difficulty' => 'Moderate',
        'image' => 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=1200&q=85'
    ],
    'Kudremukh' => [
        'location' => 'Chikkamagaluru, Karnataka',
        'difficulty' => 'Challenging',
        'image' => 'https://images.unsplash.com/photo-1464278533981-50106e6176b1?auto=format&fit=crop&w=1200&q=85'
    ]
];

$data = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'usn' => '',
    'department' => '',
    'mountain' => '',
    'date' => '',
    'type' => 'Individual',
    'group_name' => '',
    'group_size' => '',
    'experience' => '',
    'emergency_name' => '',
    'emergency_phone' => '',
    'notes' => '',
    'declaration' => ''
];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($data as $key => $value) {
        if ($key === 'declaration') {
            $data[$key] = $_POST[$key] ?? '';
        } else {
            $data[$key] = trim($_POST[$key] ?? '');
        }
    }

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $errors[] = 'Session expired. Please refresh and try again.';
    }

    if (mb_strlen($data['name']) < 2) {
        $errors[] = 'Enter your full name.';
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if (!preg_match('/^[0-9+\-\s()]{8,16}$/', $data['phone'])) {
        $errors[] = 'Enter a valid phone number.';
    }

    if ($data['usn'] === '') {
        $errors[] = 'Enter your USN or student ID.';
    }

    if ($data['department'] === '') {
        $errors[] = 'Enter your department.';
    }

    if (!array_key_exists($data['mountain'], $mountains)) {
        $errors[] = 'Please select a trekking destination.';
    }

    if ($data['date'] === '' || $data['date'] < date('Y-m-d')) {
        $errors[] = 'Select a valid upcoming trekking date.';
    }

    if (!in_array($data['type'], ['Individual', 'Group'], true)) {
        $errors[] = 'Select a participation type.';
    }

    if ($data['type'] === 'Group') {
        if ($data['group_name'] === '') {
            $errors[] = 'Enter your group name.';
        }

        if (
            !ctype_digit($data['group_size']) ||
            (int)$data['group_size'] < 2 ||
            (int)$data['group_size'] > 30
        ) {
            $errors[] = 'Group size must be between 2 and 30.';
        }
    }

    if (!in_array($data['experience'], ['Beginner', 'Intermediate', 'Experienced'], true)) {
        $errors[] = 'Select your trekking experience.';
    }

    if ($data['emergency_name'] === '') {
        $errors[] = 'Enter an emergency contact name.';
    }

    if (!preg_match('/^[0-9+\-\s()]{8,16}$/', $data['emergency_phone'])) {
        $errors[] = 'Enter a valid emergency contact number.';
    }

    if ($data['declaration'] !== 'yes') {
        $errors[] = 'Please accept the trekking declaration.';
    }

    if (!$errors) {
        $success = true;
        $reference = 'GT26-' . strtoupper(bin2hex(random_bytes(3)));
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Green Trails | Trekking Registration</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header class="navbar">
    <a href="index.php" class="brand">
        <span class="brand-icon">⌁</span>
        <span>GREEN<span class="brand-light">TRAILS</span></span>
    </a>

    <a href="#register" class="nav-button">Join the adventure</a>
</header>

<?php if ($success): ?>

<section class="success-page">
    <div class="success-card">

        <div class="success-icon">✓</div>

        <p class="eyebrow">REGISTRATION COMPLETED</p>

        <h1>Adventure awaits,<br><em><?= e($data['name']) ?>!</em></h1>

        <p class="success-text">
            Your trekking registration has been received successfully.
            Get ready to explore the beautiful mountains of Karnataka!
        </p>

        <div class="reference">
            <small>YOUR REGISTRATION ID</small>
            <strong><?= e($reference) ?></strong>
        </div>

        <div class="success-details">
            <div>
                <small>DESTINATION</small>
                <strong><?= e($data['mountain']) ?></strong>
            </div>

            <div>
                <small>TREKKING DATE</small>
                <strong><?= e($data['date']) ?></strong>
            </div>

            <div>
                <small>PARTICIPATION</small>
                <strong><?= e($data['type']) ?></strong>
            </div>

            <?php if ($data['type'] === 'Group'): ?>
            <div>
                <small>GROUP</small>
                <strong><?= e($data['group_name']) ?> · <?= e($data['group_size']) ?> members</strong>
            </div>
            <?php endif; ?>

            <div>
                <small>EXPERIENCE</small>
                <strong><?= e($data['experience']) ?></strong>
            </div>
        </div>

        <button class="primary-button" onclick="window.print()">
            Print registration
        </button>

        <a href="index.php" class="back-link">Back to Green Trails</a>

    </div>
</section>

<?php else: ?>

<section class="hero">

    <div class="hero-content">

        <p class="eyebrow">DISCOVER · EXPLORE · EXPERIENCE</p>

        <h1>Find your path.<br>
            <em>Feel the wild.</em>
        </h1>

        <p>
            Leave the ordinary behind. Discover the breathtaking
            mountains of Karnataka and create memories beyond the horizon.
        </p>

        <a href="#destinations" class="primary-button">
            Explore destinations
        </a>

        <div class="hero-info">
            <span>⛰ Karnataka Trails</span>
            <span>🌿 Nature & Adventure</span>
            <span>✦ Open to Everyone</span>
        </div>

    </div>

    <div class="hero-image">
        <div class="image-caption">
            <span>YOUR NEXT ADVENTURE</span>
            <strong>Starts here.</strong>
        </div>
    </div>

</section>

<section class="destinations" id="destinations">

    <div class="section-heading">
        <div>
            <p class="eyebrow">CHOOSE YOUR DESTINATION</p>
            <h2>Mountains are calling.</h2>
        </div>

        <p>Select your favourite destination before proceeding
           to the registration form.</p>
    </div>

    <div class="mountain-grid">

        <?php foreach ($mountains as $name => $mountain): ?>

        <label class="mountain-card">

            <input
                type="radio"
                name="mountain"
                value="<?= e($name) ?>"
                form="eventForm"
                <?= $data['mountain'] === $name ? 'checked' : '' ?>
            >

            <div class="mountain-image"
                 style="background-image:url('<?= e($mountain['image']) ?>')">

                <span class="difficulty">
                    <?= e($mountain['difficulty']) ?>
                </span>

                <span class="selected-mark">✓</span>

            </div>

            <div class="mountain-info">
                <h3><?= e($name) ?></h3>
                <p>⌖ <?= e($mountain['location']) ?></p>
                <span class="choose-text">SELECT DESTINATION</span>
            </div>

        </label>

        <?php endforeach; ?>

    </div>

</section>

<section class="registration-section" id="register">

    <div class="registration-intro">
        <p class="eyebrow">YOUR JOURNEY STARTS HERE</p>
        <h2>Ready to take<br><em>the next step?</em></h2>

        <p>
            Complete your details, choose how you want to participate
            and get ready for an unforgettable trekking experience.
        </p>

        <div class="intro-note">
            <span>🌱</span>
            <p>Respect nature. Leave no trace. Take only memories.</p>
        </div>
    </div>

    <div class="form-card">

        <div class="form-heading">
            <div>
                <p class="eyebrow">GREEN TRAILS / REGISTRATION</p>
                <h3>Trekking registration</h3>
                <p>Fields marked * are required.</p>
            </div>

            <span class="form-number">01 / 01</span>
        </div>

        <?php if ($errors): ?>
            <div class="error-box">
                <strong>Please correct the following:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="eventForm" method="POST" action="index.php">

            <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

            <div class="form-section-title">
                <span>01</span>
                <h4>Personal information</h4>
            </div>

            <div class="form-grid">

                <div class="field full">
                    <label>Full name *</label>
                    <input type="text" name="name"
                           value="<?= e($data['name']) ?>"
                           placeholder="Enter your full name" required>
                </div>

                <div class="field">
                    <label>Email address *</label>
                    <input type="email" name="email"
                           value="<?= e($data['email']) ?>"
                           placeholder="you@example.com" required>
                </div>

                <div class="field">
                    <label>Phone number *</label>
                    <input type="tel" name="phone"
                           value="<?= e($data['phone']) ?>"
                           placeholder="+91 98765 43210" required>
                </div>

                <div class="field">
                    <label>USN / Student ID *</label>
                    <input type="text" name="usn"
                           value="<?= e($data['usn']) ?>"
                           placeholder="Enter your USN" required>
                </div>

                <div class="field">
                    <label>Department *</label>
                    <input type="text" name="department"
                           value="<?= e($data['department']) ?>"
                           placeholder="e.g. Computer Science" required>
                </div>

            </div>

            <div class="form-section-title">
                <span>02</span>
                <h4>Choose your trekking destination</h4>
            </div>

            <p class="form-hint">
                Select a mountain from the destination cards above.
            </p>

            <div class="selected-destination" id="selectedDestination">
                <span>⛰</span>
                <strong id="selectedMountainText">
                    <?= $data['mountain'] ? e($data['mountain']) : 'No destination selected yet' ?>
                </strong>
            </div>

            <div class="field date-field">
                <label>Preferred trekking date *</label>
                <input type="date" name="date" id="trekDate"
                       value="<?= e($data['date']) ?>"
                       min="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-section-title">
                <span>03</span>
                <h4>Participation details</h4>
            </div>

            <div class="participation-grid">

                <label class="participation-card">
                    <input type="radio" name="type" value="Individual"
                        <?= $data['type'] === 'Individual' ? 'checked' : '' ?>>
                    <span class="participation-icon">🚶</span>
                    <strong>Individual</strong>
                    <small>I'll join on my own</small>
                </label>

                <label class="participation-card">
                    <input type="radio" name="type" value="Group"
                        <?= $data['type'] === 'Group' ? 'checked' : '' ?>>
                    <span class="participation-icon">👥</span>
                    <strong>Group</strong>
                    <small>We'll trek together</small>
                </label>

            </div>

            <div class="form-grid group-fields" id="groupFields"
                 style="display:none">

                <div class="field">
                    <label>Group name *</label>
                    <input type="text" name="group_name"
                           value="<?= e($data['group_name']) ?>"
                           placeholder="Enter group name">
                </div>

                <div class="field">
                    <label>Number of participants *</label>
                    <input type="number" name="group_size"
                           min="2" max="30"
                           value="<?= e($data['group_size']) ?>"
                           placeholder="2 to 30">
                </div>

            </div>

            <div class="field">
                <label>Trekking experience *</label>
                <select name="experience" required>
                    <option value="">Select your experience</option>
                    <?php foreach (['Beginner','Intermediate','Experienced'] as $level): ?>
                        <option value="<?= e($level) ?>"
                            <?= $data['experience'] === $level ? 'selected' : '' ?>>
                            <?= e($level) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-section-title">
                <span>04</span>
                <h4>Emergency contact</h4>
            </div>

            <div class="form-grid">

                <div class="field">
                    <label>Emergency contact name *</label>
                    <input type="text" name="emergency_name"
                           value="<?= e($data['emergency_name']) ?>"
                           placeholder="Contact person's name" required>
                </div>

                <div class="field">
                    <label>Emergency contact number *</label>
                    <input type="tel" name="emergency_phone"
                           value="<?= e($data['emergency_phone']) ?>"
                           placeholder="Emergency phone number" required>
                </div>

            </div>

            <div class="field">
                <label>Additional information <span>OPTIONAL</span></label>
                <textarea name="notes" rows="3"
                          placeholder="Any special requirements or information?"><?= e($data['notes']) ?></textarea>
            </div>

            <div class="declaration">
                <label>
                    <input type="checkbox" name="declaration" value="yes"
                        <?= $data['declaration'] === 'yes' ? 'checked' : '' ?>
                        required>
                    <span>
                        I confirm that the information provided is correct.
                        I agree to follow the trek leader's safety instructions
                        and respect the natural environment. *
                    </span>
                </label>
            </div>

            <button type="submit" class="primary-button submit-button">
                Complete registration <span></span>
            </button>

            <p class="form-bottom-note">
                🌿 Adventure responsibly. Keep the mountains clean.
            </p>

        </form>

    </div>

</section>

<?php endif; ?>

<footer class="footer">

    <a href="index.php" class="brand footer-brand">
        <span class="brand-icon">⌁</span>
        GREEN<span class="brand-light">TRAILS</span>
    </a>

    <p>Find your path. Feel the wild.</p>

    <div class="footer-line"></div>

    <span class="watermark">Charan K | 1RF25CS405</span>

    <small>© 2026 GREEN TRAILS · EXPLORE RESPONSIBLY</small>

</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="assets/js/script.js"></script>

</body>
</html>