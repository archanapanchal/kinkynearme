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
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h5 class="h5 mb-0 text-gray-200">
		<span class="text-primary"><i class="fa fa-heart" aria-hidden="true"></i></span>
		<?= __tr('Mutual Likes') ?></h5>
</div>

<!-- liked people container -->
<div class="container-fluid">
	@if(!__isEmpty($usersData))
	<div class="row row-cols-sm-1 row-cols-md-2 row-cols-lg-4" id="lwMutualLikesContainer">
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
</script>
@endpush