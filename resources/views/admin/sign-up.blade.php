<?php $pageTitle = __tr('Create an Account'); ?>
@section('page-title', $pageTitle)
@section('head-title', $pageTitle)
@section('keywordName', strip_tags(__tr('Create an Account!')))
@section('keyword', strip_tags(__tr('Create an Account!')))
@section('description', strip_tags(__tr('Create an Account!')))
@section('keywordDescription', strip_tags(__tr('Create an Account!')))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- include header -->
@include('includes.header')
<!-- /include header -->

<body class="lw-login-register-page">
    <div class="lw-page-bg lw-lazy-img" data-src="<?= __yesset("imgs/home/random/*.jpg", false, [
                                                        'random' => true
                                                    ]) ?>"></div>
    <!-- container start -->
    <div class="container">
        <div class="row justify-content-center">
            <!-- card -->
            <div class="card o-hidden border-0 shadow-lg my-5 col-lg-6">
                <!-- card body -->
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <!-- /background image -->
                        <div class="col-lg-12 col-md-9">
                            <div class="p-5">
                                <!-- page heading -->
                                <div class="text-center">
                                    <a href="<?= url(''); ?>">
                                        <img class="lw-logo-img" src="<?= getStoreSettings('logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
                                    </a>
                                    <hr class="mt-4 mb-4">
                                    <h4 class="text-gray-200 mb-4"><?= __tr('Create an Account!') ?></h4>
                                </div>
                                <!-- /page heading -->
                                <form class="user lw-ajax-form lw-form" method="post" action="<?= route('user.sign_up.process') ?>" data-show-processing="true" data-secured="true" data-unsecured-fields="first_name,last_name">
                                    <div class="form-group row">
                                        <!-- First Name -->
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="text" class="form-control form-control-user" name="first_name" placeholder="<?= __tr('First Name') ?>" required minlength="3">
                                        </div>
                                        <!-- /First Name -->

                                        <!-- Last Name -->
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control form-control-user" name="last_name" placeholder="<?= __tr('Last Name') ?>" required minlength="3">
                                        </div>
                                        <!-- /Last Name -->
                                    </div>
                                    <div class="form-group row">
                                        <!-- Username -->
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="text" class="form-control form-control-user" name="username" placeholder="<?= __tr('Username') ?>" required minlength="5">
                                        </div>
                                        <!-- /Username -->

                                        <!-- Mobile Number -->
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control form-control-user" name="mobile_number" placeholder="<?= __tr('Mobile No') ?>" required>
                                        </div>
                                        <!-- /Mobile Number -->
                                    </div>
                                    <!-- Email Address -->
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user" name="email" placeholder="<?= __tr('Email Address') ?>" required>
                                    </div>
                                    <!-- /Email Address -->
                                    <div class="form-group row">
                                        <!-- Gender -->
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <select name="gender" class="form-control form-control-user lw-user-gender-select-box" id="select_gender" required>
                                                <option value="" selected disabled><?= __tr('Choose your gender') ?></option>
                                                @foreach($genders as $genderKey => $gender)
                                                <option value="<?= $genderKey ?>"><?= $gender ?></option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- /Gender -->

                                        <!-- Confirm Password -->
                                        <div class="col-sm-6">
                                            <input type="date" min="{{ \Carbon\Carbon::now()->subYears(configItem('age_restriction.maximum'))->format('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->subYears(configItem('age_restriction.minimum'))->format('Y-m-d') }}" class="form-control form-control-user" name="dob" placeholder="<?= __tr('YYYY-MM-DD') ?>" required="true">
                                        </div>
                                        <!-- /Confirm Password -->
                                    </div>

                                    <div class="form-group row">
                                        <!-- Password -->
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="password" class="form-control form-control-user" name="password" placeholder="<?= __tr('Password') ?>" required minlength="6">
                                        </div>
                                        <!-- /Password -->

                                        <!-- Confirm Password -->
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control form-control-user" name="repeat_password" placeholder="<?= __tr('Repeat Password') ?>" required minlength="6">
                                        </div>
                                        <!-- /Confirm Password -->
                                    </div>

                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="hidden" name="accepted_terms">
                                            <input type="checkbox" class="form-check-input" id="acceptTerms" name="accepted_terms" value="1" required>
                                            <label class="form-check-label" for="acceptTerms">
                                                <?= __tr('I accept all ') ?>
                                                <a target="_blank" href="<?= getStoreSettings('terms_and_conditions_url') ?>">
                                                    <?= __tr('terms and conditions') ?></a>
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <!-- Register Account Button -->
                                        <a href class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block">
                                            <?= __tr('Register Account') ?>
                                        </a>
                                        <!-- /Register Account Button -->
                                    </div>
                                    <hr>
                                    <!-- Register with Google Button -->
                                    @if(getStoreSettings('allow_google_login'))
                                    <a href="<?= route('social.user.login', [getSocialProviderKey('google')]) ?>" class="btn btn-google btn-user btn-block">
                                        <i class="fab fa-google fa-fw"></i> <?= __tr('Register with Google') ?>
                                    </a>
                                    @endif
                                    <!-- /Register with Google Button -->

                                    <!-- Register with Facebook Button -->
                                    @if(getStoreSettings('allow_facebook_login'))
                                    <a href="<?= route('social.user.login', [getSocialProviderKey('facebook')]) ?>" class="btn btn-facebook btn-user btn-block">
                                        <i class="fab fa-facebook-f fa-fw"></i> <?= __tr('Register with Facebook') ?>
                                    </a>
                                    @endif
                                    <!-- /Register with Facebook Button -->
                                </form>
                                @if(getStoreSettings('allow_google_login') || getStoreSettings('allow_facebook_login'))
                                <hr>
                                @endif
                                <div class="text-center">
                                    <!-- Forgot Password Link -->
                                    <a class="small" href="<?= route('user.forgot_password') ?>"><?= __tr('Forgot Password?') ?></a>
                                    <!-- /Forgot Password Link -->
                                </div>
                                <div class="text-center">
                                    <!-- Login Link -->
                                    <a class="small" href="<?= route('user.login') ?>"><?= __tr('Already have an account? Login!') ?></a>
                                    <!-- /Login Link -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Nested Row within Card Body -->
                </div>
                <!-- /card body -->
            </div>
            <!-- /card -->
        </div>
    </div>
    <!-- /container end -->
</body>
<!-- include footer -->
@include('includes.footer')
<!-- /include footer -->

</html>