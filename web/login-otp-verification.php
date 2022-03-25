<?php

session_start();

require_once '../common/security.php';
require_once '../common/session.php';
require_once '../common/agency.php';

if (!isPostRequest()) {
    echo "Invalid access";
    exit(1);
}

$csrfToken = filter_input(INPUT_POST, 'csrfToken');
if (!isCsrfTokenValid($csrfToken)) {
    echo "CSRF attack";
    exit(1);
}

$agencyLoginOTP = filter_input(INPUT_POST, 'agencyLoginOTP');
try {
    incrementSessionValue('agency-loginAttempts');
} catch (\Exception $e) {
    echo "Error getting session values. Exiting...";
    exit(1);
}
$positionedAt = null;
$isActivationSuccessful = false;

try {
    $telephone = getSessionValue('agency-telephone');
    $loginAttempts = getSessionValue('agency-loginAttempts');
} catch (\Exception $e) {
    echo "Error getting session values. Exiting...";
    exit(1);
}

if (isAgencyLoginOTPValid($agencyLoginOTP)) {
    
    try {
        setSessionValues([
            'agency-name' => null,
            'agency-city' => null,
            'agency-telephone' => null,
            'agency-activationOTP' => null,
            'agency-activationAttempts' => null,
            'agency-id' => findAgencyIdByTelephone($telephone),
        ]);

        header('Location: /agency-dashboard.php');

    } catch (\Exception $e) {
        // TODO: Differentiate the error message display based on the scenario
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
    <form class="lead" method="post" action="agency-activation-otp-verification.php">

        <?php if ($loginAttempts >= CONFIG['app']['maxActivationAttempts']): ?>
            <div class="row my-2 alert bg-danger">
                <div class="col">කණගාටුයි! ඔබගේ ප්‍රවේශයට ලැබී ඇති වාර සංඛ්‍යාව අවසන් වී ඇත. කරුණාකර විනාඩි 30කින් පසු
                    නැවත උත්සාහ කරන්න.
                </div>
            </div>
        <?php else: ?>
            <div class="row my-2 alert bg-warning text-dark">
                <div class="col">ප්‍රවේශය සාර්ථක නොවුණි! කරුණාකර නැවත උත්සාහ කරන්න.
                </div>
            </div>

            <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">

            <div class="row">
                <div class="col">
                    <label for="agencyLoginOTP" class="my-1">ඔබ ඇතුලුකල දුරථනයට ලැබී ඇති SMS පණිවිඩයේ සඳහන් කේතය ඇතුළු
                        කරන්න.</label>
                    <input type="text" maxlength="6" minlength="6" pattern="^\d{6}$"
                           class="form-control text-center" id="agencyLoginOTP" name="agencyLoginOTP"
                           placeholder="XXXXXX" autocomplete="off">
                </div>
            </div>

            <div class="row my-4">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-success">නැවත උත්සාහ කරන්න</button>
                </div>
                <div class="col-sm-12">
                    <a href="/" class="btn btn-link">ආපසු</a>
                </div>
            </div>
        <?php endif; ?>
    </form>

</main>

<?php include_once '../templates/footer.php' ?>

