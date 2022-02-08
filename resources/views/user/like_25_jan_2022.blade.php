@section('page-title', __tr('Mutual Likes'))
@section('head-title', __tr('Mutual Likes'))
@section('keywordName', __tr('Mutual Likes'))
@section('keyword', __tr('Mutual Likes'))
@section('description', __tr('Mutual Likes'))
@section('keywordDescription', __tr('Mutual Likes'))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- Page Heading -->
<div class="tab-pane active" id="tabs-2" role="tabpanel">
    <div class="find-people-tab tab-inner">
        <div class="tab-heading">
            <div class="tab-left-side">
                <h3><?= __tr('Following are people who you liked.') ?></h3>
            </div>
        </div>

		<!-- liked people container -->
		<div class="find-people-profile">
			@if(!__isEmpty($usersData))
			<div class="row" id="lwMutualLikesContainer">
				@include('user.partial-templates.my-liked-users')
			</div>
			@else
			<!-- info message -->
			<div class="alert alert-info">
				<?= __tr('There are no mutual likes.') ?>
			</div>
			<!-- / info message -->
			@endif
		</div>
		<!-- / liked people container -->
		<!-- if($hasMorePages)
        <div class="lw-load-more-container">
            <button type="button" class="btn btn-light btn-block lw-ajax-link-action" id="lwLoadMoreButton" data-action="<?php //echo $nextPageUrl ?>" data-callback="loadMoreUsers"><?php //echo __tr('Load More') ?></button>
        </div>
        endif -->

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
			$("#lwMutualLikesContainer").append(response.data);
		}
	};

	function onLikeCallback(response) {
		var requestData = response.data;
		//check reaction code is 1 
		if (response.reaction == 1) {
			$("#user-" + requestData.user_id).fadeOut(1000, function() { $(this).remove(); })
		}
	}

	function onFavouriteCallback(response) {

	}

</script>
@endpush