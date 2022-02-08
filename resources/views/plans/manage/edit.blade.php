@section('plan-title', __tr("Edit Plan"))
@section('head-title', __tr("Edit Plan"))
@section('keywordName', strip_tags(__tr("Edit Plan")))
@section('keyword', strip_tags(__tr("Edit Plan")))
@section('description', strip_tags(__tr("Edit Plan")))
@section('keywordDescription', strip_tags(__tr("Edit Plan")))
@section('plan-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('plan-url', url()->current())

@push('header')
<link rel="stylesheet" href="<?= __yesset('dist/summernote/summernote.min.css') ?>" />
@endpush
@push('footer')
<script src="<?= __yesset('dist/summernote/summernote.min.js') ?>"></script>
@endpush


<!-- Plan Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Edit Plan') ?></h1>
	<!-- back button -->
	<a class="btn btn-light btn-sm" href="<?= route('manage.plan.view') ?>">
		<i class="fa fa-arrow-left" aria-hidden="true"></i> <?= __tr('Back to Plans') ?>
	</a>
	<!-- /back button -->
</div>
<!-- Start of Plan Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">

				<!-- plan edit form -->
				<form class="lw-ajax-form lw-form" method="post" action="<?= route('manage.plan.write.edit', ['planUId' => $planEditData['_uid']]) ?>">
					<!-- hidden _uid input field -->
					<input type="hidden" value="<?= $planEditData['_uid'] ?>" class="form-control" name="planUid">
					<!-- / hidden _uid input field -->

					<!-- title input field -->
					<div class="form-group">
						<label for="lwTitle"><?= __tr('Title') ?></label>
						<input type="text" value="<?= $planEditData['title'] ?>" id="lwTitle" class="form-control" name="title" required minlength="3">
					</div>
					<!-- / title input field -->

					<!-- price input field -->
					<div class="form-group">
						<label for="lwPrice"><?= __tr('Price') ?></label>
						<input type="number" min="0" value="<?= $planEditData['price'] ?>" id="lwPrice" class="form-control" name="price" required>
					</div>
					<!-- / price input field -->

					<!-- type input field -->
					<div class="form-group">
						<label for="lwType"><?= __tr('Type') ?></label>
						<select name="plan_type" class="form-control" id="lwType" required>
							@foreach($types as $planKey => $plan)
							<option value="<?= $planKey ?>" <?= (__ifIsset($planEditData['plan_type']) and $planKey == $planEditData['plan_type']) ? 'selected' : '' ?>><?= $plan ?></option>
							@endforeach
						</select>
					</div>
					<!-- / type input field -->

					<?php /**
					<!-- feature input field -->
					<div class="form-group">
						<label for="lwFeature"><?= __tr('Feature') ?></label>
						<select name="feature[]" class="form-control" id="lwFeature" multiple>
							@foreach($features as $featureKey => $feature)
							<option value="<?= $featureKey ?>" <?= (__ifIsset($planEditData['feature']) and (is_array($planEditData['feature'])) and in_array($featureKey, $planEditData['feature'])) ? 'selected' : '' ?>><?= $feature ?></option>
							@endforeach
						</select>
					</div>
					<!-- / feature input field -->
					**/ ?>

					<!-- description field -->
					<div class="form-group">
						<label for="lwDescription"><?= __tr('Description') ?></label>
						<textarea rows="4" cols="50" class="form-control summernote" name="description" id="lwDescription" required><?= $planEditData['description'] ?></textarea>
					</div>
					<!-- / description field -->

					<!-- status field -->
					<div class="custom-control custom-checkbox custom-control-inline">
						<input type="checkbox" class="custom-control-input" id="activeCheck" name="status" <?= $planEditData['status'] == 1 ? 'checked' : '' ?>>
						<label class="custom-control-label" for="activeCheck"><?= __tr('Active') ?></label>
					</div>
					<!-- / status field -->

					<br><br>
					<!-- update button -->
					<button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile"><?= __tr('Update') ?></button>
					<!-- / update button -->
				</form>
				<!-- / plan edit form -->
			</div>
		</div>
	</div>
</div>
<!-- End of Plan Wrapper -->