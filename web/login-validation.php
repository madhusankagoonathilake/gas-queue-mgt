<?php

session_start();

include_once '../common/security.php';
include_once '../common/session.php';
include_once '../common/agency.php';
include_once '../common/sms.php';
include_once '../common/sms.php';

if (!isPostRequest()) {
    echo "Invalid access";
    exit(1);
}

$csrfToken = filter_input(INPUT_POST, 'csrfToken', FILTER_SANITIZE_STRING);
$telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);

if (!isCsrfTokenValid($csrfToken)) {
    echo "CSRF attack";
    exit(1);
}

$isTelephoneNumberInUse = isTelephoneNumberInUse($telephone);
$success = false;

if ($isTelephoneNumberInUse) {

    $agencyLoginOTP = generateAgencyLoginOTP();

    setSessionValues([
        'agency-telephone' => $telephone,
        'agency-loginOTP' => $agencyLoginOTP,
        'agency-loginAttempts' => 0,
    ]);

    $agencyLoginOTPMessage = prepareAgencyLoginOTPMessage($agencyLoginOTP);
    try {
        sendSMS($telephone, $agencyLoginOTPMessage);
        $success = true;
    } catch (\Exception $e) {
        app_log('EROR', $e->getMessage());
    }

}
include_once '../templates/header.php';
?>
<main class="px-3 py-3 mt-5 ">
    <h1>ඒජන්සි හිමිකරු</h1>
    <p class="lead">&nbsp;</p>
    <p class="lead">
        <strong>ඇතුළුවීම</strong>
    </p>
    <form class="lead" method="post" action="login-otp-verification.php">
        <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">

        <?php if (!$isTelephoneNumberInUse): ?>
            <div class="row my-2 alert bg-danger">
                <div class="col">ඔබ සඳහන් කළ දුරකථන අංකයෙන් ආයතනයක් මෙම සේවාව සමඟ ලියාපදිංචි වී නැත.</div>
            </div>
            <div class="row my-4">
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php elseif (!$success): ?>
            <div class="row my-2 alert bg-warning text-dark">
                <div class="col">කණගාටුයි! තාක්ෂණික දෝෂයක් හේතුවෙන් මෙම අවස්ථාවේදී පද්ධතියට ප්‍රවේශ විය නොහැකිය. අප මෙය
                    නිවරද කිරීමට ඉක්මනින් කටයුතු කරන්නෙමු.
                </div>
            </div>
            <div class="row my-4">
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php else: ?>

            <div class="row">
                <div class="col">
                    <label for="agencyLoginOTP" class="my-1">ඔබ ඇතුලුකල දුරථනයට ලැබී ඇති SMS පණිවිඩයේ සඳහන් කේතය ඇතුළු
                        කරන්න.</label>
                    <input type="text" maxlength="6" minlength="6" pattern="^\d{6}$" required
                           class="form-control text-center" id="agencyLoginOTP" name="agencyLoginOTP"
                           placeholder="XXXXXX" autocomplete="off">
                </div>
            </div>

            <div class="row my-4">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-success">ඇතුළුවන්න</button>
                </div>
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php endif; ?>
    </form>

</main>

<?php include_once '../templates/footer.php' ?>

