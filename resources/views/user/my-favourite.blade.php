@section('page-title', __tr('Favorite'))
@section('head-title', __tr('Favorite'))
@section('keywordName', __tr('Favorite'))
@section('keyword', __tr('Favorite'))
@section('description', __tr('Favorite'))
@section('keywordDescription', __tr('Favorite'))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())




<!-- Page Heading -->
<div class="tab-pane active" id="tabs-3" role="tabpanel">
    <div class="find-people-tab tab-inner">
        <div class="tab-heading">
            <div class="tab-left-side">
                <h3><?= __tr('Following are the people whom you marked as your favorite') ?></h3>
            </div>
        </div>

		<!-- liked people container -->
		<div class="find-people-profile">
			@if(!__isEmpty($usersData))
			<div class="row" id="lwLikedUsersContainer_data">
				@include('user.partial-templates.my-liked-users')
			</div>
			@else
			<!-- info message -->
			<br>
			<div class="alert alert-info">
				<?= __tr('There are no any users whom you marked as your favorite.') ?>
			</div>
			<!-- / info message -->
			@endif
		</div>

		@if($hasMorePages)
        <div class="lw-load-more-container">
            <button type="button" class="btn btn-light btn-block lw-ajax-link-action" id="lwLoadMoreButton" data-action="<?= $nextPageUrl ?>" data-callback="loadMoreUsers_data"><?= __tr('Load More data') ?></button>
        </div>
        @endif

		<!-- / liked people container -->
	</div>
</div>

@push('appScripts')
<script type="text/javascript">

	function loadMoreUsers_data(responseData) {
        
        $(function() {
            applyLazyImages();
        });
        var requestData = responseData.data,
            appendData = responseData.response_action.content;

        $('#lwLikedUsersContainer_data').append(appendData);
        $('#lwLoadMoreButton').data('action', requestData.nextPageUrl);
        if (!requestData.hasMorePages) {
            $('.lw-load-more-container').hide();
        }
    }
	
    // Show advance filter
    $('#lwShowAdvanceFilterLink').on('click', function(e) {
        e.preventDefault();
        $('.lw-advance-filter-container').addClass('lw-expand-filter');
        $('#lwShowAdvanceFilterLink').hide();
        $('#lwHideAdvanceFilterLink').show();
        // $('.lw-advance-filter-container').show();
    });
    // Hide advance filter
    $('#lwHideAdvanceFilterLink').on('click', function(e) {
        e.preventDefault();
        $('.lw-advance-filter-container').removeClass('lw-expand-filter');
        $('#lwShowAdvanceFilterLink').show();
        $('#lwHideAdvanceFilterLink').hide();
        // $('.lw-advance-filter-container').hide();
    });

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
			//$("#user-" + requestData.user_id).fadeOut(1000, function() { $(this).remove(); })
			if (response.data.status == "deleted") {
				// Modal
				$(".user_detail_modal").find("#user-" + requestData.user_id).find('div.main-heart').find('span').find('svg').find('g').find('path').attr({"fill": ""});

                $("#user-" + requestData.user_id).find('div.main-heart').find('path').attr({"fill": ""})
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
			$("#user-" + requestData.user_id).fadeOut(1000, function() { $(this).remove(); })
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