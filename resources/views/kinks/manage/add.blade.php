@section('kink-title', __tr("Add Kink"))
@section('head-title', __tr("Add Kink"))
@section('keywordName', strip_tags(__tr("Add Kink")))
@section('keyword', strip_tags(__tr("Add Kink")))
@section('description', strip_tags(__tr("Add Kink")))
@section('keywordDescription', strip_tags(__tr("Add Kink")))
@section('kink-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('kink-url', url()->current())

<!-- Kink Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Add Kink')  ?></h1>
	<!-- back button -->
	<a class="btn btn-light btn-sm" href="<?= route('manage.kink.view') ?>">
		<i class="fa fa-arrow-left" aria-hidden="true"></i> <?= __tr('Back to Kinks') ?>
	</a>
	<!-- /back button -->
</div>
<!-- Start of Kink Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">
				<!-- kink add form -->
				<form class="lw-ajax-form lw-form" method="post" action="<?= route('manage.kink.write.add') ?>">
					<!-- title input field -->
					<div class="form-group">
						<label for="lwTitle"><?= __tr('Title') ?></label>
						<input type="text" class="form-control" name="title" id="lwTitle" required minlength="3">
					</div>
					<!-- / title input field -->

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
				<!-- / kink add form -->
			</div>
		</div>
	</div>
</div>
<!-- End of Kink Wrapper -->