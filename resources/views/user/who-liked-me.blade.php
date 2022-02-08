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
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h5 class="h5 mb-0 text-gray-200">
		<span class="text-primary"><i class="fa fa-heart" aria-hidden="true"></i></span>
		<?= __tr('Who Likes Me') ?></h5>
</div>

<!-- liked people container -->
<div class="container-fluid">
	@if(getFeatureSettings('show_like'))
	@if(!__isEmpty($usersData))
	<div class="row row-cols-sm-1 row-cols-md-2 row-cols-lg-4" id="lwWhoLikedUsersContainer">
		@include('user.partial-templates.my-liked-users')
	</div>
	@else
	<!-- info message -->
	<div class="alert alert-info">
		<?= __tr('There are no people liked me.') ?>
	</div>
	<!-- / info message -->
	@endif
	@else
	<!-- info message -->
	<div class="alert alert-info">
		<?= __tr('This is a premium feature, to view who likes me you need to buy premium plan first.') ?>
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
			$("#lwWhoLikedUsersContainer").append(response.data);
		}
	};
</script>
@endpush