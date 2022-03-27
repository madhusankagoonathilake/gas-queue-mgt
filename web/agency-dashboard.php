<?php

session_start();

include_once '../common/session.php';
include_once '../common/agency.php';

if (!isLoggedIn()) {
    header('Location: /login.php');
}

try {
    $agencyId = getSessionValue('agency-id');
    list($name, $city, $queueLength, $currentBatchSize, $availableAmount, $issuedAmount) = findAgencyDetailsById($agencyId);

    setSessionValues([
        'agency-name' => $name,
        'agency-city' => $city,
        'agency-queueLength' => $queueLength,
    ]);

} catch (\Exception $e) {
    echo "Cannot get values from session. Exiting...";
    exit(1);
}

$issuedPercentage = ($currentBatchSize == 0) ? 0 : ceil($issuedAmount / $currentBatchSize * 100);
$availablePercentage = ($currentBatchSize == 0) ? 0 : (100 - $issuedPercentage);

include_once '../templates/header.php';
?>

<main class="px-3 py-3 mt-5 ">
    <h1>ගෑස් නිකුත් කිරීම</h1>
    <p class="lead my-4"><?php echo htmlspecialchars($name, ENT_COMPAT); ?>
        - <?php echo htmlspecialchars($city, ENT_COMPAT); ?></p>
    <div class="lead">

        <div class="row">
            <div class="col-md-6 text-center">
                <?php echo generateCurrentBatchStatusDonutChart($issuedAmount, $currentBatchSize, $issuedPercentage, $availablePercentage); ?>
                <br/>
                <small>වත්මන් තොගය</small>
            </div>

            <div class="col-md-6 text-center">
                <?php echo generateQueueGraphic($queueLength, $currentBatchSize, $issuedAmount); ?>
                <br/>
                <small>පොරොත්තු ලයිස්තුව</small>
            </div>
        </div>

        <div class="row">

            <div class="col m-2 align-content-start">
                <button class="btn btn-warning" onclick="location.href = 'agency-issue-batch.php';"
                        <?php if ($availablePercentage != 0): ?>disabled<?php endif; ?>>
                    නිකුත්කිරීම්
                </button>
            </div>

            <div class="col m-2 align-content-end">
                <button class="btn btn-success" onclick="location.href = 'agency-validate-buyer.php';"
                        <?php if ($availablePercentage == 0): ?>disabled<?php endif; ?>>
                    අළෙවිකිරීම්
                </button>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-sm-12">
                <a href="logout.php" class="btn btn-link">පිටවීම</a>
            </div>
        </div>

    </div>
</main>

<?php include_once '../templates/footer.php' ?>
