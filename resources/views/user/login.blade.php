@section('page-title', __tr('Login'))
@section('head-title', __tr('Login - Kinky Near Me'))
@section('keywordName', strip_tags(__tr('Login')))
@section('keyword', strip_tags(__tr('Login')))
@section('description', strip_tags(__tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.')))
@section('keywordDescription', strip_tags(__tr('Login')))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- include header -->
@include('includes.header')
<!-- /include header -->



<section class="login-section">
    <div class="form-login fadeInDown">
    <div class="form-title text-center">
        <h2 class="heading-title"><?= __tr('LOGIN')  ?></h2>
        <p class="title-para"><?= __tr('Create an account to find your kinky partner')  ?></p>

        @if(session('errorStatus'))
        <!--  success message when email sent  -->
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= session('message') ?>
        </div>
        <!--  /success message when email sent  -->
        @endif

        @if(session('successStatus'))
        <!--  success message when email sent  -->
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= session('message') ?>
        </div>
        <!--  /success message when email sent  -->
        @endif
    </div>
    <div id="formContent">

        <!-- login form -->
        <form class="form-field-fontent user lw-ajax-form lw-form" data-callback="onLoginCallback" method="post" action="<?= route('user.login.process') ?>" data-show-processing="true" data-secured="true">
            <!-- email input field -->
            <div class="form-group">
                <label for="inputAddress"><?= __tr('Email or Username')  ?></label>
                <input type="text" class="second form-control-user" name="email_or_username" aria-describedby="emailHelp" placeholder="<?= __tr('Ex. johnsmith@gmail.com or johnsmith') ?>" required>
            </div>
            <!-- / email input field -->

            <!-- password input field -->
            <div class="form-group">
                <label for="inputAddress"><?= __tr('Password')  ?></label>
                <input type="password" class="third form-control-user" name="password" placeholder="<?= __tr('******') ?>" required minlength="6">
            </div>
            <!-- password input field -->

            <!-- remember me input field -->
            <div class="form-group" style="display: none;">
                <div class="custom-control custom-checkbox small">
                    <input type="checkbox" class="custom-control-input" id="rememberMeCheckbox" name="remember_me">
                    <label class="custom-control-label text-gray-200" for="rememberMeCheckbox"><?= __tr('Remember Me')  ?></label>
                </div>
            </div>
            <!-- remember me input field -->

            <!-- login button -->
            <div class="login-button">
                <button type="submit" value="Login" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block"><?= __tr('Login')  ?></button>
            </div>
            <!-- / login button -->

            <!-- forgot password button -->
            <div class="forgot-pass">
                <a class="btn primary-button" href="<?= route('user.forgot_password') ?>"><?= __tr('Forgot Password?')  ?></a>
            </div>
            <!-- / forgot password button -->
        </form>
        <!-- / login form -->
                                    
        <div id="formFooter">
            <span><?= __tr('Not a member yet?')  ?></span> <a class="underlineHover" href="<?= route('user.sign_up') ?>"><?= __tr('Register now')  ?></a>
        </div>
    </div>
</section>
@push('appScripts')
<script>
    //on login success callback
    function onLoginCallback(response) {
        //check reaction code is 1 and intended url is not empty
        if (response.reaction == 1 && !_.isEmpty(response.data.intendedUrl)) {
            //redirect to intendedUrl location
            _.defer(function() {
                window.location.href = response.data.intendedUrl;
            })
        }
    }
</script>
@endpush
<!-- include footer -->
@include('includes.footer')
<!-- /include footer -->