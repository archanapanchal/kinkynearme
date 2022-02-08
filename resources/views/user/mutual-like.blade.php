@section('page-title', __tr('Matches'))
@section('head-title', __tr('Matches'))
@section('keywordName', __tr('Matches'))
@section('keyword', __tr('Matches'))
@section('description', __tr('Matches'))
@section('keywordDescription', __tr('Matches'))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())
	


<!-- Page Heading -->
<div class="tab-pane active" id="tabs-2" role="tabpanel">
    <div class="find-people-tab tab-inner">
        <div class="tab-heading">
            <div class="tab-left-side">
                <h3><?= __tr('Following are people who liked back your profile and are a match to you') ?></h3>
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
		@if($hasMorePages)
        <div class="lw-load-more-container">
            <button type="button" class="btn btn-light btn-block lw-ajax-link-action" id="lwLoadMoreButton" data-action="<?= $nextPageUrl ?>" data-callback="loadMoreUsers_data"><?= __tr('Load More') ?></button>
        </div>
        @endif

	</div>
</div>

@push('appScripts')
<script type="text/javascript">


	function loadMoreUsers_data(responseData) {

       
        $(function() {
            applyLazyImages();
        });

        var requestData = responseData.data,
            appendData = responseData.data;


        $('#lwMutualLikesContainer').append(appendData);
        $('#lwLoadMoreButton').data('action', requestData.nextPageUrl);
        if (!requestData.hasMorePages) {
            $('.lw-load-more-container').hide();
        }
    }

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

	/*function onLikeCallback(response) {
		var requestData = response.data;
		//check reaction code is 1 
		if (response.reaction == 1) {
			
		}
	}

	function onFavouriteCallback(response) {

	}*/


	function onLikeCallback(response) {
        var requestData = response.data;        
        if (response.reaction == 1) { 
            if (response.data.status == "deleted") {
            	// Modal
				$(".user_detail_modal").find("#user-" + requestData.user_id).find('div.main-heart').find('span').find('svg').find('g').find('path').attr({"fill": ""});

            	$("#user-" + requestData.user_id).fadeOut(1000, function() { $(this).remove(); });
                $("#user-" + requestData.user_id).find('div.main-heart').find('path').attr({"fill": ""});
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
            if (response.data.status == "deleted") {
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