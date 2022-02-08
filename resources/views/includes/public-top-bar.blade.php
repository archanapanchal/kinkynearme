<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-dark topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle (Topbar) -->
    <button type="button" id="sidebarToggleTop" class="btn btn-link d-block d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <ul class="navbar-nav ml-0">
        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-sm"></i>&nbsp;<span class="d-md-block d-none"><?= __tr('Find Matches') ?></span>
            </a>

            <?php
            $lookingFor = getUserSettings('looking_for');
            $minAge = getUserSettings('min_age');
            $maxAge = getUserSettings('max_age');

            if (request()->session()->has('userSearchData')) {
                $userSearchData = session('userSearchData');
                $lookingFor = $userSearchData['looking_for'];
                $minAge = $userSearchData['min_age'];
                $maxAge = $userSearchData['max_age'];
            }
            ?>

            <!-- Dropdown - Messages -->
            <div class="dropdown-menu p-3 shadow animated--grow-in lw-basic-filter-container" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto" data-show-processing="true" action="<?= route('user.read.find_matches') ?>">
                    <!-- Looking For -->
                    <div class="lw-looking-for-container lw-basic-filter-field">
                        <label for="looking_for"><?= __tr('Looking For') ?></label>
                        <select name="looking_for" class="form-control" id="looking_for">
                            <option value="all"><?= __tr('All') ?></option>
                            @foreach(configItem('user_settings.gender') as $genderKey => $gender)
                            <option value="<?= $genderKey ?>" <?= (request()->looking_for == $genderKey or $genderKey == $lookingFor) ? 'selected' : '' ?>><?= $gender ?></option>
                            @endforeach
                        </select>
                    </div>
                    <!-- /Looking For -->
                    <!-- Age between -->
                    <div class="lw-age-between-container lw-basic-filter-field">
                        <label for="min_age"><?= __tr('Age Between') ?></label>
                        <select name="min_age" class="form-control" id="min_age">
                            @foreach(range(18,70) as $age)
                            <option value="<?= $age ?>" <?= (request()->min_age == $age or $age == $minAge) ? 'selected' : '' ?>><?= $age ?></option>
                            @endforeach
                        </select>
                        <select name="max_age" class="form-control" id="max_age">
                            @foreach(range(18,70) as $age)
                            <option value="<?= $age ?>" <?= (request()->max_age == $age or $age == $maxAge) ? 'selected' : '' ?>><?= $age ?></option>
                            @endforeach
                        </select>
                    </div>
                    <!-- /Age between -->
                    <!-- Distance from my location -->
                    <div class="lw-distance-location-container lw-basic-filter-field">
                        <label for="distance"><?= __tr('Distance From My Location (__distanceUnit__)', ['__distanceUnit__' => (getStoreSettings('distance_measurement') == '6371') ? 'KM' : 'Miles']) ?></label>
                        <input type="text" class="form-control" name="distance" value="<?= (request()->distance != null) ? request()->distance : getUserSettings('distance') ?>" placeholder="<?= __tr('Anywhere') ?>">
                    </div>
                    <!-- /Distance from my location -->
                    <div class="lw-basic-filter-footer-container lw-basic-filter-field">
                        <button type="submit" class="btn btn-primary"><?= __tr('Search') ?></button>
                    </div>
                </form>
            </div>
        </li>
    </ul>
    <!-- buy premium plans page link -->
    @if(!isPremiumUser())
    <a href="<?= route('user.premium_plan.read.view') ?>" class="btn btn-primary btn-sm" title="<?= __tr('Be Premium User') ?>"><?= __tr('Be Premium') ?></a>
    <!-- /buy premium plans page link -->
    @endif
    <!-- Topbar Navbar -->

    <ul class="navbar-nav">
        <li class="nav-item d-none d-sm-none d-md-block">
            <a class="nav-link" onclick="getChatMessenger('<?= route('user.read.all_conversation') ?>', true)" id="lwAllMessageChatButton" data-chat-loaded="false" data-toggle="modal" data-target="#messengerDialog">
                <span class="badge badge-danger badge-counter lw-new-message-badge"></span>
                <i class="far fa-comments"></i>
            </a>
        </li>
        <!-- Notification Link -->
        <li class="nav-item dropdown no-arrow mx-1 d-none d-sm-none d-md-block">
            <a class="nav-link dropdown-toggle lw-ajax-link-action" href="<?= route('user.notification.write.read_all_notification') ?>" data-callback="onReadAllNotificationCallback" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-method="post">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger badge-counter" data-model="totalNotificationCount"><?= (getNotificationList()['notificationCount'] > 0) ? getNotificationList()['notificationCount'] : '' ?></span>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                    <?= __tr('Notification') ?>
                </h6>
                <!-- Notification block -->
                <div id="lwNotificationContent"></div>
                <script type="text/_template" id="lwNotificationListTemplate">
                    <% if(!_.isEmpty(__tData.notificationList)) { %>
						<% _.forEach(__tData.notificationList, function(notification) { %>
							<!-- show all notification list -->
							<a class="dropdown-item d-flex align-items-center" href="<%- notification['actionUrl'] %>">
								<div>
									<div class="small text-gray-500"><%- notification['created_at'] %></div>
									<span class="font-weight-bold"><%- notification['message'] %></span>
								</div>
							</a>
							<!-- show all notification list -->
						<% }); %>
						<!-- show all notification link -->
						<a class="dropdown-item text-center small text-gray-500" href="<?= route('user.notification.read.view') ?>" id="lwShowAllNotifyLink" data-show-if="showAllNotifyLink"><?= __tr('Show All Notifications.') ?></a>
						<!-- /show all notification link -->
					<% } else { %>
						<!-- info message -->
						<a class="dropdown-item text-center small text-gray-500"><?= __tr('There are no notification.') ?></a>
						<!-- /info message -->
					<% } %>
				</script>
                <!-- /Notification block -->
            </div>
        </li>
        <!-- /Notification Link -->

        <!-- Nav Item - Messages -->
        <li class="nav-item d-none d-sm-none d-md-block">
            <a class="nav-link" href="<?= route('user.credit_wallet.read.view') ?>">
                <i class="fas fa-coins fa-fw mr-2"></i>
                <span class="badge badge-success badge-counter" id="lwTotalCreditWalletAmt"><?= totalUserCredits() ?></span>
            </a>
        </li>

        <!-- Nav Item - Messages -->
        <li class="nav-item d-none d-sm-none d-md-block">
            <a class="nav-link lw-ajax-link-action" method="get" data-callback="updateBoosterPrice" href="<?= route('user.read.booster_data') ?>" data-toggle="modal" data-target="#boosterModal">
                <i class="fas fa-bolt fa-fw mr-2"></i> <span id="lwBoosterTimerCountDown"></span>
            </a>
        </li>

        <?php $translationLanguages = getActiveTranslationLanguages(); ?>

        <!-- Language Menu -->
        @if(!__isEmpty($translationLanguages) and (count($translationLanguages) > 1))
        <?php $translationLanguages['en_US'] = configItem('default_translation_language');  ?>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="d-none d-md-inline-block"><?= (isset($translationLanguages[config('CURRENT_LOCALE')])) ? $translationLanguages[config('CURRENT_LOCALE')]['name'] : '' ?></span>
                &nbsp; <i class="fas fa-language"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <h6 class="dropdown-header">
                    <?= __tr('Choose your language') ?>
                </h6>
                <div class="dropdown-divider"></div>
                <?php foreach ($translationLanguages as $languageId => $language) {
                    if ($languageId == config('CURRENT_LOCALE') or (isset($language['status']) and $language['status'] == false)) continue;
                ?>
                    <a class="dropdown-item" href="<?= route('locale.change', ['localeID' => $languageId]) . '?redirectTo=' . base64_encode(Request::fullUrl());  ?>">
                        <?= $language['name'] ?>
                    </a>
                <?php } ?>
            </div>
        </li>
        @endif
        <!-- Language Menu -->

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="img-profile rounded-circle" src="<?= getUserAuthInfo('profile.profile_picture_url') ?>">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <h6 class="dropdown-header">
                    @if(isPremiumUser())
                    <a href='<?= route('user.premium_plan.read.view') ?>'><span class="lw-small-premium-badge" title="<?= __tr('Premium User') ?>"></span></a>
                    @endif
                    <?= getUserAuthInfo('profile.full_name') ?>
                </h6>
                @if(getStoreSettings('allow_user_to_change_theme'))
                <div class="dropdown-divider"></div>
                @if(getApplicableTheme() == 'light')
                <a class="dropdown-item" href="<?= route('site_theme.change', [
                                                    'themeName' => 'dark'
                                                ])  . '?redirectTo=' . base64_encode(Request::fullUrl());  ?>">
                    <i class="fas fa-moon fa-sm fa-fw mr-2 text-gray-400"></i>
                    <?= __tr('Dark Theme') ?>
                </a>
                @else
                <a class="dropdown-item" href="<?= route('site_theme.change', [
                                                    'themeName' => 'light'
                                                ])  . '?redirectTo=' . base64_encode(Request::fullUrl());  ?>">
                    <i class="fas fa-sun fa-sm fa-fw mr-2 text-gray-400"></i>
                    <?= __tr('Light Theme') ?>
                </a>
                @endif
                @endif
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" title="<?= __tr('My Profile') ?>" href="<?= route('user.profile_view', ['username' => getUserAuthInfo('profile.username')]) ?>">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    <?= __tr('My Profile') ?>
                </a>
                <a class="dropdown-item" title="<?= __tr('My Settings') ?>" href="<?= route('user.read.setting', ['pageType' => 'notification']) ?>">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    <?= __tr('My Settings') ?>
                </a>
                <a class="dropdown-item" title="<?= __tr('Change Password') ?>" href="<?= route('user.change_password') ?>">
                    <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>
                    <?= __tr('Change Password') ?>
                </a>
                <a class="dropdown-item" title="<?= __tr('Change Email') ?>" href="<?= route('user.change_email') ?>">
                    <i class="fas fa-envelope fa-sm fa-fw mr-2 text-gray-400"></i>
                    <?= __tr('Change Email') ?>
                </a>
                @if(isAdmin())
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-primary" title="<?= __tr('Admin Panel') ?>" target="_blank" href="<?= route('manage.dashboard') ?>">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    <?= __tr('Admin Panel') ?>
                </a>
                @endif
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" title="<?= __tr('Logout') ?>" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    <?= __tr('Logout') ?>
                </a>
            </div>
        </li>
    </ul>
</nav>

<!-- Modal -->
<div class="modal fade" id="boosterModal" tabindex="-1" role="dialog" aria-labelledby="boosterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="boosterModalLabel"><?= __tr('Boost Profile') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!-- insufficient balance error message -->
                <div class="alert alert-info" id="lwBoosterCreditsNotAvailable" style="display: none;">
                    <?= __tr('Your credit balance is too low, please') ?>
                    <a href="<?= route('user.credit_wallet.read.view') ?>"><?= __tr('purchase credits') ?></a>
                </div>
                <!-- / insufficient balance error message -->

                <div class="text-center">
                    <?= __tr('By boosting your profile you will be a part of featured user and will a get priority in search and random users and this will costs you __boosterPrice__ credits for immediate __boosterPeriod__ minutes', [
                        '__boosterPrice__' => '<strong id="lwBoosterPrice">' . (isPremiumUser() ? getStoreSettings('booster_price_for_premium_user') : getStoreSettings('booster_price')) . '</strong>',
                        '__boosterPeriod__' => '<strong id="lwBoosterPeriod">' . getStoreSettings('booster_period') . '</strong>'
                    ]) ?>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal"><?= __tr('Cancel') ?></button>
                <a class="btn btn-success btn-sm lw-ajax-link-action" data-callback="onProfileBoosted" href="<?= route('user.write.boost_profile') ?>" data-method="post"><i class="fas fa-bolt fa-fw"></i> <?= __tr('Boost') ?></a>
            </div>
        </div>
    </div>
</div>
<!-- for image gallery -->
@include('includes.image-gallery')

<!-- End of Topbar -->
@push('appScripts')
<script>
    window.onresize = function() {
        _.delay(function() {
            $('#cboxWrapper,#colorbox').height($('#cboxContent').height());
            $('#cboxWrapper,#colorbox').width($('#cboxContent').width() - 5);
        }, 300);
    };

    updateBoosterPrice = function(response) {
        if (response.reaction == 1) {
            $("#lwBoosterPeriod").html(response.data.booster_period);
            $("#lwBoosterPrice").html(response.data.booster_price);
        }
    };

    //callback for when profile boosted
    onProfileBoosted = function(response) {
        if (_.has(response.data, 'boosterExpiry')) {
            activateBooster(response.data.boosterExpiry);
            $('#boosterModal').modal('hide');
        }
        //updated credit wallet amt
        if (_.has(response.data, 'creditsRemaining')) {

            if (response.data.creditsRemaining <= 0) {
                $("#lwBoosterCreditsNotAvailable").show();
            }

            $("#lwTotalCreditWalletAmt").html(response.data.creditsRemaining)
        }
    };

    var boosterInterval;

    //to calculate booster and show countdown
    activateBooster = function(boosterExpiry) {
        clearInterval(boosterInterval);
        if (boosterExpiry > 0) {
            var boosterExpiryTime = (new Date().getTime()) + (boosterExpiry * 1000);
            var now, timeRemaining, days, hours, minutes, seconds = 0;
            var timeString = "";
            boosterInterval = setInterval(function() {
                now = new Date().getTime();
                timeRemaining = boosterExpiryTime - now;
                // days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24)); 
                hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);
                timeString = ("0" + hours.toString()).slice(-2) +
                    ":" + ("0" + minutes.toString()).slice(-2) +
                    ":" + ("0" + seconds.toString()).slice(-2);
                $('#lwBoosterTimerCountDown,#lwBoosterTimerCountDownOnSB').html(timeString);
                if (timeRemaining < 0) {
                    clearInterval(boosterInterval);
                    $('#lwBoosterTimerCountDown,#lwBoosterTimerCountDownOnSB').html("");
                }
            }, 1000);
        }
    };

    activateBooster(<?= getProfileBoostTime() ?>);
    // Set text direction for RTL language support
    function setTextDirection(isRtl) {
        if (isRtl) {
            $('html').attr('dir', 'rtl');
        }
    };

    var translationLanguages = '<?= (!__isEmpty($translationLanguages)) ? json_encode($translationLanguages) : null ?>',
        currentLocale = "<?= config('CURRENT_LOCALE') ?>";
    // Check if translation language exists
    if (!_.isEmpty(translationLanguages)) {
        if (!_.isUndefined(JSON.parse(translationLanguages)[currentLocale])) {
            var selectedLang = JSON.parse(translationLanguages)[currentLocale];
            if (selectedLang['is_rtl']) {
                setTextDirection(true);
            }
        }
    }

    //get notification data
    <?php $getNotificationList = getNotificationList() ?>;
    // get lodash template
    var template = _.template($("#lwNotificationListTemplate").html());
    // append template
    $("#lwNotificationContent").html(template({
        'notificationList': JSON.parse('<?= json_encode($getNotificationList['notificationData']) ?>'),
    }));

    //on read all notification callback
    function onReadAllNotificationCallback(responseData) {
        if (responseData.reaction == 1) {
            __DataRequest.updateModels({
                'totalNotificationCount': '', //total notification count
            });
        }
    };
</script>
@endpush