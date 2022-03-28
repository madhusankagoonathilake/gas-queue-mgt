<?php 
    session_start();

    include_once '../common/session.php';
    
    if (isset($_COOKIE['language'])) {
        setSessionValues(array('language' => $_COOKIE['languae']));
    } else {
        setSessionValues(array('language' => 'si'));
    }
?> 
<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>ගෑස් පෝලිම් සහයකය - ශ්‍රී ලංකාව - 2022</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/cover/">

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicons -->
    <link rel="icon" href="/docs/5.0/assets/img/favicons/favicon.ico">
    <meta name="theme-color" content="#7952b3">


    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>


    <!-- Custom styles for this template -->
    <link href="assets/css/cover.css" rel="stylesheet">
</head>
<body class="d-flex h-100 text-center text-white bg-dark">

<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <header class="mb-auto fixed-top bg-light text-dark">
        <div class="py-2">
            <h3 class="mb-0"><a href="/" class="text-dark text-decoration-none">ගෑස් පෝලිම් සහයකය (beta)</a></h3>
        </div>
    </header>

