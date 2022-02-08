@section('page-title', __tr('Blocked Users'))
@section('head-title', __tr('Blocked Users'))
@section('keywordName', __tr('Blocked Users'))
@section('keyword', __tr('Blocked Users'))
@section('description', __tr('Blocked Users'))
@section('keywordDescription', __tr('Blocked Users'))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h5 class="h5 mb-0 text-gray-200">
		<span class="text-primary"><i class="fas fa-ban"></i></span> <?= __tr('Blocked Users') ?>
	</h5>
</div>

<!-- blocked users -->
<div class="container-fluid">
	@if(!__isEmpty($usersData))
	<div class="row row-cols-sm-1 row-cols-md-2 row-cols-lg-4" id="lwBlockedUsersContainer">
		@include('user.partial-templates.blocked-users')
	</div>
	@else
	<!-- info message -->
	<div class="alert alert-info">
		<?= __tr('There are no blocked users.') ?>
	</div>
	<!-- / info message -->
	@endif
</div>
<!-- / blocked users -->

@push('appScripts')
<script type="text/javascript">
	function loadNextLikedUsers(response) {
		if (response.data != '') {
			//call lazy load function in misc.js file for image lazy loaded
			$(function() {
				applyLazyImages();
			});
			$("#lwNextPageLink").remove();
			$("#lwBlockedUsersContainer").append(response.data);
		}
	};
</script>
@endpush

@push('appScripts')
<script>
	//get block user data
	var blockUserData = JSON.parse('<?= json_encode($usersData) ?>');

	//if block user length is zero then show info message
	if (blockUserData.length == 0) {
		$("#lwShowInfoMessage").show();
	} else {
		$("#lwShowInfoMessage").hide();
	}

	//on un block user callback
	function onUnblockUser(response) {
		//check reaction code is 1
		if (response.reaction == 1) {
			var requestData = response.data;

			//apply class row fade in
			$("#lwBlockUser_" + requestData.blockUserUid).hide();
			if (requestData.blockUserLength == 0) {
				$("#lwShowInfoMessage").show();
			}
		}
	}
</script>
@endpush