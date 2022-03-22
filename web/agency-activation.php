<?php

session_start();

include_once '../common/security.php';
include_once '../common/session.php';
include_once '../common/agency.php';
include_once '../common/sms.php';

if (!isPostRequest()) {
    echo "Invalid access";
    exit(1);
}

$csrfToken = filter_input(INPUT_POST, 'csrfToken', FILTER_SANITIZE_STRING);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
$telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);

if (!isCsrfTokenValid($csrfToken)) {
    echo "CSRF attack";
    exit(1);
}

$agencyExists = agencyExists("{$name} - {$city}");
$isTelephoneNumberInUse = isTelephoneNumberInUse($telephone);
$success = false;

if (!$agencyExists && !$isTelephoneNumberInUse) {

    $agencyActivationOTP = generateAgencyActivationOTP();

    setSessionValues([
        'agency-name' => $name,
        'agency-city' => $city,
        'agency-telephone' => $telephone,
        'agency-activationOTP' => $agencyActivationOTP,
        'agency-activationAttempts' => 0,
    ]);

    $agencyActivationMessage = prepareAgencyActivationMessage($agencyActivationOTP);
    try {
        sendSMS($telephone, $agencyActivationMessage);
        $success = true;
    } catch (\Exception $e) {
        // TODO: Log error
    }

}
include_once '../templates/header.php';
?>
<main class="px-3 py-3 mt-5 ">
    <h1>ඒජන්සි හිමිකරු</h1>
    <p class="lead">&nbsp;</p>
    <p class="lead">
        <strong>සක්‍රීය කිරීම</strong>
    </p>
    <form class="lead" method="post" action="agency-activation-otp-verification.php">
        <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">

        <?php if ($agencyExists): ?>
            <div class="row my-2 alert bg-danger">
                <div class="col">ඔබ සඳහන් කළ නමින් ආයතනයක් දැනටමත් මෙම සේවාව සමඟ ලියාපදිංචි වී ඇත.</div>
            </div>
            <div class="row my-4">
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php elseif ($isTelephoneNumberInUse): ?>
            <div class="row my-2 alert bg-danger">
                <div class="col">මෙම දුරකථන අංකයෙන් දැනටමත් ආයතනයක් ලියාපදිංචි වී ඇත.</div>
            </div>
            <div class="row my-4">
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php elseif (!$success): ?>
            <div class="row my-2 alert bg-warning text-dark">
                <div class="col">කණගාටුයි! පද්ධතියේ දෝෂයක් හේතුවෙන් මෙම අවස්ථාවේදී ඔබේ ආයතනය ලියාපදිංචි කිරීමට අපොහොසත්
                    වුනි. අප මෙය නිවරද කිරීමට ඉක්මනින් කටයුතු කරන්නෙමු.
                </div>
            </div>
            <div class="row my-4">
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php else: ?>
            <div class="row my-2">
                <div class="col">ඔබ විසින් <?php echo htmlspecialchars($city, ENT_COMPAT); ?> නගරයේ
                    <?php echo htmlspecialchars($name, ENT_COMPAT); ?> නැමති ව්‍යාපාරික ස්ථානය ගෑස් පෝලිම් සහයකයේ
                    ලියාපදිංචි කීරීමට ඉල්ලා ඇත. එය සනාථ කිරීමට ඔබ ඇතුලුකල දුරථනයට ලැබී ඇති SMS පණිවිඩයේ සඳහන් කේතය ඇතුළු
                    කරන්න.
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="agencyActivationOTP" class="my-1">කේතය</label>
                    <input type="text" maxlength="6" minlength="6" pattern="^\d{6}$"
                           class="form-control text-center" id="agencyActivationOTP" name="agencyActivationOTP"
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

