@section('page-title', __tr("Settings"))
@section('head-title', __tr("Settings"))
@section('keywordName', strip_tags(__tr("Settings")))
@section('keyword', strip_tags(__tr("Settings")))
@section('description', strip_tags(__tr("Settings")))
@section('keywordDescription', strip_tags(__tr("Settings")))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-200"><?= __tr('Settings') ?></h1>
</div>
<!-- Page Heading -->
<?php $pageType = request()->pageType ?>
<div class="row">
    <div class="col-lg-3">
        <!-- List group start here -->
        <div class="list-group">
            <!-- General Settings -->
            <a class="list-group-item list-group-item-action <?= $pageType == 'general' ? 'active' : '' ?>" href="<?= route('manage.configuration.read', ['pageType' => 'general']) ?>"><?= __tr('General') ?></a>
            <!-- /General Settings -->

            
            <!-- User Settings -->
            <a class="list-group-item list-group-item-action <?= $pageType == 'user' ? 'active' : '' ?>" href="<?= route('manage.configuration.read', ['pageType' => 'user']) ?>"><?= __tr('Users') ?></a>
            <!-- /User Settings -->

            <?php /* ## CUSTOM-CHANGE ##
            <!-- Currency & Credit Packages -->
            <a class="list-group-item list-group-item-action <?= $pageType == 'currency' ? 'active' : '' ?>" href="<?= route('manage.configuration.read', ['pageType' => 'currency']) ?>"><?= __tr('Currency') ?></a>
            <!-- /Currency & Credit Packages -->
            
            <!-- Payment Settings -->
            <a class="list-group-item list-group-item-action <?= $pageType == 'payment' ? 'active' : '' ?>" href="<?= route('manage.configuration.read', ['pageType' => 'payment']) ?>"><?= __tr('Payment Gateways') ?></a>
            <!-- /Payment Settings -->

            <!-- Social Login Settings -->
            <a class="list-group-item list-group-item-action <?= $pageType == 'social-login' ? 'active' : '' ?>" href="<?= route('manage.configuration.read', ['pageType' => 'social-login']) ?>"><?= __tr('Social Logins') ?></a>
            <!-- /Social Login Settings -->

            <!-- Integration Settings -->
            <a class="list-group-item list-group-item-action <?= $pageType == 'integration' ? 'active' : '' ?>" href="<?= route('manage.configuration.read', ['pageType' => 'integration']) ?>"><?= __tr('Integrations') ?></a>
            <!-- /Integration Settings -->

            <!-- Premium Plans Settings -->
            <a class="list-group-item list-group-item-action <?= $pageType == 'premium-plans' ? 'active' : '' ?>" href="<?= route('manage.configuration.read', ['pageType' => 'premium-plans']) ?>"><?= __tr('Premium Plans') ?></a>
            <!-- /Premium Plans Settings -->

            <!-- Premium Features Settings -->
            <a class="list-group-item list-group-item-action <?= $pageType == 'premium-feature' ? 'active' : '' ?>" href="<?= route('manage.configuration.read', ['pageType' => 'premium-feature']) ?>"><?= __tr('Features') ?></a>
            <!-- /Premium Features Settings -->

            <!-- Email Settings -->
            <a class="list-group-item list-group-item-action <?= $pageType == 'email' ? 'active' : '' ?>" href="<?= route('manage.configuration.read', ['pageType' => 'email']) ?>"><?= __tr('Email') ?></a>
            <!-- /Email Settings -->

            <!-- Booster Settings -->
            <a class="list-group-item list-group-item-action <?= $pageType == 'booster' ? 'active' : '' ?>" href="<?= route('manage.configuration.read', ['pageType' => 'booster']) ?>"><?= __tr('Booster') ?></a>
            <!-- /Booster Settings -->

            <!-- Advertisement Settings -->
            <a class="list-group-item list-group-item-action <?= $pageType == 'advertisement' ? 'active' : '' ?>" href="<?= route('manage.configuration.read', ['pageType' => 'advertisement']) ?>"><?= __tr('Advertisement') ?></a>
            <!-- /Advertisement Settings --> */ ?>
        </div>
        <!-- /List group end here -->
    </div>
    <div class="col-lg-9">
        <!-- card start -->
        <div class="card">
            <!-- card body -->
            <div class="card-body">
                <!-- include related view -->
                @include('configuration.'. $pageType)
                <!-- /include related view -->
            </div>
            <!-- /card body -->
        </div>
        <!-- card start -->
    </div>
</div>