<?php

session_start();

require_once '../common/dbh.php';

$requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

if ($requestMethod !== 'POST') {
    echo "Invalid access";
    exit(1);
}

$csrfToken = filter_input(INPUT_POST, 'csrfToken');

if ($csrfToken !== $_SESSION['csrfToken']) {
    echo "CSRF attack";
    exit(1);
}

$buyerActivationOTP = filter_input(INPUT_POST, 'buyerActivationOTP');
$isBuyerActivationOTPValid = $_SESSION['buyerActivationOTP'] == $buyerActivationOTP;
$_SESSION['buyerActivationAttempts']++;

$positionedAt = null;
$isActivationSuccessful = false;
$agency = $_SESSION['agency'];

if ($isBuyerActivationOTPValid) {

    $stmt = $dbh->prepare("SELECT id, queue FROM agency WHERE name = ? AND city = ?");
    $stmt->execute(explode(' - ', $_SESSION['agency']));
    list($agencyId, $queueJson) = $stmt->fetch(\PDO::FETCH_NUM);

    $queue = empty($queueJson) ? [] : json_decode($queueJson, true);

    $queueOTP = bin2hex(random_bytes(4));

    $queue[$_SESSION['telephone']] = [$queueOTP, null];

    try {
        $dbh->beginTransaction();
        $insertStmt = $dbh->prepare("INSERT INTO customers VALUES (?)");
        $insertStmt->execute([$_SESSION['telephone']]);

        $updateStmt = $dbh->prepare("UPDATE agency SET queue = ? WHERE id = ? ;");
        $updateStmt->execute([
            json_encode($queue),
            $agencyId,
        ]);

        $dbh->commit();

        $positionedAt = count($queue);

        $_SESSION['agency'] = null;
        $_SESSION['telephone'] = null;
        $_SESSION['csrfToken'] = null;
        $_SESSION['buyerActivationAttempts'] = null;
        $_SESSION['buyerActivationOTP'] = null;

        $isActivationSuccessful = true;

    } catch (\Exception $e) {
        $dbh->rollBack();
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
        <?php elseif ($_SESSION['buyerActivationAttempts'] >= CONFIG['app']['maxActivationAttempts']): ?>
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
                    <input type="text" maxlength="6" minlength="6" pattern="^\d{6}$"
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

