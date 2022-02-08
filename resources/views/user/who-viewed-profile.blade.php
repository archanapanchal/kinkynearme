@section('page-title', __tr('Who Likes Me'))
@section('head-title', __tr('Who Likes Me'))
@section('keywordName', __tr('Who Likes Me'))
@section('keyword', __tr('Who Likes Me'))
@section('description', __tr('Who Likes Me'))
@section('keywordDescription', __tr('Who Likes Me'))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())


<!-- Page Heading -->
<div class="tab-pane active" id="tabs-3" role="tabpanel">
    <div class="find-people-tab tab-inner">
        <div class="tab-heading">
            <div class="tab-left-side">
                <h3><?= __tr('Following are the people who viewed my profile') ?></h3>
            </div>
        </div>

		<!-- liked people container -->
		<div class="find-people-profile">
			@if(!__isEmpty($usersData))
			<div class="row" id="lwWhoViewedMyProfileContainer">
				@include('user.partial-templates.my-viewed-profile')
			</div>
			@else
			<!-- info message -->
			<br>
			<div class="alert alert-info">
				<?= __tr('here are no people who viewd my profile.') ?>
			</div>
			<!-- / info message -->
			@endif
		</div>
		<!-- / liked people container -->

		@if($hasMorePages)
        <div class="lw-load-more-container">
            <button type="button" class="btn btn-light btn-block lw-ajax-link-action" id="lwLoadMoreButton" data-action="<?= $nextPageUrl ?>" data-callback="loadMoreUsers"><?= __tr('Load More') ?></button>
        </div>
        @endif

	</div>
</div>


@push('appScripts')
<script type="text/javascript">

	function loadMoreUsers(responseData) {

        $(function() {
            applyLazyImages();
        });
        var requestData = responseData.data,
            appendData = responseData.response_action.content;
        console.log(responseData.response_action);
        $('#lwWhoViewedMyProfileContainer').append(appendData);
        $('#lwLoadMoreButton').data('action', requestData.nextPageUrl);
        if (!requestData.hasMorePages) {
            $('.lw-load-more-container').hide();
        }
    }
	
	
	function loadNextLikedUsers(response) {
		if (response.data != '') {
			//call lazy load function in misc.js file for image lazy loaded
			$(function() {
				applyLazyImages();
			});
			$("#lwNextPageLink").remove();
			$("#lwWhoViewedMyProfileContainer").append(response.data);
		}
	};
</script>
@endpush