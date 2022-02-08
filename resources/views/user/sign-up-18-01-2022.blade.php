<?php $pageTitle = __tr('Register - Kinky Near Me'); ?>
@section('page-title', $pageTitle)
@section('head-title', $pageTitle)
@section('keywordName', strip_tags(__tr('Create an Account!')))
@section('keyword', strip_tags(__tr('Create an Account!')))
@section('description', strip_tags(__tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.')))
@section('keywordDescription', strip_tags(__tr('Create an Account!')))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- include header -->
@include('includes.header')
<!-- /include header -->

<section class="login-section register-section">
    <div class="form-login form-register fadeInDown">
    <div class="form-title text-center">
        <h2 class="heading-title"><?= __tr('FULFILL YOUR FETISH FANTASY') ?></h2>
        <p class="title-para"><?= __tr('Register with us and find your kinky partner') ?></p>
    </div>
    <div id="formContent">
        <form class="form-field-fontent user lw-ajax-form lw-form" method="POST" action="<?= route('user.sign_up.process') ?>" data-show-processing="true" data-secured="true" data-unsecured-fields="first_name,last_name">
            <div class="form-row">
                <!-- First Name -->
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    <label for="inputAddress"><?= __tr('first name') ?></label>
                    <input type="text" class="form-control form-control-user" name="first_name" placeholder="<?= __tr('Ex. John') ?>" required minlength="3">
                </div>
                <!-- /First Name -->
            </div>
            <div class="form-row">
                <!-- Last Name -->
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    <label for="inputAddress"><?= __tr('last name') ?></label>
                    <input type="text" class="form-control form-control-user" name="last_name" placeholder="<?= __tr('Ex. Smith') ?>" required minlength="3">
                </div>
                <!-- /Last Name -->
            </div>
            <div class="form-row">
                <!-- Email Address -->
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    <label for="inputAddress"><?= __tr('Email address') ?></label>
                    <input type="email" class="form-control form-control-user" name="email" placeholder="<?= __tr('Ex. johnsmith@gmail.com') ?>" required>
                </div>
                <!-- /Email Address -->
            </div>
            <div class="form-row">
                <!-- Mobile Number -->
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    <label for="inputAddress"><?= __tr('Username') ?></label>
                    <input type="text" class="form-control form-control-user" name="username" placeholder="<?= __tr('Ex. john') ?>" required minlength="5">
                </div>
                <!-- /Mobile Number -->
            </div>
            <div class="form-row">
                <!-- Password -->
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    <label for="inputPassword4"><?= __tr('Password') ?></label>
                    <input type="password" class="form-control form-control-user" name="password" placeholder="<?= __tr('******') ?>" required minlength="6">
                </div>
                <!-- /Password -->
            </div>
            <div class="form-row">
                <!-- Confirm Password -->
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    <label for="inputPassword4"><?= __tr('confirm Password') ?></label>
                    <input type="password" class="form-control form-control-user" name="repeat_password" placeholder="<?= __tr('******') ?>" required minlength="6">
                </div>
                <!-- /Confirm Password -->
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="g-recaptcha" data-sitekey="6LeVDOUdAAAAACBoEYeF6tON_xIlSx2gBEgyex2U"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    <div class="form-check">
                        <input type="hidden" name="accepted_terms">
                        <input type="checkbox" class="form-check-input" id="acceptTerms" name="accepted_terms" value="1" required>
                        <label class="form-check-label" for="acceptTerms">
                            <?= __tr('I agree to the ') ?>
                            <a target="_blank" href="<?= getStoreSettings('terms_and_conditions_url') ?>">
                                <?= __tr('Terms & Conditions') ?></a>
                        </label>
                    </div>
                </div>
            </div>

            <div class="login-button">
                <!-- Register Account Button -->
                <a href class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block">
                    <?= __tr('Register') ?>
                </a>
                <!-- /Register Account Button -->
            </div>
        </form>


        <!-- Remind Passowrd -->
        <div id="formFooter">
            <span>Already a member?</span> <a class="underlineHover" href="<?= route('user.login') ?>"><?= __tr('Login now') ?></a>
        </div>
    </div>
</section>

@push('appScripts')
    <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
   
    <!-- <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
    async defer>
</script>
    <script type="text/javascript">
  var onloadCallback = function() {
    alert("grecaptcha is ready!");
  };
</script> -->


@endpush

<!-- include footer -->
@include('includes.footer')
<!-- /include footer -->


</html>