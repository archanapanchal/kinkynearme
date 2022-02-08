<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="<?= config('CURRENT_LOCALE_DIRECTION') ?>" @if(isAdmin()) class="lw-light-theme" @endif>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1.0, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<title>@yield('head-title') : <?= getStoreSettings('name') ?></title>
	<!-- Custom fonts for this template-->
	<link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet">
	<link rel="shortcut icon" href="<?= getStoreSettings('favicon_image_url') ?>" type="image/x-icon">
	<link rel="icon" href="<?= getStoreSettings('favicon_image_url') ?>" type="image/x-icon">

	<!-- Primary Meta Tags -->
	<meta name="title" content="@yield('page-title')">
	<meta name="description" content="@yield('description')">
	<meta name="keywordDescription" property="og:keywordDescription" content="@yield('keywordDescription')">
	<meta name="keywordName" property="og:keywordName" content="@yield('keywordName')">
	<meta name="keyword" content="@yield('keyword')">
	<!-- Google Meta -->
	<meta itemprop="name" content="@yield('page-title')">
	<meta itemprop="description" content="@yield('description')">
	<meta itemprop="image" content="@yield('page-image')">
	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="website">
	<meta property="og:url" content="@yield('page-url')">
	<meta property="og:title" content="@yield('page-title')">
	<meta property="og:description" content="@yield('description')">
	<meta property="og:image" content="@yield('page-image')">
	<!-- Twitter -->
	<meta property="twitter:card" content="@yield('twitter-card-image')">
	<meta property="twitter:url" content="@yield('page-url')">
	<meta property="twitter:title" content="@yield('page-title')">
	<meta property="twitter:description" content="@yield('description')">
	<meta property="twitter:image" content="@yield('page-image')">

	<!-- Custom styles for this template-->
	@if(isAdmin())
	<?= __yesset([
		'dist/css/public-assets-app*.css',
		'dist/fa/css/all.min.css',
		"dist/css/vendorlibs-datatable.css",
		"dist/css/vendorlibs-photoswipe.css",
		"dist/css/vendorlibs-smartwizard.css",
		'dist/css/custom*.css',
		'dist/css/messenger*.css',
		'dist/css/login-register*.css'
	], true) ?>
	@else
	<?= __yesset([
		'dist/css/bootstrap.css',
	    'dist/fa/css/all.min.css',
	    "dist/css/vendorlibs-photoswipe.css",
		"dist/css/vendorlibs-smartwizard.css",
		'dist/css/noty.css',
	    'dist/css/styles.css',
	    'dist/css/responsive.css',
	    'dist/css/messenger*.css',
	    'dist/css/animate.css',
	], true) ?>
	@endif
@if(getApplicableTheme() == 'light')
<?= __yesset([
		'dist/css/light-theme*.css',
	], true) ?>
@endif
	@stack('header')
</head>

@if(!isAdmin() && (!isset($isadmin)))
<body id="page-top">
	<div class="wrapper">
		<header class="header @auth header-2 @endif">
		    <nav class="navbar navbar-expand-lg">
		        <div class="container">
		            <a href="<?= route('landing_page') ?>" class="navbar-brand">
		            <img class="lw-logo-img" src="<?= getStoreSettings('logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
		            </a>

		            @auth
		            	<div class="search-box middle-bar-header">
			                <div class="input-group">
			                    <div class="form-outline">
			                        <input type="search" id="form1" class="form-control" placeholder="Search by name, interest/kinks" />
			                    </div>
			                    <button type="button" class="btn btn-primary">search</button>
			                </div>
			                <p class="search-content">Ex. Blindfold, Elvira, john, BDSM</p>
			            </div>
			            <div class="right-side-bar-header">
			                <div class="notification-bar"><img src="<?= url('dist/images/notification-icon.svg') ?>"><span class="count-num">0</span></div>
			                <div class="admin-bar">
			                	<img class="lw-profile-thumbnail lw-lazy-img" data-src="<?= getUserAuthInfo('profile.profile_picture_url') ?>">
			                    <div class="form-field">
		                        	<div class="select-input">
		                            	<div class="dropdown">
		                            		<button type="button" class="dropdown-toggle" data-toggle="dropdown"><?= getUserAuthInfo('profile.full_name') ?></button>
	                            			<div class="dropdown-menu">
		                                		<a class="dropdown-item" href="<?= route('landing_page') ?>"><?= __tr('Account') ?></a>
		                                		<a class="dropdown-item" href="<?= route('user.logout') ?>"><?= __tr('Logout') ?></a>
		                               		</div>
		                             	</div>
		                           	</div>
		                        </div>
			                </div>
			            </div>
		            @else
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
		            @endif
		            
		        </div>
		    </nav>
		</header>

		            

@endif