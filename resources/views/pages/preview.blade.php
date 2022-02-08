<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="<?= config('CURRENT_LOCALE_DIRECTION') ?>">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
	<?= __yesset([
		'dist/css/public-assets-app*.css',
		'dist/css/custom*.css',
		'dist/css/login-register*.css'
	], true) ?>
</head>

<body id="page-top" class="lw-public-master lw-login-register-page">
	<!-- Page Wrapper -->
	<div id="wrapper" class="container">
		<!-- Content Wrapper -->
		<div id="content-wrapper" class="d-flex flex-column">
			<div id="content">
				<!-- Page Header -->
				<nav class="navbar navbar-expand navbar-light bg-dark topbar mb-4 static-top shadow lw-other-page-nav">
					<ul class="navbar-nav ml-0">
						<li>
							<a class="sidebar-brand d-flex align-items-center bg-dark" href="<?= url('/') ?>">
								<div class="sidebar-brand-icon">
									<img class="lw-logo-img" src="<?= getStoreSettings('small_logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
								</div>
								<img class="lw-logo-img d-sm-none d-none d-md-block" src="<?= getStoreSettings('logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
								<img class="lw-logo-img d-sm-block d-md-none" src="<?= getStoreSettings('small_logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
							</a>
						</li>
					</ul>
				</nav>
				<!-- /Page Header -->

				<!-- Begin Page Content -->
				<div class="lw-page-content lw-other-page-content">
					<section class="section">
						<div class="container">
							@if(isset($page) and !__isEmpty($page))
							<div class="row">
								<div class="content-area col-md-12 mx-auto">
									<div class="site-content" role="main">
										<article>
											<header>
												<h1><?= $page['title'] ?></h1>
											</header>
											<div class="lw-page-description">
												<?= $page['content'] ?>
											</div>
										</article>
									</div>
								</div>
							</div>
							@endif
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>

	<!-- include footer -->
	@include('includes.footer')
	<!-- /include footer -->
</body>