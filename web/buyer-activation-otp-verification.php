<?php

session_start();

require_once '../common/security.php';
require_once '../common/session.php';
require_once '../common/buyer.php';

if (!isPostRequest()) {
    echo "Invalid access";
    exit(1);
}

$csrfToken = filter_input(INPUT_POST, 'csrfToken');
if (!isCsrfTokenValid($csrfToken)) {
    echo "CSRF attack";
    exit(1);
}

$buyerActivationOTP = filter_input(INPUT_POST, 'buyerActivationOTP');
try {
    incrementSessionValue('buyerActivationAttempts');
} catch (\Exception $e) {
    echo "Error getting session values. Exiting...";
    exit(1);
}
$positionedAt = null;
$isActivationSuccessful = false;

try {
    $agency = getSessionValue('agency');
    $telephone = getSessionValue('telephone');
    $buyerActivationAttempts = getSessionValue('buyerActivationAttempts');
} catch (\Exception $e) {
    echo "Error getting session values. Exiting...";
    exit(1);
}

if (isBuyerActivationOTPValid($buyerActivationOTP)) {

    try {
        $positionedAt = addBuyerToQueue($telephone, $agency);

        setSessionValues([
            'agency' => null,
            'telephone' => null,
            'csrfToken' => null,
            'buyerActivationAttempts' => null,
            'buyerActivationOTP' => null,
        ]);

        $isActivationSuccessful = true;

    } catch (\Exception $e) {
        // TODO: Differentiate the error message display based on the scenario
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
        <?php if ($isActivationSuccessful): ?>
            <div class="row my-2 alert bg-success">
                <div class="col">ඔබ <?php echo htmlspecialchars($agency, ENT_COMPAT); ?>හි පොරොත්තු
                    ලයිස්තුවේ <strong><?php echo $positionedAt; ?></strong> ස්ථානයට සාර්ථකව ඇතුළත් කරන ලදී.
                    ඔබගේ අවස්ථාව එළඹි විට SMS පණිවිඩයක් මගින් දැනුවත් කෙරෙනු ඇත.
                </div>
            </div>
        <?php elseif ($buyerActivationAttempts >= CONFIG['app']['maxActivationAttempts']): ?>
            <div class="row my-2 alert bg-danger">
                <div class="col">කණගාටුයි! ඔබව <?php echo htmlspecialchars($agency, ENT_COMPAT); ?>හි
                    පොරොත්තු ලයිස්තුවට ඇතුළත් කිරීම සාර්ථක නොවුනි. කරුණාකර විනාඩි 30කින් පසු නැවත උත්සාහ කරන්න.
                </div>
            </div>
        <?php else: ?>
            <div class="row my-2 alert bg-warning text-dark">
                <div class="col">සක්‍රීය කිරීම සාර්ථක නොවුණි! කරුණාකර නැවත උත්සාහ කරන්න.
                </div>
            </div>

            <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">
            <div class="row my-2">
                <div class="col">ඔබ <?php echo htmlspecialchars($agency, ENT_COMPAT); ?>හි පොරොත්තු
                    ලයිස්තුවට ඇතුළත් වීමට ඉල්ලුම් කර ඇත.
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="buyerActivationOTP" class="my-1">කේතය</label>
                    <input type="text" maxlength="6" minlength="6" pattern="^\d{6}$" required
                           class="form-control text-center" id="buyerActivationOTP" name="buyerActivationCode"
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

