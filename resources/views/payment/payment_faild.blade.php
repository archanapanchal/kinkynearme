@section('page-title', strip_tags($userData['userName']))
@section('head-title', strip_tags($userData['userName']))
@section('page-url', url()->current())

@if(isset($userData['aboutMe']))
@section('keywordName', strip_tags($userProfileData['aboutMe']))
@section('keyword', strip_tags($userProfileData['aboutMe']))
@section('description', strip_tags($userProfileData['aboutMe']))
@section('keywordDescription', strip_tags($userProfileData['aboutMe']))
@endif

@if(isset($userData['profilePicture']))
@section('page-image', $userData['profilePicture'])
@endif
@if(isset($userData['coverPicture']))
@section('twitter-card-image', $userData['coverPicture'])
@endif


<section class="payment payment-faild">
	<div class="container">
		<div class="fadeInDown text-center payment-box">
		<img src="<?= url('/dist/images/payment-faild.png'); ?>">
		<h3 class="sub-title-error">Payment Failed</h3>
		<h5>It seem we have not received money</h5>
		</div>
	</div>
</section>
@push('appScripts')
@endpush