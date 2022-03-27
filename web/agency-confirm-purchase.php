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

$success = confirmPurchase($agencyId, $queueOTP);

include_once '../templates/header.php';
?>

<main class="px-3 py-3 mt-5 ">
    <h1>ගෑස් නිකුත් කිරීම</h1>
    <p class="lead my-4"><?php echo htmlspecialchars($name, ENT_COMPAT); ?>
        - <?php echo htmlspecialchars($city, ENT_COMPAT); ?></p>

    <form class="lead" method="post" action="agency-confirm-purchase.php">
        <?php if ($success): ?>
            <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">
            <div class="row my-2 alert bg-success">
                <div class="col">
                    ගනුදෙනුව සාර්ථකව සටහන් කරගන්නා ලදී.
                </div>
            </div>
        <?php else: ?>
            <div class="row my-2 alert bg-warning text-dark">
                <div class="col">
                    කණගාටුයි! පද්ධතියේ තාක්ෂණික දෝෂයක් නිස ගනුදෙනුව සටහන් කරගැනීමට නොහැකි විය. නැවත උත්සාහ කරන්න. දිගින්
                    දිගටම අසාර්ථක වේ නම් <a href="report-a-bug.php">දෝෂය වාර්තා කරන්න</a>..
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
