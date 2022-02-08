@section('page-title', __tr('Forgot Your Password?'))
@section('head-title', __tr('Forgot Your Password?'))
@section('keywordName', strip_tags(__tr('Forgot Your Password?')))
@section('keyword', strip_tags(__tr('Forgot Your Password?')))
@section('description', strip_tags(__tr('Forgot Your Password?')))
@section('keywordDescription', strip_tags(__tr('Forgot Your Password?')))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- include header -->
@include('includes.header')
<!-- /include header -->


<section class="login-section">
    <div class="form-login fadeInDown">
    <div class="form-title text-center">
        <h2 class="heading-title"><?= __tr('Forgot Your Password?')  ?></h2>
        <p class="title-para"><?= __tr("We get it, stuff happens. Just enter your email address below and we'll send you a link to reset your password!")  ?></p>
    </div>
    <div id="formContent">
		<!-- forgot password form form -->
		<form class="form-field-fontent user lw-ajax-form lw-form" method="post" action="<?= route('user.forgot_password.process') ?>">
			<!-- email input field -->
			<div class="form-group">
				<label for="inputAddress"><?= __tr('Email address')  ?></label>
				<input type="email" class="form-control form-control-user" name="email" aria-describedby="emailHelp" placeholder="<?= __tr('Ex. johnsmith@gmail.com') ?>" required>
			</div>
			<!-- / email input field -->

			<!-- Reset Password button -->
			<div class="login-button">
				<button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block">
					<?= __tr('Reset Password')  ?>
				</button>
			</div>
			<!-- Reset Password button -->
		</form>
								
		<div id="formFooter">
            <a class="underlineHover" href="<?= route('user.login') ?>"><?= __tr('Login now')  ?></a>
        </div>
	</div>
</section>
@include('includes.footer')