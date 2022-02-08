@section('page-title', __tr('Liked You'))
@section('head-title', __tr('Liked You'))
@section('keywordName', __tr('Liked You'))
@section('keyword', __tr('Liked You'))
@section('description', __tr('Liked You'))
@section('keywordDescription', __tr('Liked You'))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())


<!-- Page Heading -->
<div class="tab-pane active" id="tabs-2" role="tabpanel">
    <div class="find-people-tab tab-inner">
        <div class="tab-heading">
            <div class="tab-left-side">
                <h3><?= __tr('Following are people who liked your profile.') ?></h3>
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
				<?= __tr('There are no users found who liked your profile.') ?>
			</div>
			<!-- / info message -->
			@endif
		</div>

		@if($hasMorePages)
        <div class="lw-load-more-container">
            <button type="button" class="btn btn-light btn-block lw-ajax-link-action" id="lwLoadMoreButton" data-action="<?= $nextPageUrl ?>" data-callback="loadMoreUsers"><?= __tr('Load More') ?></button>
        </div>
        @endif
		<!-- / liked people container -->

	</div>
</div>

@push('appScripts')
<script type="text/javascript">
	/*function loadNextLikedUsers(response) {
		if (response.data != '') {
			//call lazy load function in misc.js file for image lazy loaded
			$(function() {
				applyLazyImages();
			});
			$("#lwNextPageLink").remove();
			$("#lwMutualLikesContainer").append(response.data);
		}
	};*/


	function loadMoreUsers(responseData) {

        $(function() {
            applyLazyImages();
        });
        var requestData = responseData.data,
            appendData = responseData.data;
        console.log(appendData);
        $('#lwMutualLikesContainer').append(appendData);
        $('#lwLoadMoreButton').data('action', requestData.nextPageUrl);
        if (!requestData.hasMorePages) {
            $('.lw-load-more-container').hide();
        }
    }

	/*function onLikeCallback(response) {
		var requestData = response.data;
		//check reaction code is 1 
		if (response.reaction == 1) {
			$("#user-" + requestData.user_id).fadeOut(1000, function() { $(this).remove(); })
		}
	}

	function onFavouriteCallback(response) {

	}*/


	function onLikeCallback(response) {
        var requestData = response.data;        
        if (response.reaction == 1) { 

        	console.log(requestData);


            if (requestData.status == "deleted") {
            	// Modal
				$(".user_detail_modal").find("#user-" + requestData.user_id).find('div.main-heart').find('span').find('svg').find('g').find('path').attr({"fill": ""});

                $("#user-" + requestData.user_id).find('div.main-heart').find('path').attr({"fill": ""});
                $("#user-" + requestData.user_id).fadeOut(1000, function() { $(this).remove(); });
            } else {
            	/*modal*/
            	$(".user_detail_modal").find("#user-" + requestData.user_id).find('div.main-heart').find('span').find('svg').find('g').find('path').attr({"fill": "#f06a6a"});

                $("#user-" + requestData.user_id).find('div.main-heart').find('path').attr({"fill": "#f06a6a"})
            }  
        }
    }

    function onFavouriteCallback(response) {
        var requestData = response.data;


        if (response.reaction == 1) {
            if (requestData.status == "deleted") {
            	/*Modal*/
            	$(".user_detail_modal").find("#user-" + requestData.user_id).find('div.main-star').find('span').find('svg').find('g').find('path').attr({"fill": ""});

                $("#user-" + requestData.user_id).find('div.main-star').find('path').attr({"fill": ""})
            } else {
            	/*Modal*/
            	$(".user_detail_modal").find("#user-" + requestData.user_id).find('div.main-star').find('span').find('svg').find('g').find('path').attr({"fill": "#ebe054"});
            	
                $("#user-" + requestData.user_id).find('div.main-star').find('path').attr({"fill": "#ebe054"})
            }  
        }
    }

</script>
@endpush