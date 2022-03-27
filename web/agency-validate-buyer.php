<?php

session_start();

include_once '../common/session.php';
include_once '../common/security.php';
include_once '../common/agency.php';

if (!isLoggedIn()) {
    header('Location: /login.php');
}

try {
    $name = getSessionValue('agency-name');
    $city = getSessionValue('agency-city');
} catch (\Exception $e) {
    echo "Cannot get values from session. Exiting...";
    exit(1);
}

$csrfToken = generateCsrfToken();

include_once '../templates/header.php';
?>

<main class="px-3 py-3 mt-5 ">
    <h1>ගෑස් නිකුත් කිරීම</h1>
    <p class="lead my-4"><?php echo htmlspecialchars($name, ENT_COMPAT); ?>
        - <?php echo htmlspecialchars($city, ENT_COMPAT); ?></p>

    <form class="lead" method="post" action="agency-verify-queue-otp.php">
        <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">

        <div class="row">
            <div class="col-12 my-3">
                <label for="queueOTP" class="my-1">මුරපදය</label>
                <input type="text" maxlength="8" minlength="8" pattern="^[a-f0-9]{8}$" required
                       class="form-control text-center" id="queueOTP" name="queueOTP"
                       placeholder="XXXXXXXX" autocomplete="off">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button class="btn btn-success">පරීක්ෂා කරන්න</button>
            </div>
        </div>

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
