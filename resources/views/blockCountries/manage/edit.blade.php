@section('blockCountry-title', __tr("Edit Blocked Country"))
@section('head-title', __tr("Edit Blocked Country"))
@section('keywordName', strip_tags(__tr("Edit Blocked Country")))
@section('keyword', strip_tags(__tr("Edit Blocked Country")))
@section('description', strip_tags(__tr("Edit Blocked Country")))
@section('keywordDescription', strip_tags(__tr("Edit Blocked Country")))
@section('blockCountry-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('blockCountry-url', url()->current())

<!-- Block Country Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Edit Block Country') ?></h1>
	<!-- back button -->
	<a class="btn btn-light btn-sm" href="<?= route('manage.blockCountry.view') ?>">
		<i class="fa fa-arrow-left" aria-hidden="true"></i> <?= __tr('Back to Block Countriess') ?>
	</a>
	<!-- /back button -->
</div>
<!-- Start of Block Country Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">

				<!-- blockCountry edit form -->
				<form class="lw-ajax-form lw-form" method="post" action="<?= route('manage.blockCountry.write.edit', ['blockCountryUId' => $blockCountryEditData['_uid']]) ?>">
					<!-- hidden _uid input field -->
					<input type="hidden" value="<?= $blockCountryEditData['_uid'] ?>" class="form-control" name="blockCountryUid">
					<!-- / hidden _uid input field -->

					<!-- name input field -->
					<div class="form-group">
						<label for="lwName"><?= __tr('Name') ?></label>
						<input type="text" value="<?= $blockCountryEditData['name'] ?>" id="lwName" class="form-control" name="name" required minlength="3">
					</div>
					<!-- / name input field -->

					<!-- status field -->
					<div class="custom-control custom-checkbox custom-control-inline" style="display: none">
						<input type="checkbox" class="custom-control-input" id="activeCheck" name="status" <?= $blockCountryEditData['status'] == 1 ? 'checked' : '' ?>>
						<label class="custom-control-label" for="activeCheck"><?= __tr('Active') ?></label>
					</div>
					<!-- / status field -->

					<br><br>
					<!-- update button -->
					<button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile"><?= __tr('Update') ?></button>
					<!-- / update button -->
				</form>
				<!-- / blockCountry edit form -->
			</div>
		</div>
	</div>
</div>
<!-- End of BlockCountry Wrapper -->