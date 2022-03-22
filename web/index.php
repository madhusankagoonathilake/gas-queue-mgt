<?php

session_start();

include_once '../common/session.php';

if (isLoggedIn()) {
    header('Location: /agency-dashboard.php');
}

include_once '../templates/header.php';
?>

<main class="px-3 py-3 mt-5">
    <h1>මම</h1>
    <p class="lead">&nbsp;</p>
    <p class="lead">
        <a href="agency.php" class="btn btn-lg btn-secondary fw-bold border-primary bg-primary text-white m-3">ඒජන්සි
            හිමියෙක්මි</a>
        <a href="buyer.php" class="btn btn-lg btn-secondary fw-bold border-success bg-success text-white m-3">ගැණුම්කරුවෙක්මි</a>
    </p>
</main>

<?php include_once '../templates/footer.php' ?>
