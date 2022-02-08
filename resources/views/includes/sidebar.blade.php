<!-- Sidebar -->
<ul class="navbar-nav bg-dark sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center" href="<?= route('manage.dashboard') ?>">
        <div class="sidebar-brand-icon">
            <img src="<?= getStoreSettings('small_logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
        </div>
        <img class="lw-logo-img" src="<?= getStoreSettings('logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?= makeLinkActive('manage.dashboard') ?> mt-2">
        <a class="nav-link" href="<?= route('manage.dashboard') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span><?= __tr('Dashboard') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.abuse_report.read.list') ?>">
        <a class="nav-link" href="<?= route('manage.abuse_report.read.list', ['status' => 1]) ?>">
            <i class="fas fa-flag"></i>
            <span><?= __tr('Abuse Reports') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.kink.view') ?>">
        <a class="nav-link" href="<?= route('manage.kink.view') ?>">
            <i class="fas fa-icons"></i>
            <span><?= __tr('Interests/Kinks') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.page.view') ?>">
        <a class="nav-link" href="<?= route('manage.page.view') ?>">
            <i class="fas fa-file"></i>
            <span><?= __tr('Pages') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.plan.view') ?>">
        <a class="nav-link" href="<?= route('manage.plan.view') ?>">
            <i class="far fa-money-bill-alt"></i>
            <span><?= __tr('Subscription Plans') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.blockCountry.view') ?>">
        <a class="nav-link" href="<?= route('manage.blockCountry.view') ?>">
            <i class="fas fa-ban"></i>
            <span><?= __tr('Block Countries') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.user.view_list') ?>">
        <a class="nav-link" href="<?= route('manage.user.view_list', ['status' => 1]) ?>">
            <i class="fas fa-users"></i>
            <span><?= __tr('Users') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.configuration.read') ?>">
        <a class="nav-link" href="<?= route('manage.configuration.read', ['pageType' => 'general']) ?>">
            <i class="fas fa-cogs"></i>
            <span><?= __tr('Settings') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.forum-category.view') ?>">
        <a class="nav-link" href="<?= route('manage.forum-category.view') ?>">
            <i class="fas fa-list-alt"></i>
            <span><?= __tr('Forum Categories') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.forum-topic.view') ?>">
        <a class="nav-link" href="<?= route('manage.forum-topic.view') ?>">
            <i class="fas fa-tasks"></i>
            <span><?= __tr('Forum Topics') ?></span>
        </a>
    </li>
    <?php /* ## CUSTOM-CHANGE ##
    <li class="nav-item <?= makeLinkActive('manage.item.gift.view') ?>">
        <a class="nav-link" href="<?= route('manage.item.gift.view') ?>">
            <i class="fas fa fa-gift"></i>
            <span><?= __tr('Gifts') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.item.sticker.view') ?>">
        <a class="nav-link" href="<?= route('manage.item.sticker.view') ?>">
            <i class="fas fa fa-sticky-note"></i>
            <span><?= __tr('Stickers') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.credit_package.read.list') ?>">
        <a class="nav-link" href="<?= route('manage.credit_package.read.list') ?>">
            <i class="fas fa-box"></i>
            <span><?= __tr('Credit Packages') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.user.photos_list') ?>">
        <a class="nav-link" href="<?= route('manage.user.photos_list') ?>">
            <i class="fas fa-upload"></i>
            <span><?= __tr('User Uploads') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.financial_transaction.read.view_list') ?>">
        <a class="nav-link" href="<?= route('manage.financial_transaction.read.view_list', ['transactionType' => 'live']) ?>">
            <i class="fas fa-university"></i>
            <span><?= __tr('Financial Transactions') ?></span>
        </a>
    </li>    
    <li class="nav-item <?= makeLinkActive('manage.translations.languages') ?>">
        <a class="nav-link" href="<?= route('manage.translations.languages') ?>">
            <i class="fas fa-language"></i>
            <span><?= __tr('Languages') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('manage.fake_users.read.generator_options') ?>">
        <a class="nav-link" href="<?= route('manage.fake_users.read.generator_options') ?>">
            <i class="fas fa-user-plus"></i>
            <span><?= __tr('Generate Fake Users') ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" title="<?= __tr("If you have made changes which doesn't reflecting this link may help to clear all the cache.") ?>" href="<?= route('manage.configuration.clear_cache', []) . '?redirectTo=' . base64_encode(Request::fullUrl()); ?>">
            <i class="fas fa-broom"></i>
            <span><?= __tr('Clear System Cache') ?></span>
        </a>
    </li>
    <li class="nav-item <?= Request::fullUrl() == route('manage.configuration.read', ['pageType' => 'licence-information']) ? 'active' : '' ?>">
        <a class="nav-link"  href="<?= route('manage.configuration.read', ['pageType' => 'licence-information']) ?>">
            <i class="fas fa-certificate"></i>
            <span><?= __tr('Licence') ?></span>
        </a>
    </li> */ ?>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->