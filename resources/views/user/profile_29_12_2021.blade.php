@section('page-title', strip_tags($userData['userName']))
@section('head-title', strip_tags($userData['userName']))
@section('page-url', url()->current())

@if(isset($userData['aboutMe']))
@section('keywordName', strip_tags($userProfileData['aboutMe']))
@section('keyword', strip_tags($userProfileData['aboutMe']))
@section('description', strip_tags($userProfileData['aboutMe']))
@section('keywordDescription', strip_tags($userProfileData['aboutMe']))
@endif

@if(isset($userData['profilePicture']))
@section('page-image', $userData['profilePicture'])
@endif
@if(isset($userData['coverPicture']))
@section('twitter-card-image', $userData['coverPicture'])
@endif


	<div class="tab-pane active" id="tabs-6" role="tabpanel">
        <div class="my-account">
            @if($isOwnProfile)
            <div class="tab">                
                <button class="tablinks" onclick="openCity(event, 'my-profile')" id="defaultOpen">My Profile</button>
                <button class="tablinks" onclick="openCity(event, 'manage-pass')">Manage Password</button>
                <button class="tablinks" onclick="openCity(event, 'subscription')">Subscription</button>
                <button class="tablinks" onclick="openCity(event, 'blocked-profiles')">Blocked Profiles</button>
                <button class="tablinks" onclick="openCity(event, 'settings')">Settings</button>
            </div>
            @endif
                <div id="my-profile" class="tabcontent">

                	<div class="my-profile-section">
                        <div class="left-right-side-my-profile">
                            <div class="left-side-my-profile">
                            	<img class="lw-profile-thumbnail lw-photoswipe-gallery-img lw-lazy-img" id="lwProfilePictureStaticImage" data-src="<?= imageOrNoImageAvailable($userData['profilePicture']) ?>">
                            </div>

                            <div class="right-side-my-profile">
                                <div class="profile-title-edit d-flex-class">
                                    <div class="profile-title">

                                        @if(!$isOwnProfile)
                                        <a class="mr-3 btn-link btn d-none" onclick="getChatMessenger('<?= route('user.read.individual_conversation', ['specificUserId' => $userData['userId']]) ?>')" href id="lwMessageChatButton" data-chat-loaded="false" data-toggle="modal" data-target="#messengerDialog"><i class="far fa-comments fa-3x"></i>
                                        </a>

                                        @endif

                                        <h3><?= $userData['fullName'] ?></h3>
                                        <!-- show user online, idle or offline status -->
										@if($userOnlineStatus == 1)
										<span><img src="<?= url('dist/images/dots-thik.svg') ?>"><?= __tr("Online") ?></span>
										@elseif($userOnlineStatus == 2)
										<span><img src="<?= url('dist/images/dots-thik.svg') ?>"><?= __tr("Idle") ?></span>
										@elseif($userOnlineStatus == 3)
										<span><img src="<?= url('dist/images/dots-thik.svg') ?>"><?= __tr("Offline") ?></span>
										@endif
										<!-- /show user online, idle or offline status -->
                                        
                                    </div>
                                    @if($isOwnProfile)
                                    <div class="profile-edit">
                                        <a href="#" class="edit">Edit Profile</a>
                                        <a href="#" class="profile-icon"><img src="<?= url('dist/images/share-icon.png') ?>"></a>
                                    </div>
                                    @endif
                                </div>
                                <!-- end -->
                                <div class="address-social-icon d-flex-class">
                                    <div class="address">
                                        <p>@if(!__isEmpty($userData['userAge'])) <?= __tr($userData['userAge']) ?> @endif <?= __ifIsset($userProfileData['gender_text'], $userProfileData['gender_text'], '-') ?> <br><?= $userProfileData['city'] ?>, <?= $userProfileData['country_name'] ?></p>
                                    </div>
                                    <div class="social-icon">
                                        @if(!$isOwnProfile)
                                            <a class="icon d-none" onclick="getChatMessenger('<?= route('user.read.individual_conversation', ['specificUserId' => $userData['userId']]) ?>')" href id="lwMessageChatButton" data-chat-loaded="false" data-toggle="modal" data-target="#messengerDialog"><img src="<?= url('dist/images/message.svg') ?>"></a>

                                            <a class="icon" ><img src="<?= url('dist/images/message.svg') ?>"></a>

                                            <a href data-action="<?= route('user.write.like_dislike', ['toUserUid' => $userData['userUId'], 'like' => 1]) ?>" data-method="post" data-callback="onLikeCallback" title="Like" class="icon lw-ajax-link-action lw-like-action-btn" id="lwLikeBtn">
                                                <span class="<?= (isset($userLikeData['like']) and $userLikeData['like'] == 1) ? 'lw-is-active' : '' ?>"><img src="<?= url('dist/images/heart-icon.svg') ?>"></span>
                                            </a>

                                            <a href data-action="<?= route('user.write.favourite', ['toUserUid' => $userData['userUId'], 'favourite' => 1]) ?>" data-method="post" data-callback="onFavouriteCallback" title="Favourite" class="icon lw-ajax-link-action lw-favourite-action-btn" id="lwFavouriteBtn">
                                                <span class="<?= (isset($userFavouriteData['favourite']) and $userFavouriteData['favourite'] == 1) ? 'lw-is-active' : '' ?>"><img src="<?= url('dist/images/star.svg') ?>"></span>
                                            </a>

                                        @endif
                                    </div>
                                </div>
                                <!-- end -->
                                <div class="open-for-relation">
                                    <div class="open-title-edit d-flex-class">
                                        <h5>Open for relationships</h5>
                                        @if($isOwnProfile)
                                        <a href="#" class="edit">Edit</a>
                                        @endif
                                    </div>
                                    <div class="open-desc">
                                        <ul>
                                            <li>
                                                <span>Looking for</span>
                                                <p><?= $userSpecificationTexts['looking_for'] ?></p>
                                            </li>
                                            <li>
                                                <span>Interests/Kinks</span>
                                                <p><?= $userSpecificationTexts['kinks'] ?></p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- end -->
                                <div class="physical-appearance">
                                    <div class="open-desc d-flex-class">
                                        <span>Open for relationships</span>
                                        <span>Open for relationships</span>
                                    </div>
                                    <div class="open-desc">
                                        <ul>
                                            <li>
                                                <img src="<?= url('dist/images/app-1.png') ?>">
                                                <p><?= $userSpecificationTexts['body_type'] ?></p>
                                            </li>
                                            <li>
                                                <img src="<?= url('dist/images/app-2.png') ?>">
                                                <p><?= $userSpecificationTexts['hair_color'] ?></p>
                                            </li>
                                            <li>
                                                <img src="<?= url('dist/images/app-3.png') ?>">
                                                <p><?= $userSpecificationTexts['ethnicity'] ?></p>
                                            </li>
                                            <li>
                                                <img src="<?= url('dist/images/app-4.png') ?>">
                                                <p><?= $userSpecificationTexts['height'] ?></p>
                                            </li>
                                            <li>
                                                <img src="<?= url('dist/images/app-5.png') ?>">
                                                <p><?= $userSpecificationTexts['eye_color'] ?></p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- end -->
                                <div class="physical-appearance life-style">
                                    <div class="open-desc d-flex-class">
                                        <span>Lifestyle</span>
                                    </div>
                                    <div class="open-desc">
                                        <ul>
                                            <li>
                                                <img src="<?= url('dist/images/app-6.png') ?>">
                                                <p><?= $userSpecificationTexts['smoke'] ?></p>
                                            </li>
                                            <li>
                                                <img src="<?= url('dist/images/app-7.png') ?>">
                                                <p><?= $userSpecificationTexts['drink'] ?></p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- end -->
                            </div>
                            <!-- end -->
                        </div>

                        @if(isset($userProfileData['aboutMe']) and $userProfileData['aboutMe'])
							<div class="my-profile-about">
						        <p class="about-title"><?= __tr('About Me') ?></p>
								<div class="about-content">
									<?= __ifIsset($userProfileData['aboutMe'], $userProfileData['aboutMe'], '-') ?>
								</div>
							</div>
						@else 
							<div class="my-profile-about">
                                <p class="about-title">About me</p>
                                <p class="about-content">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec aliquam dolor in diam bibendum egestas. Proin interdum tempus tincidunt. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nam convallis lectus arcu, et molestie justo sodales eu. Quisque vitae massa ut lectus pretium imperdiet. Praesent dapibus, leo at tempor ultrices, erat arcu ultrices nulla, sit amet cursus arcu est in mi. Phasellus at purus tincidunt, posuere quam nec, convallis quam. Nulla volutpat nibh vel neque fermentum iaculis. Phasellus luctus lorem pulvinar risus ultrices, commodo consequat diam vestibulum. Praesent scelerisque rhoncus auctor. Vivamus vestibulum pharetra quam vitae fringilla.</p>
                                <p class="about-content"> Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In nisl nulla, fringilla eget dapibus et, cursus at ipsum. Sed libero orci, pretium eu dapibus in, dignissim sed leo.</p>
                            </div>
						@endif


                        @if(!__isEmpty($photosData) or $isOwnProfile)
						<div class="my-profile-images">
							<p class="about-title"><?= __tr('More photos') ?></p>
							<div class="row">
								@if(!__isEmpty($photosData))
								@foreach($photosData as $key => $photo)
								<div class="col-md-3"><img class="lw-user-photo lw-photoswipe-gallery-img lw-lazy-img" data-img-index="<?= $key ?>" data-src="<?= imageOrNoImageAvailable($photo['image_url']) ?>"></div>
								@endforeach
								@else
								<p><?= __tr('Ooops... No images found...') ?></p>
								@endif
							</div>
						</div>
						@endif
                    </div>
                </div>
        </div>
    </div>

	
	<!-- user report Modal-->
	<div class="modal fade" id="lwReportUserDialog" tabindex="-1" role="dialog" aria-labelledby="userReportModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="userReportModalLabel"><?= __tr('Abuse Report to __username__', [
																			'__username__' => $userData['fullName']
																		]) ?></h5>
					<button class="close" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<form class="lw-ajax-form lw-form" id="lwReportUserForm" method="post" data-callback="userReportCallback" action="<?= route('user.write.report_user', ['sendUserUId' => $userData['userUId']]) ?>">
					<div class="modal-body">
						<!-- reason input field -->
						<div class="form-group">
							<label for="lwUserReportReason"><?= __tr('Reason') ?></label>
							<textarea class="form-control" rows="3" id="lwUserReportReason" name="report_reason" required></textarea>
						</div>
						<!-- / reason input field -->
					</div>

					<!-- modal footer -->
					<div class="modal-footer mt-3">
						<button class="btn btn-light btn-sm" id="lwCloseUserReportDialog"><?= __tr('Cancel') ?></button>
						<button type="submit" class="btn btn-primary btn-sm lw-ajax-form-submit-action btn-user lw-btn-block-mobile"><?= __tr('Report') ?></button>
					</div>
				</form>
				<!-- modal footer -->
			</div>
		</div>
	</div>
	<!-- /user report Modal-->


	<!-- User block Confirmation text html -->
	<div id="lwBlockUserConfirmationText" style="display: none;">
		<h3><?= __tr('Are You Sure!') ?></h3>
		<strong><?= __tr('You want to block this user.') ?></strong>
	</div>
	<!-- /User block Confirmation text html -->


@push('appScripts')
<script>
	
	// Get user profile data
	function getUserProfileData(response) {
		// If successfully stored data
		if (response.reaction == 1) {
			__DataRequest.get("<?= route('user.get_profile_data', ['username' => getUserAuthInfo('profile.username')]) ?>", {}, function(responseData) {
				var requestData = responseData.data;
				var specificationUpdateData = [];
				_.forEach(requestData.userSpecificationData, function(specification) {
					_.forEach(specification['items'], function(item) {
						specificationUpdateData[item.name] = item.value;
					});
				});
				__DataRequest.updateModels('userData', requestData.userData);
				__DataRequest.updateModels('profileData', requestData.userProfileData);
				__DataRequest.updateModels('specificationData', specificationUpdateData);
			});
		}
	}

	/**************** User Like Dislike Fetch and Callback Block Start ******************/
	//add disabled anchor tag class on click
	$(".lw-like-action-btn, .lw-dislike-action-btn").on('click', function() {
		$('.lw-like-dislike-box').addClass("lw-disable-anchor-tag");
	});
	//on like Callback function
	function onLikeCallback(response) {
		var requestData = response.data;
		//check reaction code is 1 and status created or updated and like status is 1
		if (response.reaction == 1 && requestData.likeStatus == 1 && (requestData.status == "created" || requestData.status == 'updated')) {
			__DataRequest.updateModels({
				'userLikeStatus': '<?= __tr('Liked') ?>', //user liked status
				'userDislikeStatus': '<?= __tr('Dislike') ?>', //user dislike status
			});
			//add class
			$(".lw-animated-like-heart").toggleClass("lw-is-active");
			//check if updated then remove class in dislike heart
			if (requestData.status == 'updated') {
				$(".lw-animated-broken-heart").toggleClass("lw-is-active");
			}
		}
		//check reaction code is 1 and status created or updated and like status is 2
		if (response.reaction == 1 && requestData.likeStatus == 2 && (requestData.status == "created" || requestData.status == 'updated')) {
			__DataRequest.updateModels({
				'userLikeStatus': '<?= __tr('Like') ?>', //user like status
				'userDislikeStatus': '<?= __tr('Disliked') ?>', //user disliked status
			});
			//add class
			$(".lw-animated-broken-heart").toggleClass("lw-is-active");
			//check if updated then remove class in like heart
			if (requestData.status == 'updated') {
				$(".lw-animated-like-heart").toggleClass("lw-is-active");
			}
		}
		//check reaction code is 1 and status deleted and like status is 1
		if (response.reaction == 1 && requestData.likeStatus == 1 && requestData.status == "deleted") {
			__DataRequest.updateModels({
				'userLikeStatus': '<?= __tr('Like') ?>', //user like status
			});
			$(".lw-animated-like-heart").toggleClass("lw-is-active");
		}
		//check reaction code is 1 and status deleted and like status is 2
		if (response.reaction == 1 && requestData.likeStatus == 2 && requestData.status == "deleted") {
			__DataRequest.updateModels({
				'userDislikeStatus': '<?= __tr('Dislike') ?>', //user like status
			});
			$(".lw-animated-broken-heart").toggleClass("lw-is-active");
		}
		//remove disabled anchor tag class
		_.delay(function() {
			$('.lw-like-dislike-box').removeClass("lw-disable-anchor-tag");
		}, 1000);
	}

    //on favourite Callback function
    function onFavouriteCallback(response) {
        return;
        var requestData = response.data;
        //check reaction code is 1 and status created or updated and like status is 1
        if (response.reaction == 1 && requestData.likeStatus == 1 && (requestData.status == "created" || requestData.status == 'updated')) {
            __DataRequest.updateModels({
                'userLikeStatus': '<?= __tr('Liked') ?>', //user liked status
                'userDislikeStatus': '<?= __tr('Dislike') ?>', //user dislike status
            });
            //add class
            $(".lw-animated-like-heart").toggleClass("lw-is-active");
            //check if updated then remove class in dislike heart
            if (requestData.status == 'updated') {
                $(".lw-animated-broken-heart").toggleClass("lw-is-active");
            }
        }
        //check reaction code is 1 and status created or updated and like status is 2
        if (response.reaction == 1 && requestData.likeStatus == 2 && (requestData.status == "created" || requestData.status == 'updated')) {
            __DataRequest.updateModels({
                'userLikeStatus': '<?= __tr('Like') ?>', //user like status
                'userDislikeStatus': '<?= __tr('Disliked') ?>', //user disliked status
            });
            //add class
            $(".lw-animated-broken-heart").toggleClass("lw-is-active");
            //check if updated then remove class in like heart
            if (requestData.status == 'updated') {
                $(".lw-animated-like-heart").toggleClass("lw-is-active");
            }
        }
        //check reaction code is 1 and status deleted and like status is 1
        if (response.reaction == 1 && requestData.likeStatus == 1 && requestData.status == "deleted") {
            __DataRequest.updateModels({
                'userLikeStatus': '<?= __tr('Like') ?>', //user like status
            });
            $(".lw-animated-like-heart").toggleClass("lw-is-active");
        }
        //check reaction code is 1 and status deleted and like status is 2
        if (response.reaction == 1 && requestData.likeStatus == 2 && requestData.status == "deleted") {
            __DataRequest.updateModels({
                'userDislikeStatus': '<?= __tr('Dislike') ?>', //user like status
            });
            $(".lw-animated-broken-heart").toggleClass("lw-is-active");
        }
        //remove disabled anchor tag class
        _.delay(function() {
            $('.lw-like-dislike-box').removeClass("lw-disable-anchor-tag");
        }, 1000);
    }
	/**************** User Like Dislike Fetch and Callback Block End ******************/

	//user report callback
	function userReportCallback(response) {
		//check success reaction is 1
		if (response.reaction == 1) {
			var requestData = response.data;
			//form reset after success
			$("#lwReportUserForm").trigger("reset");
			//close dialog after success
			$('#lwReportUserDialog').modal('hide');
			//reload view after 2 seconds on success reaction
			_.delay(function() {
				__Utils.viewReload();
			}, 1000)
		}
	}

	//close User Report Dialog
	$("#lwCloseUserReportDialog").on('click', function(e) {
		e.preventDefault();
		//form reset after success
		$("#lwReportUserForm").trigger("reset");
		//close dialog after success
		$('#lwReportUserDialog').modal('hide');
	});

	//block user confirmation
	$("#lwBlockUserBtn").on('click', function(e) {
		var confirmText = $('#lwBlockUserConfirmationText');
		//show confirmation 
		showConfirmation(confirmText, function() {
			var requestUrl = '<?= route('user.write.block_user') ?>',
				formData = {
					'block_user_id': '<?= $userData['userUId'] ?>',
				};
			// post ajax request
			__DataRequest.post(requestUrl, formData, function(response) {
				if (response.reaction == 1) {
					__Utils.viewReload();
				}
			});
		});
	});

	// Click on edit / close button 
	$('#lwEditBasicInformation, #lwCloseBasicInfoEditBlock').click(function(e) {
		e.preventDefault();
		showHideBasicInfoContainer();
	});
	// Show / Hide basic information container
	function showHideBasicInfoContainer() {
		$('#lwUserBasicInformationForm').toggle();
		$('#lwStaticBasicInformation').toggle();
		$('#lwCloseBasicInfoEditBlock').toggle();
		$('#lwEditBasicInformation').toggle();
	}
	// Show hide specification user settings
	function showHideSpecificationUser(formId, event) {
		event.preventDefault();
		$('#lwEdit' + formId).toggle();
		$('#lw' + formId + 'StaticContainer').toggle();
		$('#lwUser' + formId + 'Form').toggle();
		$('#lwClose' + formId + 'Block').toggle();
	}
	// Click on profile and cover container edit / close button 
	$('#lwEditProfileAndCoverPhoto, #lwCloseProfileAndCoverBlock').click(function(e) {
		e.preventDefault();
		showHideProfileAndCoverPhotoContainer();
	});
	// Hide / show profile and cover photo container
	function showHideProfileAndCoverPhotoContainer() {
		$('#lwProfileAndCoverStaticBlock').toggle();
		$('#lwProfileAndCoverEditBlock').toggle();
		$('#lwEditProfileAndCoverPhoto').toggle();
		$('#lwCloseProfileAndCoverBlock').toggle();
	}
	// After successfully upload profile picture
	function afterUploadedProfilePicture(responseData) {
		$('#lwProfilePictureStaticImage, .lw-profile-thumbnail').attr('src', responseData.data.image_url);
	}
	// After successfully upload Cover photo
	function afterUploadedCoverPhoto(responseData) {
		$('#lwCoverPhotoStaticImage').attr('src', responseData.data.image_url);
	}
</script>
@endpush