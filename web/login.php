<?php

session_start();

include_once '../common/security.php';

$csrfToken = generateCsrfToken();

include_once '../templates/header.php';
include_once '../common/agency-list.php';
?>
<main class="px-3 py-3 mt-5 ">
    <h1>ඒජන්සි හිමිකරු</h1>
    <p class="lead">&nbsp;</p>
    <p class="lead">
        <strong>ඇතුළුවීම</strong>
    </p>
    <form class="lead" method="post" action="login-validation.php">
        <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">


        <div class="row">
            <div class="col">
                <label for="telephone" class="my-1">ලියාපදිංචි වීමේදී යොදාගත් දුරකථන අංකය</label>
                <input type="tel" maxlength="10" minlength="10" pattern="^07[01245678]\d{7}$"
                       class="form-control text-center" id="telephone" name="telephone"
                       placeholder="07XXXXXXXX">
                <small class="text-white-50">මීළඟ පියවරේදී ඔබට SMS මගින් එනවන මුරපදය ඇතුළත් කරන්න.</small>
            </div>

        </div>

        <div class="row my-4">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-success">ඉදිරියට යන්න</button>
            </div>
            <div class="col-sm-12">
                <a href="/" class="btn btn-link">ආපසු</a>
            </div>
        </div>
    </form>

</main>

<?php include_once '../templates/footer.php' ?>
