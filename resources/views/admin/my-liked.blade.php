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
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h5 class="h5 mb-0 text-gray-200">
		<span class="text-primary"><i class="fa fa-heart" aria-hidden="true"></i></span>
		<?= __tr('My Likes') ?></h5>
</div>

<!-- liked people container -->
<div class="container-fluid">
	@if(!__isEmpty($usersData))
	<div class="row row-cols-sm-1 row-cols-md-2 row-cols-lg-4" id="lwLikedUsersContainer">
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
</script>
@endpush