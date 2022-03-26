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

$batchSize = filter_input(INPUT_POST, 'batchSize', FILTER_SANITIZE_NUMBER_INT);

$success = false;
try {
    $success = issueBatch($agencyId, $batchSize);
    setSessionValue('csrfToken', null);
} catch (\Exception $e) {
    app_log('ER0R', $e->getMessage());
}


include_once '../templates/header.php';
?>

<main class="px-3 py-3 mt-5 ">
    <h1>ගෑස් නිකුත් කිරීම</h1>
    <p class="lead my-4"><?php echo htmlspecialchars($name, ENT_COMPAT); ?>
        - <?php echo htmlspecialchars($city, ENT_COMPAT); ?></p>

    <?php if ($success): ?>
    <div class="row alert bg-success">
        <div class="col-12">
                ගෑස් සිලින්ඩර කාණ්ඩය නිකුත් කරන ලදී. පොරොත්තු ලයිස්තුවේ මුල් <?php echo htmlentities($batchSize, ENT_COMPAT); ?>දෙනාට
                SMS මගින් දැනුවත් කෙරෙණු ඇත.
        </div>
    </div>
    <?php else: ?>
        <div class="row alert bg-warning text-dark">
            <div class="col-12">
                කණගාටුයි! පද්ධතියේ තාක්ෂණික දෝෂයක් නිසා ගෑස් සිලින්ඩර කාණ්ඩය නිකුත් කිරීමට නොහැකි වුනි.
            </div>
        </div>
    <?php endif; ?>
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
