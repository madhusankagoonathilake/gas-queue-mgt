<?php

session_start();

include_once '../common/security.php';

if (!isPostRequest()) {
    echo "Invalid access";
    exit(1);
}

$csrfToken = filter_input(INPUT_POST, 'csrfToken', FILTER_SANITIZE_STRING);
if (!isCsrfTokenValid($csrfToken)) {
    echo "CSRF attack";
    exit(1);
}

$agency = filter_input(INPUT_POST, 'agency', FILTER_SANITIZE_STRING);
$telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);

// Check if already in a queue
require_once '../common/dbh.php';
$stmt = $dbh->prepare("SELECT IF(COUNT(*) > 0, 'Yes', 'No') FROM customers WHERE telephone = ?;");
$stmt->execute([$telephone]);
$isAlreadyInAQueue = ($stmt->fetch(\PDO::FETCH_COLUMN) === 'Yes');

if (!$isAlreadyInAQueue) {
    $buyerActivationOTP = str_shuffle(rand(100000, 999999));

    $_SESSION['agency'] = $agency;
    $_SESSION['telephone'] = $telephone;
    $_SESSION['buyerActivationOTP'] = $buyerActivationOTP;
    $_SESSION['buyerActivationAttempts'] = 0;

        include_once '../common/sms.php';

    $replacements = ['${buyerActivationOTP}' => $buyerActivationOTP];
    $messageTemplate = file_get_contents('../templates/buyer-activation.txt');
    $buyerActivationMessage = str_replace(array_keys($replacements), array_values($replacements), $messageTemplate);

    send_sms($telephone, $buyerActivationMessage);
}

include_once '../templates/header.php';
?>
<main class="px-3 py-3 mt-5 ">
    <h1>ගැණුම්කරු</h1>
    <p class="lead">&nbsp;</p>
    <p class="lead">
        <strong>සක්‍රීය කිරීම</strong>
    </p>
    <form class="lead" method="post" action="buyer-activation-otp-verification.php">
        <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">

        <?php if ($isAlreadyInAQueue): ?>
            <div class="row my-2 alert bg-warning text-dark">
                <div class="col">ඔබ දැනටමත් පොරොත්තු ලයිස්තුවකට ඇතුළත් වී ඇත.</div>
            </div>
            <div class="row my-4">
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php else: ?>
            <div class="row my-2">
                <div class="col">ඔබ <?php echo htmlspecialchars($agency, ENT_COMPAT); ?>හි පොරොත්තු ලයිස්තුවට ඇතුළත් වීමට ඉල්ලුම් කර ඇත.</div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="buyerActivationOTP" class="my-1">කේතය</label>
                    <input type="text" maxlength="6" minlength="6" pattern="^\d{6}$"
                           class="form-control text-center" id="buyerActivationOTP" name="buyerActivationOTP"
                           placeholder="XXXXXX">
                </div>
            </div>

            <div class="row my-4">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-success">සක්රිය කරන්න</button>
                </div>
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php endif; ?>
    </form>

</main>

<?php include_once '../templates/footer.php' ?>

