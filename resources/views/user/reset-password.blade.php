<!-- include header -->
@include('includes.header')
<!-- /include header -->

<section class="login-section">
    <div class="form-login fadeInDown">
    <div class="form-title text-center">
        <h2 class="heading-title"><?= __tr('Reset Your Password?') ?></h2>
        <p class="title-para"><?= __tr("We get it, stuff happens. Just enter your email address below and we'll send you a link to reset your password!") ?></p>
    </div>
    <div id="formContent">

		<!-- reset password form form -->
		<form class="form-field-fontent user lw-ajax-form lw-form" method="post" action="<?= route('user.reset_password.process', ['reminderToken' => request()->get('reminderToken')]) ?>">
			{{ csrf_field() }}
			<!-- email input field -->
			<div class="form-group">
				<label for="inputAddress"><?= __tr('Email Address')  ?></label>
				<input type="email" class="form-control form-control-user" name="email" aria-describedby="emailHelp" required placeholder="<?= __tr('Ex. johnsmith@gmail.com') ?>" required>
			</div>
			<!-- / email input field -->

			<!-- new password input field -->
			<div class="form-group">
				<label for="inputAddress"><?= __tr('New Password')  ?></label>
				<input type="password" class="form-control form-control-user" name="password" placeholder="<?= __tr('******') ?>" required minlength="6">
			</div>
			<!-- / new password input field -->

			<!-- new password confirmation input field -->
			<div class="form-group">
				<label for="inputAddress"><?= __tr('confirm Password')  ?></label>
				<input type="password" class="form-control form-control-user" name="password_confirmation" placeholder="<?= __tr('******') ?>" required minlength="6">
			</div>
			<!-- new password confirmation input field -->

			<!-- Reset Password button -->
			<div class="login-button">
                <!-- Register Account Button -->
                <button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block">
					<?= __tr('Reset Password') ?>
				</button>
                <!-- /Register Account Button -->
            </div>
			<!-- Reset Password button -->
		</form>

								
		<div id="formFooter">
            <a class="underlineHover" href="<?= route('user.login') ?>"><?= __tr('Login now')  ?></a>
        </div>
	</div>
</section>
<?php  exit;?>
@include('includes.footer')