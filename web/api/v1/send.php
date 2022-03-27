<?php
require_once '../../../common/config.php';
if(CONFIG['sms']['prod']){
    header("HTTP/1.0 404 Not Found");
    exit();
}
$fileName =  'display'.preg_replace('/^0/', '94',$_GET['phone_number']).'.txt';

$homepage = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.$fileName);
?>
<html>
<head>
    <title><?php echo $fileName ?></title>
    <meta http-equiv="refresh" content="5" />
</head>
<body>
<textarea cols="100" rows="10">
    <?php echo $homepage?>
</textarea>
</body>
</html>

