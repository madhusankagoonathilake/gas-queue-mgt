<?php

session_start();

include_once '../common/security.php';
include_once '../common/session.php';
include_once '../common/buyer.php';
include_once '../common/sms.php';
include_once '../common/sms.php';

if (!isPostRequest()) {
    echo "Invalid access";
    exit(1);
}

$csrfToken = filter_input(INPUT_POST, 'csrfToken', FILTER_SANITIZE_STRING);
$agency = filter_input(INPUT_POST, 'agency', FILTER_SANITIZE_STRING);
$telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);

if (!isCsrfTokenValid($csrfToken)) {
    echo "CSRF attack";
    exit(1);
}

$agencyExists = agencyExists($agency);
$success = false;

if ($agencyExists) {

    $isAlreadyInAQueue = isAlreadyInAQueue($telephone);

    if (!$isAlreadyInAQueue) {
        $buyerActivationOTP = generateBuyerActivationOTP();

        setSessionValues([
            'agency' => $agency,
            'telephone' => $telephone,
            'buyerActivationOTP' => $buyerActivationOTP,
            'buyerActivationAttempts' => 0,
        ]);

        $buyerActivationMessage = prepareBuyerActivationMessage($buyerActivationOTP);
        try {
            sendSMS($telephone, $buyerActivationMessage);
            $success = true;
        } catch (\Exception $e) {
            app_log('EROR', $e->getMessage());
        }
    }
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

        <?php if (!$agencyExists): ?>
            <div class="row my-2 alert bg-danger">
                <div class="col">ඔබ සඳහන් කළ ආයතනය මේ සේවා සමඟ ලියාපදිංචි වී නැත!</div>
            </div>
            <div class="row my-4">
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php elseif ($isAlreadyInAQueue): ?>
            <div class="row my-2 alert bg-warning text-dark">
                <div class="col">ඔබ දැනටමත් පොරොත්තු ලයිස්තුවකට ඇතුළත් වී ඇත.</div>
            </div>
            <div class="row my-4">
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php elseif (!$success): ?>
            <div class="row my-2 alert bg-warning text-dark">
                <div class="col">කණගාටුයි! පද්ධතියේ දෝෂයක් හේතුවෙන් මෙම අවස්ථාවේදී ඔබව මෙම ආයතනයේ පොරොත්තු ලයිස්තුවට
                    ඇතුළත් කරගත නොහැක. අප මෙය නිවරද කිරීමට ඉක්මනින් කටයුතු කරන්නෙමු.
                </div>
            </div>
            <div class="row my-4">
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php else: ?>
            <div class="row my-2">
                <div class="col">ඔබ <?php echo htmlspecialchars($agency, ENT_COMPAT); ?>හි පොරොත්තු ලයිස්තුවට ඇතුළත්
                    වීමට ඉල්ලුම් කර ඇත.
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="buyerActivationOTP" class="my-1">කේතය</label>
                    <input type="text" maxlength="6" minlength="6" pattern="^\d{6}$" required
                           class="form-control text-center" id="buyerActivationOTP" name="buyerActivationOTP"
                           placeholder="XXXXXX" autocomplete="off">
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

