<?php

session_start();

include_once '../common/session.php';
include_once '../common/security.php';
include_once '../common/agency.php';

if (!isLoggedIn()) {
    header('Location: /login.php');
}

if (!isPostRequest()) {
    echo "Invalid access";
    exit(1);
}

$csrfToken = filter_input(INPUT_POST, 'csrfToken', FILTER_SANITIZE_STRING);
if (!isCsrfTokenValid($csrfToken)) {
    echo "CSRF attack";
    exit(1);
}

try {
    $agencyId = getSessionValue('agency-id');
    $name = getSessionValue('agency-name');
    $city = getSessionValue('agency-city');

} catch (\Exception $e) {
    echo "Cannot get values from session. Exiting...";
    exit(1);
}

$queueOTP = filter_input(INPUT_POST, 'queueOTP', FILTER_SANITIZE_STRING);

$buyerStatus = getBuyerStatusByQueueOTP($agencyId, $queueOTP);

include_once '../templates/header.php';
?>

<main class="px-3 py-3 mt-5 ">
    <h1>ගෑස් නිකුත් කිරීම</h1>
    <p class="lead my-4"><?php echo htmlspecialchars($name, ENT_COMPAT); ?>
        - <?php echo htmlspecialchars($city, ENT_COMPAT); ?></p>

    <form class="lead" method="post" action="agency-confirm-purchase.php">
        <?php if ($buyerStatus === 'NOTIFIED'): ?>
            <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">
            <input type="hidden" name="queueOTP" value="<?php echo $queueOTP; ?>">
            <div class="row my-2 alert bg-success">
                <div class="col">
                    මෙම මුරපදය වලංගු වන අතර, එය ඉදිරිපත් කළ පුද්ගලයා පොරොත්තු ලයිස්තුවේ නියමිත ස්ථානයේ රැඳී සිටින්නෙකි.
                </div>
            </div>
            <div class="row my-5">
                <div class="col-12">
                    <button class="btn btn-success">විකිණීම තහවුරු කරන්න</button>
                </div>
            </div>
        <?php elseif ($buyerStatus === 'EXPIRED'): ?>
            <div class="row my-2 alert bg-warning text-dark">
                <div class="col">
                    මෙම මුරපදය දින 3කට පසුව කල් ඉකුත්වී ඇත.
                </div>
            </div>
        <?php elseif ($buyerStatus === 'ERROR'): ?>
            <div class="row my-2 alert bg-warning text-dark">
                <div class="col">
                    කණගාටුයි! පද්ධතියේ තාක්ෂණික දෝෂයක් නිසා මේ අවස්තාවේදී මෙම මුරපදයේ වලංගුතාවය පරීක්ෂා කළ නොහැක.
                </div>
            </div>
        <?php else: ?>
            <div class="row my-2 alert bg-danger">
                <div class="col">
                    මෙම මුරපදය වලංගු නොවේ. මෙය ඉදිරිපත් කළ පුද්ගලයා පොරොත්තු ලයිස්තුවේ රැඳී සිටින්නෙකු නොවේ.
                </div>
            </div>
        <?php endif; ?>


    </form>

    <div class="row my-4">
        <div class="col-sm-12">
            <a href="agency-dashboard.php" class="btn btn-link">ආපසු</a>
        </div>
        <div class="col-sm-12">
            <a href="logout.php" class="btn btn-link">පිටවීම</a>
        </div>
    </div>

</main>

<?php include_once '../templates/footer.php' ?>
