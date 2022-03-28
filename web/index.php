<?php

session_start();

include_once '../common/session.php';

if (isLoggedIn()) {
    header('Location: /agency-dashboard.php');
}

if (isset($_COOKIE['language'])) {
    if (strlen($_COOKIE['language']) == 2) {
        if ($_COOKIE['language'] == 'si') {
            setSessionValues(['language' => 'si']);
        } else if ($_COOKIE['language'] == 'en') {
            setSessionValues(['language' => 'en']);
        }  else if ($_COOKIE['language'] == 'ta') {
            setSessionValues(['language' => 'ta']);
        } else {
            setSessionValues(['language' => 'si']);
        }
    } else {
        setSessionValues(['language' => 'si']);
    }
}

if (isset($_POST['language'])) {
    if ($_POST['language'] == 'si') {
        setcookie('language', 'si', time() + (86400 * 30), "/");
        setSessionValues(['language' => 'si']);
    } else if ($_POST['language'] == 'en') {
        setcookie('language', 'en', time() + (86400 * 30), "/");
        setSessionValues(['language' => 'en']);
    } else if ($_POST['language'] == 'ta') {
        setcookie('language', 'ta', time() + (86400 * 30), "/");
        setSessionValues(['language' => 'ta']);
    }
}  

if (getSessionValue('language') == 'si') {
    $line0 = "මම";
    $line1 = "ඒජන්සි හිමියෙක්මි";
    $line2 = "ගනුම්කරුවෙක්මි";
} else if (getSessionValue('language') == 'en') {
    $line0 = "I an";
    $line1 = "Agency Owner";
    $line2 = "Buyer";
}

include_once '../templates/header.php';
?>

<main class="px-3 py-3 mt-5">
    <h1><?php echo $line0; ?></h1>
    <p class="lead">&nbsp;</p>
    <p class="lead">
        <a href="agency.php" class="btn btn-lg btn-secondary fw-bold border-primary bg-primary text-white m-3"><?php echo $line1; ?></a>
        <a href="buyer.php" class="btn btn-lg btn-secondary fw-bold border-success bg-success text-white m-3"><?php echo $line2; ?></a>
    </p>
    <div class="mt-3">
        <h4>කරුණාකර ඔබගේ භාෂාව තෝරන්න | Please select your language</h4>
        <form action="./" method="post">
            <label for="language">භාෂාව | Language</label>
            <select name="language" id="language" class="form-select">
                <option value="si">සිංහල | Sinhala</option>
                <option value="en">ඉංග්‍රීසි | English</option>
            </select>
            <button type="submit" class="btn btn-success">භාෂාව තෝරන්න | Select Language</button>
        </form>
    </div>   
</main>

<?php include_once '../templates/footer.php' ?>
