@section('forum-category-title', __tr("Add Kink"))
@section('head-title', __tr("Add Kink"))
@section('keywordName', strip_tags(__tr("Add Kink")))
@section('keyword', strip_tags(__tr("Add Kink")))
@section('description', strip_tags(__tr("Add Kink")))
@section('keywordDescription', strip_tags(__tr("Add Kink")))
@section('forum-category-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('forum-category-url', url()->current())

<!-- Forum Category Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Add Kink')  ?></h1>
	<!-- back button -->
	<a class="btn btn-light btn-sm" href="<?= route('manage.forum-category.view') ?>">
		<i class="fa fa-arrow-left" aria-hidden="true"></i> <?= __tr('Back to Kinks') ?>
	</a>
	<!-- /back button -->
</div>
<!-- Start of Forum Category Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">
				<!-- forum-category add form -->
				<form class="lw-ajax-form lw-form" method="post" action="<?= route('manage.forum-category.write.add') ?>">
					<!-- title input field -->
					<div class="form-group">
						<label for="lwTitle"><?= __tr('Title') ?></label>
						<input type="text" class="form-control" name="title" id="lwTitle" required minlength="3">
					</div>
					<!-- / title input field -->

					<!-- parent category input field -->
					<div class="form-group">
						<label for="lwParentCategory"><?= __tr('Parent Category') ?></label>
						<select name="parent_category" class="form-control" id="lwParentCategory">
							<option value="" selected><?= __tr('Choose your parent category') ?></option>
							@foreach($parentCategories as $parentCategoryKey => $parentCategory)
							<option value="<?= $parentCategoryKey ?>"><?= $parentCategory ?></option>
							@endforeach
						</select>
					</div>
					<!-- / parent category input field -->

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
				<!-- / forum-category add form -->
			</div>
		</div>
	</div>
</div>
<!-- End of Forum Category Wrapper -->