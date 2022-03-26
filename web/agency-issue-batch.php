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
    $queueLength = getSessionValue('agency-queueLength');
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

    <?php if ($queueLength > 0): ?>
        <form class="lead" method="post" action="agency-persist-issued-batch.php">
            <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">

            <div class="row">
                <div class="col-12">
                    <h1 class="display-1" id="display">1</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12 my-3">
                    <input type="range" id="batchSize" name="batchSize" class="form-control form-range" value="1"
                           min="1" max="<?php echo htmlentities($queueLength, ENT_COMPAT); ?>"
                           oninput="document.getElementById('display').innerText = this.value;"
                           onchange="document.getElementById('display').innerText = this.value;">

                    <label for="batchSize" class="my-2">පොරොත්තු ලයිස්තුවේ සිටින්නන්ට නිකුත් කරන ගෑස් සිලින්ඩර
                        ගණන</label>
                    <small class="text-white-50">කරුණාකර ලබාදිය හැකි සංඛ්‍යාවට වඩා නිකුත් නොකරන්න. මෙහි සනාථ කිරීමට පෙර
                        නැවත පරීක්ෂා කරන්න.</small>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-success">නිකුත් කරන්න</button>
                </div>
            </div>

        </form>
    <?php else: ?>
        <div class="row">
            <div class="col-12 my-3">
                <label for="range" class="my-2">ඔබගේ පොරොත්තු ලයිස්තුව හිස් බැවින් දැනට ගෑස් සිලින්ඩර නිකුත් කිරීම
                    අවශ්‍ය නොවේ.</label>
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
