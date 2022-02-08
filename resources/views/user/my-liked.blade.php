@section('page-title', __tr('My Likes'))
@section('head-title', __tr('My Likes'))
@section('keywordName', __tr('My Likes'))
@section('keyword', __tr('My Likes'))
@section('description', __tr('My Likes'))
@section('keywordDescription', __tr('My Likes'))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- Page Heading -->
<div class="tab-pane active" id="tabs-2" role="tabpanel">
    <div class="find-people-tab tab-inner">
        <div class="tab-heading">
            <div class="tab-left-side">
                <h3><?= __tr('Following are people whose profile you liked') ?></h3>
            </div>
        </div>

		<!-- liked people container -->
		<div class="find-people-profile">
			@if(!__isEmpty($usersData))
			<div class="row" id="lwLikedUsersContainer">
				@include('user.partial-templates.my-liked-users')
			</div>
			@else
			<!-- info message -->
			<div class="alert alert-info">
				<?= __tr('There are no any users who likes me.') ?>
			</div>
			<!-- / info message -->
			@endif
		</div>
		<!-- / liked people container -->
	</div>
</div>

@push('appScripts')
<script type="text/javascript">
	function loadNextLikedUsers(response) {
		if (response.data != '') {
			//call lazy load function in misc.js file for image lazy loaded
			$(function() {
				applyLazyImages();
			});
			$("#lwNextPageLink").remove();
			$("#lwLikedUsersContainer").append(response.data);
		}
	};

	function onLikeCallback(response) {
		var requestData = response.data;
		//check reaction code is 1 
		if (response.reaction == 1) {
			// Modal
			$(".user_detail_modal").find("#user-" + requestData.user_id).find('div.main-heart').find('span').find('svg').find('g').find('path').attr({"fill": ""});
			$("#user-" + requestData.user_id).fadeOut(1000, function() { $(this).remove(); })
		}
	}

	function onFavouriteCallback(response) {

	}
</script>
@endpush