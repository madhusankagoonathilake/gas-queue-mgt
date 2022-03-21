<?php

session_start();

include_once '../common/security.php';

$csrfToken = generateCsrfToken();

include_once '../templates/header.php';
include_once '../common/agency-list.php';
?>
<main class="px-3 py-3 mt-5 ">
    <h1>ගැණුම්කරු</h1>
    <p class="lead">&nbsp;</p>
    <p class="lead">
        <strong>පියවර</strong>
        <br />
        <small class="text-white-50">ඔබට එක වරකට ඇතුලත් විය හැක්කේ එක විකිණුම් පොලක පොරොත්තු ලයිස්තුවකට පමණි. තහවුරු කිරීමෙන්
            පසු එය වෙනස් කළ නොහැක. එනිසා ඔබ අයත් නගරයේ නිවැරදි වෙළඳ ආයතනය තෝරාගන්න.</small>
    </p>
    <form class="lead" method="post" action="buyer-activation.php">
        <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">

        <div class="row">
            <div class="col">
                <label for="agency" class="my-1">ආයතනය</label>
                <input type="search" id="agency" required list="agencyList" class="form-control text-center"
                       placeholder="ඔබට අවශ්‍ය වෙළඳ ආයතනය තෝරන්න" name="agency" autocomplete="off" lang="en-US">
            </div>
        </div>

        <div class="row">
            <div class="col">
                <label for="telephone" class="my-1">ඔබේ ජංගම දුරකථන අංකය</label>
                <input type="tel" maxlength="10" minlength="10" pattern="^07[01245678]\d{7}$"
                       class="form-control text-center" id="telephone" name="telephone"
                       placeholder="07XXXXXXXX">
            </div>

        </div>

        <div class="row my-4">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-success">තහවුරු කරන්න</button>
            </div>
            <div class="col-sm-12">
                <a href="/" class="btn btn-link">ආපසු</a>
            </div>
        </div>
    </form>

</main>

<?php include_once '../templates/footer.php' ?>
