@section('forum-category-title', __tr("Edit Forum Category"))
@section('head-title', __tr("Edit Forum Category"))
@section('keywordName', strip_tags(__tr("Edit Forum Category")))
@section('keyword', strip_tags(__tr("Edit Forum Category")))
@section('description', strip_tags(__tr("Edit Forum Category")))
@section('keywordDescription', strip_tags(__tr("Edit Forum Category")))
@section('forum-category-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('forum-category-url', url()->current())

<!-- Forum Category Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Edit Forum Category') ?></h1>
	<!-- back button -->
	<a class="btn btn-light btn-sm" href="<?= route('manage.forum-category.view') ?>">
		<i class="fa fa-arrow-left" aria-hidden="true"></i> <?= __tr('Back to Forum Category') ?>
	</a>
	<!-- /back button -->
</div>
<!-- Start of Forum Category Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">

				<!-- forum-category edit form -->
				<form class="lw-ajax-form lw-form" method="post" action="<?= route('manage.forum-category.write.edit', ['forumCategoryUId' => $forumCategoryEditData['_uid']]) ?>">
					<!-- hidden _uid input field -->
					<input type="hidden" value="<?= $forumCategoryEditData['_uid'] ?>" class="form-control" name="forumCategoryUid">
					<!-- / hidden _uid input field -->

					<!-- title input field -->
					<div class="form-group">
						<label for="lwTitle"><?= __tr('Title') ?></label>
						<input type="text" value="<?= $forumCategoryEditData['title'] ?>" id="lwTitle" class="form-control" name="title" required minlength="3">
					</div>
					<!-- / title input field -->

					<!-- parent category input field -->
					<!-- <div class="form-group">
						<label for="lwParentCategory"><?= __tr('Parent Category') ?></label>
						<select name="parent_category" class="form-control" id="lwParentCategory">
							<option value="" selected><?= __tr('Choose your parent category') ?></option>
							@foreach($parentCategories as $parentCategoryKey => $parentCategory)
							<option value="<?= $parentCategoryKey ?>" <?= (__ifIsset($forumCategoryEditData['parent_category']) and $parentCategoryKey == $forumCategoryEditData['parent_category']) ? 'selected' : '' ?>><?= $parentCategory ?></option>
							@endforeach
						</select>
					</div> -->
					<!-- / parent category input field -->

					<!-- status field -->
					<div class="custom-control custom-checkbox custom-control-inline" style="display: none">
						<input type="checkbox" class="custom-control-input" id="activeCheck" name="status" <?= $forumCategoryEditData['status'] == 1 ? 'checked' : '' ?>>
						<label class="custom-control-label" for="activeCheck"><?= __tr('Active') ?></label>
					</div>
					<!-- / status field -->

					<br><br>
					<!-- update button -->
					<button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile"><?= __tr('Update') ?></button>
					<!-- / update button -->
				</form>
				<!-- / forum-category edit form -->
			</div>
		</div>
	</div>
</div>
<!-- End of Forum Category Wrapper -->