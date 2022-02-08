<!-- include header -->
@include('includes.header')
<!-- /include header -->
<style>
	.lw-login-register-page .lw-page-bg {
		background-image: url(<?= __yesset("imgs/home/random/*.jpg", false, [
									'random' => true
								]) ?>);
	}
</style>

<body class="bg-gradient-primary lw-login-register-page">
	<div class="lw-page-bg"></div>
	<div class="container">
		<!-- Outer Row -->
		<div class="row justify-content-center">
			<div class="col-lg-6 col-md-9">
				<div class="card o-hidden border-0 shadow-lg my-5">
					<div class="card-body p-5">
						<!-- Nested Row within Card Body -->
						<div class="row">
							<div class="col-lg-12">
								<div class="p-5">
									<!-- heading -->
									<div class="text-center">
										<img class="lw-logo-img" src="<?= getStoreSettings('logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
										<hr class="mt-4 mb-4">
										<h4 class="text-gray-200 mb-4"><?= __tr('Reset Your Password?') ?></h4>
										<p class="mb-4"><?= __tr("We get it, stuff happens. Just enter your email address below and we'll send you a link to reset your password!") ?></p>
									</div>
									<!-- / heading -->
									<!-- reset password form form -->
									<form class="user lw-ajax-form lw-form" method="post" action="<?= route('user.reset_password.process', ['reminderToken' => request()->get('reminderToken')]) ?>">
										<!-- email input field -->
										<div class="form-group">
											<input type="email" class="form-control form-control-user" name="email" aria-describedby="emailHelp" required placeholder="<?= __tr('Enter Email Address...') ?>">
										</div>
										<!-- / email input field -->

										<!-- new password input field -->
										<div class="form-group">
											<input type="password" class="form-control form-control-user" name="password" placeholder="<?= __tr('New Password') ?>" required minlength="6">
										</div>
										<!-- / new password input field -->

										<!-- new password confirmation input field -->
										<div class="form-group">
											<input type="password" class="form-control form-control-user" name="password_confirmation" placeholder="<?= __tr('New Password Confirmation') ?>" required minlength="6">
										</div>
										<!-- new password confirmation input field -->

										<!-- Reset Password button -->
										<button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block">
											<?= __tr('Reset Password') ?>
										</button>
										<!-- Reset Password button -->
									</form>
									<!-- reset password form form -->
									<hr>
									<!-- account and login page link -->
									<div class="text-center">
										<a class="small" href="<?= route('user.sign_up') ?>"><?= __tr('Create an Account!')  ?></a>
									</div>
									<div class="text-center">
										<a class="small" href="<?= route('user.login') ?>"><?= __tr('Already have an account? Login!')  ?></a>
									</div>
									<!-- / account and login page link -->
								</div>
							</div>
						</div>
						<!-- /Nested Row within Card Body -->
					</div>
				</div>
			</div>
		</div>
		<!-- /Outer Row -->
	</div>
</body>