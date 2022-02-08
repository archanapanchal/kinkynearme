@section('forum-topic-title', __tr("Edit Forum Topics"))
@section('head-title', __tr("Edit Forum Topics"))
@section('keywordName', strip_tags(__tr("Edit Forum Topics")))
@section('keyword', strip_tags(__tr("Edit Forum Topics")))
@section('description', strip_tags(__tr("Edit Forum Topics")))
@section('keywordDescription', strip_tags(__tr("Edit Forum Topics")))
@section('forum-topic-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('forum-topic-url', url()->current())

@push('header')
<link rel="stylesheet" href="<?= __yesset('dist/summernote/summernote.min.css') ?>" />
<link rel="stylesheet" href="<?= __yesset('dist/css/select2.min.css') ?>" />
@endpush
@push('footer')
<script src="<?= __yesset('dist/summernote/summernote.min.js') ?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
       $('.multiple-kinks').select2();
   });
</script>
@endpush
<!-- Forum Category Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Edit Forum Topics') ?></h1>
	<!-- back button -->
	<a class="btn btn-light btn-sm" href="<?= route('manage.forum-topic.view') ?>">
		<i class="fa fa-arrow-left" aria-hidden="true"></i> <?= __tr('Back to Forum Topics') ?>
	</a>
	<!-- /back button -->
</div>
<!-- Start of Forum Category Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<div class="card mb-4">
			<div class="card-body">

				<!-- forum-topic edit form -->
				<form class="lw-ajax-form lw-form" method="post" action="<?= route('manage.forum-topic.write.edit', ['forumTopicUId' => $forumTopicEditData['_uid']]) ?>">
					<!-- hidden _uid input field -->
					<input type="hidden" value="<?= $forumTopicEditData['_uid'] ?>" class="form-control" name="forumTopicUid">
					<input type="hidden" value="<?= $forumTopicEditData['_id'] ?>" class="form-control" name="topic_id">
					<!-- / hidden _uid input field -->
					<!-- title input field -->
					<div class="form-group">
						<label for="lwTitle"><?= __tr('Title') ?></label>
						<input type="text" value="<?= $forumTopicEditData['title'] ?>" id="lwTitle" class="form-control" name="title" required minlength="3">
					</div>
					<!-- / title input field -->

					<!-- parent topic input field -->
					<div class="form-group">
						<label for="lwParentCategory"><?= __tr('Forum Category') ?></label>
						<select name="category_id" class="form-control" id="lwParentCategory" required>
							<option value="" selected><?= __tr('Choose forum category') ?></option>
							@foreach($forumCategories as $forumCategoryKey => $forumCategory)
							<option value="<?= $forumCategoryKey ?>" <?= (__ifIsset($forumTopicEditData['category_id']) and $forumCategoryKey == $forumTopicEditData['category_id']) ? 'selected' : '' ?>><?= $forumCategory ?></option>
							@endforeach
						</select>
					</div>
					<!-- / parent topic input field -->

					<div class="form-group">
						<label for="lwInterest"><?= __tr('Your Interests/Kinks') ?></label>
						<select name="interest_id[]" class="form-control multiple-kinks" id="lwInterest" required multiple="multiple">
							@foreach($interestsList as $interestKey => $interest)
							<option value="<?= $interest['_id'] ?>" <?= (__ifIsset($getInterestData) and in_array($interest['_id'], $getInterestData)) ? 'selected' : '' ?>><?= $interest['title'] ?></option>
							@endforeach
						</select>
					</div>		
					<div class="form-group">
						<label><?= __tr('Upload Forum Images') ?></label>
						<input type="file" class="lw-file-uploader" data-instant-upload="true" data-action="<?= route('media.forum.upload_temp_media') ?>" data-remove-media="true" data-callback="afterUploadedFile" data-allow-image-preview="false">
 						<input type="hidden" name="forum_images" class="lw-uploaded-file">
 						@if(!__isEmpty($forumTopicEditData['image']))
 						<div class="col-lg-6" id="lwStickerImagePreview">
 							<img class="lw-sticker-preview-image lw-uploaded-preview-img" src="<?= $forumTopicEditData['image'] ?>">
 						</div>
 						@endif
					</div>				

					<!-- description field -->
					<div class="form-group">
						<label for="lwDescription"><?= __tr('Description') ?></label>
						<textarea rows="4" cols="50" class="form-control summernote" name="description" id="lwDescription" required><?= $forumTopicEditData['description'] ?></textarea>
					</div>
					<!-- / description field -->

					<!-- status field -->
					<div class="custom-control custom-checkbox custom-control-inline">
						<input type="checkbox" class="custom-control-input" id="activeCheck" name="status" <?= $forumTopicEditData['status'] == 1 ? 'checked' : '' ?>>
						<label class="custom-control-label" for="activeCheck"><?= __tr('Active') ?></label>
					</div>
					<!-- / status field -->

					<br><br>
					<!-- update button -->
					<button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile"><?= __tr('Update') ?></button>
					<!-- / update button -->
				</form>
				<!-- / forum-topic edit form -->
			</div>
		</div>
	</div>
</div>
@push('appScripts')
<script>
	function afterUploadedFile(responseData) {
 		if (responseData.reaction == 1) {
 			$("#lwStickerImagePreview").show();
 			$('.lw-sticker-preview-image').attr('src', responseData.data.path);
 		}
 	}
</script>
@endpush
<!-- End of Forum Topic Wrapper -->