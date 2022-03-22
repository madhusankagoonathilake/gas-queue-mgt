<?php

session_start();

include_once '../common/session.php';

setSessionValues(['agency-id' => null]);

header('Location: /');
