<?php

session_start();

include_once '../common/security.php';

$csrfToken = generateCsrfToken();

include_once '../templates/header.php';
include_once '../common/city-list.php';

?>
<main class="px-3 py-3 mt-5 ">
    <h1>ඒජන්සි හිමිකරු</h1>
    <p class="lead">&nbsp;</p>
    <p class="lead">
        <strong>ලියාපදිංචි වීම</strong>
    </p>
    <form class="lead" method="post" action="agency-activation.php">
        <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">

        <div class="row">
            <div class="col">
                <label for="name" class="my-1">ආයතනයේ නම</label>
                <input type="text" id="name" class="form-control text-center"
                       placeholder="උදාහරණ: : Giripura Trade Center" name="name" lang="en-US" autocomplete="off">
            </div>
        </div>

        <div class="row">
            <div class="col">
                <label for="city" class="my-1">නගරය</label>
                <input type="search" class="form-control text-center" id="city" name="city"
                       placeholder="නගරය තෝරන්න" list="cityList" required autocomplete="off">
            </div>

        </div>

        <div class="row">
            <div class="col">
                <label for="telephone" class="my-1">ඔබේ ජංගම දුරකථන අංකය<br/>
                    <small class="text-white-50">පද්ධතියට ඇතුළු වීමට ඉදිරියේදී මෙම අංකය භාවිතා වේ. ඒ නිසා ඔබගේ අයාතනයේ
                        ස්ථාවර දුරකථන අංකය වෙනුවට ඔබේ ජංගම දුරකථන අංකය ඇතුළත් කරන්න. </small>
                </label>
                <input type="tel" maxlength="10" minlength="10" pattern="^07[01245678]\d{7}$"
                       class="form-control text-center" id="telephone" name="telephone"
                       placeholder="07XXXXXXXX">
            </div>

        </div>

        <div class="row my-4">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-success">ලියාපදිංචි වන්න</button>
            </div>
            <div class="col-sm-12">
                <a href="/" class="btn btn-link">ආපසු</a>
            </div>
        </div>
    </form>

</main>

<?php include_once '../templates/footer.php' ?>
