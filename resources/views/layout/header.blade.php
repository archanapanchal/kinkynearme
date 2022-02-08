<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="<?= config('CURRENT_LOCALE_DIRECTION') ?>" class="lw-light-theme">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?= getStoreSettings('name') ?></title>

  <?= __yesset([
    //'dist/css/public-assets-app*.css',
    'dist/css/bootstrap.css',
    'dist/fa/css/all.min.css',
    'dist/css/styles.css',
    'dist/css/responsive.css',
    'dist/css/animate.css',
    'dist/css/card_demo.css',
    'dist/css/card_styles.css',
    //'dist/css/home*.css'
  ], true) ?>
  <link rel="shortcut icon" href="<?= getStoreSettings('favicon_image_url') ?>" type="image/x-icon">
  <link rel="icon" href="<?= getStoreSettings('favicon_image_url') ?>" type="image/x-icon">
</head>

<body id="page-top">

  <div class="wrapper">
    <header class="header">
      <nav class="navbar navbar-expand-lg">
        <div class="container">
          <a href="<?= route('landing_page') ?>" class="navbar-brand">
            <img class="lw-logo-img" src="<?= getStoreSettings('logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
          </a>
            <button type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler navbar-toggler-right"><i class="fa fa-bars"></i></button>
          <div id="navbarSupportedContent" class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto" id="mainNav">
              <li class="nav-item active"><a href="#" class="nav-link">Home<span class="sr-only">(current)</span></a></li>
              <li class="nav-item"><a href="#" class="nav-link">About us</a></li>
              <li class="nav-item"><a href="#" class="nav-link">Contact</a></li>
              <li class="nav-item"><a href="#" class="nav-link">Privacy</a></li>
              <li class="nav-item"><a href="<?= route('user.sign_up') ?>" class="nav-link link-border register">Register</a></li>
              <li class="nav-item"><a href="<?= route('user.login') ?>" class="nav-link link-border">Login</a></li>
            </ul>
          </div>
        </div>
      </nav>
    </header>