<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <li>
        <a class="sidebar-brand d-flex align-items-center bg-dark" href="<?= url('/home') ?>">
            <div class="sidebar-brand-icon">
                <img class="lw-logo-img" src="<?= getStoreSettings('small_logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
            </div>
            <img class="lw-logo-img d-sm-none d-none d-md-block" src="<?= getStoreSettings('logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
            <img class="lw-logo-img d-sm-block d-md-none" src="<?= getStoreSettings('small_logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
        </a>
    </li>
    <li class="nav-item mt-2 d-sm-block d-md-none">
        <a href class="nav-link" onclick="getChatMessenger('<?= route('user.read.all_conversation') ?>', true)" id="lwAllMessageChatButton" data-chat-loaded="false" data-toggle="modal" data-target="#messengerDialog">
            <i class="far fa-comments"></i>
            <span><?= __tr('Messenger') ?></span>
        </a>
    </li>
    <!-- Notification Link -->
    <li class="nav-item dropdown no-arrow mx-1 d-sm-block d-md-none">
        <a class="nav-link dropdown-toggle lw-ajax-link-action" href="<?= route('user.notification.write.read_all_notification') ?>" data-callback="onReadAllNotificationCallback" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-method="post">
            <i class="fas fa-bell fa-fw"></i>
            <span><?= __tr('Notification') ?></span>
            <span class="badge badge-danger badge-counter" id="lwNotificationCount"><?= getNotificationList()['notificationCount'] ?></span>
        </a>
        <!-- Dropdown - Alerts -->
        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
            <h6 class="dropdown-header">
                <?= __tr('Notification') ?>
            </h6>
            <!-- Notification block -->
            @if(!__isEmpty(getNotificationList()['notificationData']))
            <span id="lwNotificationList">
                @foreach(getNotificationList()['notificationData'] as $notification)

                <!-- show all notification list -->
                <a class="dropdown-item d-flex align-items-center" href="<?= $notification['actionUrl'] ?>">
                    <div>
                        <div class="small text-gray-500"><?= $notification['created_at'] ?></div>
                        <span class="font-weight-bold"><?= $notification['message'] ?></span>
                    </div>
                </a>
                <!-- show all notification list -->
                @endforeach
            </span>
            <!-- show all notification link -->
            <a class="dropdown-item text-center small text-gray-500" href="<?= route('user.notification.read.view') ?>" id="lwShowAllNotifyLink"><?= __tr('Show All Notifications.') ?></a>
            <!-- /show all notification link -->
            @else
            <!-- info message -->
            <a class="dropdown-item text-center small text-gray-500"><?= __tr('There are no notification.') ?></a>
            <!-- /info message -->
            @endif
            <!-- /Notification block -->
        </div>
    </li>
    <!-- /Notification Link -->

    <!-- Nav Item - Messages -->
    <li class="nav-item d-sm-block d-md-none">
        <a class="nav-link" href="<?= route('user.credit_wallet.read.view') ?>">
            <i class="fas fa-coins fa-fw mr-2"></i>
            <span><?= __tr('Credit Wallet') ?></span>
            <span class="badge badge-success badge-counter"><?= totalUserCredits() ?></span>
        </a>
    </li>

    <!-- Nav Item - Messages -->
    <li class="nav-item d-sm-block d-md-none">
        <a class="nav-link" href data-toggle="modal" data-target="#boosterModal">
            <i class="fas fa-bolt fa-fw mr-2"></i>
            <span><?= __tr('Profile Booster') ?></span>
            <span id="lwBoosterTimerCountDownOnSB"></span>
        </a>
    </li>
    @if(isset($is_profile_page) and ($is_profile_page === true))
    @if(!$isBlockUser and !$blockByMeUser)
    @stack('sidebarProfilePage')
    @endif
    @endif
    <hr class="sidebar-divider mt-2 mb-2 d-sm-block d-md-none">
    <!-- Heading -->
    <li class="mt-2 nav-item <?= makeLinkActive('home_page') ?>">
        <a class="nav-link" href="<?= route('home_page') ?>">
            <i class="fas fa-home"></i>
            <span><?= __tr('Home') ?></span>
        </a>
    </li>

    <li class="nav-item <?= makeLinkActive('user.read.find_matches') ?>">
        <a class="nav-link" href="<?= route('user.read.find_matches') ?>">
            <i class="fas fa-search"></i>
            <span><?= __tr('Find Matches') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('user.profile_view') ?>">
        <a class="nav-link" href="<?= route('user.profile_view', ['username' => getUserAuthInfo('profile.username')]) ?>">
            <i class="fas fa-user"></i>
            <span><?= __tr('My Profile') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('user.photos_setting') ?>">
        <a class="nav-link" href="<?= route('user.photos_setting', ['username' => getUserAuthInfo('profile.username')]) ?>">
            <i class="far fa-images"></i>
            <span><?= __tr('My Photos') ?></span>
        </a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider mt-2 mb-2">
    <li class="nav-item <?= makeLinkActive('user.who_liked_me_view') ?>">
        <a class="nav-link" href="<?= route('user.who_liked_me_view') ?>">
            <i class="fa fa-thumbs-up" aria-hidden="true"></i>
            <span><?= __tr('Who likes me') ?>
                <?php
                $featurePlans = getStoreSettings('feature_plans');
                $showLike = $featurePlans['show_like']['select_user'];
                ?>
                @if($showLike == 2)
                <span class="lw-premium-feature-badge"></span></span>
            @endif
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('user.mutual_like_view') ?>">
        <a class="nav-link" href="<?= route('user.mutual_like_view') ?>">
            <i class="fa fa-users"></i>
            <span><?= __tr('Mutual Likes') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('user.my_liked_view') ?>">
        <a class="nav-link" href="<?= route('user.my_liked_view') ?>">
            <i class="fas fa-fw fa-heart"></i>
            <span><?= __tr('My Likes') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('user.my_disliked_view') ?>">
        <a class="nav-link" href="<?= route('user.my_disliked_view') ?>">
            <i class="fas fa-fw fa-heart-broken"></i>
            <span><?= __tr('My Dislikes') ?></span>
        </a>
    </li>
    <li class="nav-item  <?= makeLinkActive('user.profile_visitors_view') ?>">
        <a class="nav-link" href="<?= route('user.profile_visitors_view') ?>">
            <i class="fa fa-user" aria-hidden="true"></i>
            <span><?= __tr('Visitors') ?></span>
        </a>
    </li>
    <li class="nav-item  <?= makeLinkActive('user.notification.read.view') ?>">
        <a class="nav-link" href="<?= route('user.notification.read.view') ?>">
            <i class="fa fa-bell" aria-hidden="true"></i>
            <span><?= __tr('Notifications') ?></span>
        </a>
    </li>
    <li class="nav-item <?= makeLinkActive('user.read.block_user_list') ?>">
        <a class="nav-link" href="<?= route('user.read.block_user_list') ?>">
            <i class="fas fa-ban"></i>
            <span><?= __tr('Blocked Users') ?></span>
        </a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <!-- Featured Users -->
    @if(!__isEmpty(getFeatureUserList()))
    <div class="card">
        <div class="card-header">
            <?= __tr('Featured Users') ?>
        </div>
        <div class="card-body lw-featured-users">
            @foreach(getFeatureUserList() as $users)
            <a href="<?= route('user.profile_view', ['username' => $users['username']]) ?>">
                <img class="img-fluid img-thumbnail lw-sidebar-thumbnail lw-lazy-img" data-src="<?= $users['userImageUrl'] ?>">
            </a>
            @endforeach
        </div>
    </div>
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    @endif
    <!-- /Featured Users -->

    <!-- sidebar advertisement -->
    @if(!getFeatureSettings('no_adds') and getStoreSettings('user_sidebar_advertisement')['status'] == 'true')
    <li class="nav-item lw-sidebar-ads-container d-none d-md-block">
        <!-- sidebar advertisement content -->
        <div>
            <?= getStoreSettings('user_sidebar_advertisement')['content'] ?>
        </div>
        <!-- /sidebar advertisement content -->
    </li>
    <!-- sidebar advertisement -->
    @endif
    <!-- Sidebar Toggler (Sidebar) -->
</ul>
<!-- End of Sidebar -->