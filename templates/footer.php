<?php
    session_start();
    include_once('../common/session.php');

    if (getLanguage() == 'si') {
        $line0 = 'ශ්‍රී ලාංකිකයන් දැනට මුහුණපා සිටින අසීරුතා යම්තාක් හෝ අඩු කරගැනීමේ පරමාර්ථයෙන් නොමිලයේ සපයන පොදු සේවාවකි. මෙය
        අවභාවිතාවෙන් හෝ හානිකර ක්‍රියාවන්ගෙන් වළකින මෙන් කාරුණිකව ඉල්ලා සිටිමු.';
        $line1 = 'ඔබේ පුද්ගලික තොරතුරු (දුරකථන අංකය) මෙම පද්ධය්තියෙන් පිටත කිසිඳු කටයුත්තක් සඳහා ලබා දීමක් හෝ ඔබ ගෑස් සිලින්ඩරය
    මිලදී ගැනීමෙන් පසු පද්ධතිය තුළ රඳවා තබාගැනීමක් හෝ සිදු කෙරෙන්නේ නැත.';
        $line2 = 'වැඩිදුර තොරතුරු';
        $line3 = 'මෙය';
        $line4 = 'නිදහස් හා විවෘත (Free &amp; Open Source) මෘදුකාංගයකි';
    } else if (getLanguage() == 'en') {
        $line0 = 'This is a free service in order to mitigate the hardships faced by Sri Lankans in finding LP gas';
        $line1 = 'We do not share your contact number with any third patry, nor do we keep it shored in the system once your LP gas cylinder has been issued';
        $line2 = 'More details';
        $line3 = 'This is a';
        $line4 = 'free and open source software';
    }    
?>
<footer class="mt-auto bg-dark text-white-50 py-1 small">
    <p><?php echo $line0; ?></p>
    <?php echo $line1; ?> (<a href="privacy.php"><?php echo $line2; ?></a>).
    <?php echo $line3 ; ?>
    <a href="https://github.com/madhusankagoonathilake/gas-queue-mgt" target="_blank"><?php echo $line4; ?></a>.
</footer>
</div>


</body>
</html>
