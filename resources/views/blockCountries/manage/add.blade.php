@section('blockCountry-title', __tr("Add Block Country"))
@section('head-title', __tr("Add Block Country"))
@section('keywordName', strip_tags(__tr("Add Block Country")))
@section('keyword', strip_tags(__tr("Add Block Country")))
@section('description', strip_tags(__tr("Add Block Country")))
@section('keywordDescription', strip_tags(__tr("Add Block Country")))
@section('blockCountry-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('blockCountry-url', url()->current())

<!-- BlockCountry Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Add Block Country')  ?></h1>
	<!-- back button -->
	<a class="btn btn-light btn-sm" href="<?= route('manage.blockCountry.view') ?>">
		<i class="fa fa-arrow-left" aria-hidden="true"></i> <?= __tr('Back to Block Countries') ?>
	</a>
	<!-- /back button -->
</div>
<!-- Start of Block Country Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">
				<!-- Block Country add form -->
				<form class="lw-ajax-form lw-form" method="post" action="<?= route('manage.blockCountry.write.add') ?>">
					<!-- name input field -->
					<div class="form-group">
						<label for="lwName"><?= __tr('Name') ?></label>
						<input type="text" class="form-control" name="name" id="lwName" required minlength="3">
					</div>
					<!-- / name input field -->

					<!-- status field -->
					<div class="custom-control custom-checkbox custom-control-inline" style="display: none">
						<input type="checkbox" class="custom-control-input" id="activeCheck" name="status" checked="true">
						<label class="custom-control-label" for="activeCheck"><?= __tr('Active')  ?></label>
					</div>
					<!-- / status field -->
					<br><br>
					<!-- add button -->
					<button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile"><?= __tr('Add')  ?></button>
					<!-- / add button -->
				</form>
				<!-- / Block Country add form -->
			</div>
		</div>
	</div>
</div>
<!-- End of Block Country Wrapper -->