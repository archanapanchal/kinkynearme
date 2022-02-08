@section('page-title', __tr('Forum'))
@section('head-title', __tr('Forum'))
@section('keywordName', __tr('Forum'))
@section('keyword', __tr('Forum'))
@section('description', __tr('Forum'))
@section('keywordDescription', __tr('Forum'))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())
@push('header')
<link rel="stylesheet" href="<?= __yesset('dist/summernote/summernote.min.css') ?>" />
<link rel="stylesheet" href="<?= __yesset('dist/css/select2.min.css') ?>" />
<style type="text/css">
</style>
@endpush
@push('footer')
<script src="<?= __yesset('dist/summernote/summernote.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
       $('.multiple-kinks-add').select2();
       $('.multiple-kinks-edit').select2();
       
   });

    $(function () {

    /* Summernote Validation */

    var summernoteFormAdd = $('.createTopicForm');
    var summernoteElement = $('.summernote');

    var summernoteValidator1 = summernoteFormAdd.validate({
        errorElement: "div",
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        ignore: ':hidden:not(.summernote),.note-editable.card-block',
        errorPlacement: function (error, element) {
            // Add the `help-block` class to the error element
            error.addClass("invalid-feedback");
            console.log(element);
            if (element.prop("type") === "checkbox") {
                error.insertAfter(element.siblings("label"));
            } else if (element.hasClass("summernote")) {
                error.insertAfter(element.siblings(".note-editor"));
            } else {
                error.insertAfter(element);
            }
        }
    });

    summernoteElement.summernote({
        height: 300,
        callbacks: {
            onChange: function (contents, $editable) {
                // Note that at this point, the value of the `textarea` is not the same as the one
                // you entered into the summernote editor, so you have to set it yourself to make
                // the validation consistent and in sync with the value.
                summernoteElement.val(summernoteElement.summernote('isEmpty') ? "" : contents);

                // You should re-validate your element after change, because the plugin will have
                // no way to know that the value of your `textarea` has been changed if the change
                // was done programmatically.
                summernoteValidator1.element(summernoteElement);
            }
        }
    });

});

$(document).on('click','#discussion-forum-button',function(){
    $('#lwCreateTopicModel').modal('show');
    $('.filepond--drop-label , .filepond--list-scroller, .filepond--panel, .filepond--assistant, .filepond--data, .filepond--drip').addClass('hideanimation ');

})


$(document).on('click','.discussion-forum-edit-button',function(){
    var topicUId = $(this).attr('topicUId');

    $('#lwEditTopicModel-'+topicUId).modal('show');
    $('.filepond--drop-label , .filepond--list-scroller, .filepond--panel, .filepond--assistant, .filepond--data, .filepond--drip').addClass('hideanimation ');

})

</script>
@endpush
<!-- Page Heading -->
<div class="tab-pane active" id="tabs-1" role="tabpanel">
    <div class="find-people-tab tab-inner">
        <div class="tab-heading">
            <div class="tab-left-side">
                <h3><?= __tr('Forum Topics') ?></h3>
            </div>
            @if(getUserPlan(getUserID()) == config('constant.platinum'))
                <a class="btn btn-secondary btn-sm discussion-forum-button" id="discussion-forum-button" title="<?= __tr('Create Topic') ?>" href="#" data-toggle="modal"><?= __tr('Create Topic') ?></a>
            @endif
        </div>

        <!-- liked people container -->
        <div class="find-people-profile">
            @if(!__isEmpty($forumCategoriesTopics))

            <!-- <a class="btn btn-secondary btn-sm discussion-forum-button" title="<?= __tr('Create Topic') ?>" href="#" data-toggle="modal" data-target="#lwCreateTopicModel"><?= __tr('Create Topic') ?></a>
            </br></br> -->

            <div class="row" id="lwForumTopicsContainer">
                @foreach($forumCategoriesTopics as $categories)
                    <div class="col-lg-12 mb-5 load-categories" id="categories-<?= $categories['_id'] ?>">
                        {{$categories['title']}}
                        
                    </div>
                    @php 
                        $countForumTopic = count($categories['forum_topics']); 
                        $twoTopicList = array_slice($categories['forum_topics'], 0, 3, true);
                    @endphp 

                    @foreach($twoTopicList as $topics)
                        <?php 
                            $getInterestData = fetchUserTopicInterestDetail($topics['_id']); 
                            $topicCount = count($topics['replies']);
                            $topicViews = $topics['view_count'];
                        ?>
                        <div class="discussion-forum-main">
                            <a id="forumtopictitle" topic_id="{{$topics['_id']}}" href="{{url('/user/').'/'.$topics['_uid'].'/topic-detail'}}">
                                <div class="col-lg-12 mb-2 load-topics" id="topics-<?= $topics['_id'] ?>">
                                    <span class="topic-title"><b>{{$topics['title']}}</b></span> 
                                   
                                    <span class="topic-description"><p>{!! strlen(strip_tags($topics['description'])) > 50 ? substr(strip_tags($topics['description']),0,100)."..." : strip_tags($topics['description']) !!}</p></span>
                                    <span class="forum-topic-username"><h6>By  @if($topics['user_detail']['_id'] == 1) {{"Kinkynearme"}} @else {{$topics['user_detail']['username'] ?? ""}} @endif</h6></span>
                                </div>
                                <div class="views-replies-count mb-3">
                                    <span class="forum-views-count">VIEWS ({{$topicViews}})</span>
                                    <span class="forum-reply-count">REPLIES ({{$topicCount}})</span>
                                </div>
                            </a>
                            <div class="discussion-forum-sub-section">
                                @if(!__isEmpty(getUserID()) && getUserID() == $topics['users__id'])
                                    @if(getUserPlan(getUserID()) == config('constant.platinum'))
                                    <a class="discussion-forum-edit-button" title="<?= __tr('Edit Topic') ?>" href="#" data-toggle="modal" topicUId="{{$topics['_uid']}}">Edit Topic</a> 
                                    <a class="discussion-forum-delete-button" title="<?= __tr('Delete Topic') ?>" href="#" data-toggle="modal" data-target="#lwDeleteTopicModel-{{$topics['_uid']}}">Delete Topic</a>
                                    @endif

                                    <div class="modal fade" id="lwEditTopicModel-{{$topics['_uid']}}" tabindex="-1" role="dialog" aria-labelledby="createTopicModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"><?= __tr('Edit Topic') ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="user lw-ajax-form lw-form editTopicForm" method="post" action="<?= route('user.write.update_topic', ['forumTopicUId' => $topics['_uid']]) ?>">
                                                        <input type="hidden" value="<?= $topics['_uid'] ?>" class="form-control" name="forumTopicUid">
                                                        <input type="hidden" value="<?= $topics['_id'] ?>" class="form-control" name="topic_id">
                                                        <div class="form-group">
                                                            <label for="lwTitle"><?= __tr('Title') ?></label>
                                                            <input type="text" class="form-control" name="title" id="lwTitle" required minlength="3" value="{{$topics['title']}}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="lwForumCategory"><?= __tr('Forum Category') ?></label>
                                                            <select name="category_id" class="form-control" id="lwForumCategory">
                                                                <option value="" selected><?= __tr('Choose forum category') ?></option>
                                                                @foreach($forumCatgories as $forumCategoryKey => $forumCategory)
                                                                <option value="<?= $forumCategoryKey ?>" <?= (__ifIsset($topics['category_id']) and $forumCategoryKey == $topics['category_id']) ? 'selected' : '' ?>><?= $forumCategory ?></option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="lwInterestEdit"><?= __tr('Your Interests/Kinks') ?></label>
                                                            <select name="interest_id[]" class="form-control multiple-kinks-edit" id="lwInterestEdit" multiple="multiple">
                                                                @foreach($interestsList as $interestKey => $interest)
                                                                <option value="<?= $interest['_id'] ?>" <?= (__ifIsset($getInterestData) and in_array($interest['_id'], $getInterestData)) ? 'selected' : '' ?>><?= $interest['title'] ?></option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><?= __tr('Upload Forum Images') ?></label>
                                                            <a href="#" class="btn btn-outline-secondary bg-color-red choosefilebtn forum-file-upload-button">Upload<input type="file" name="forum_images" class="forum_images lw-file-uploader" id="lwFileUploader" data-remove-media="true" data-instant-upload="true" data-action="<?= route('user.write.upload_temp_media_front') ?>" data-label-idle="<?= __tr("Drag & Drop your picture or __browseAction__", ['__browseAction__' => "<span class='forum_images--label-action'>" . __tr('Browse') . "</span>"]) ?>" data-image-preview-height="170" data-image-crop-aspect-ratio="1:1" data-style-panel-layout="compact circle" data-style-load-indicator-position="center bottom" data-style-progress-indicator-position="right bottom" data-style-button-remove-item-position="left bottom" data-style-button-process-item-position="right bottom" data-callback="afterUploadedFile"></a>
                                                            <?php 
                                                                $forumImageFolderPath = getPathByKey('forum_images', ['{_uid}' => authUID()]);
                                                                $forumImageUrl = noThumbImageURL();
                                                                if($topics['image'] != ""){
                                                                    $forumImageUrl = getMediaUrl($forumImageFolderPath, $topics['image']);
                                                                }
                                                            ?>
                                                            <img height="166" width="240" class="lw-profile-thumbnail lw-photoswipe-gallery-img lw-lazy-img lwProfilePictureStaticImage" data-src="<?= imageOrNoImageAvailable($forumImageUrl) ?>">
                                                            <input type="hidden" name="forum_images" class="lw-uploaded-file-front">       
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="lwDescription"><?= __tr('Description') ?></label>
                                                            <textarea rows="2" cols="30" class="form-control summernote" id="lwDescription" name="description" required data-msg="This field is required."><?= $topics['description'] ?></textarea>
                                                        </div>
                                                        <input type="hidden" class="custom-control-input" name="status" value="on">
                                                        <button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block-on-mobile"><?= __tr('Update Topic')  ?></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="lwDeleteTopicModel-{{$topics['_uid']}}" tabindex="-1" role="dialog" aria-labelledby="deleteTopicModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"><?= __tr('Delete topic?') ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="user lw-ajax-form lw-form" method="post" action="{{url('/user/').'/'.$topics['_uid'].'/delete-forum-topic'}}">
                                                        <?= __tr('Are you sure you want to delete this topic?') ?>
                                                        <hr />
                                                        <button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block-on-mobile"><?= __tr('Delete Topic')  ?></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if($countForumTopic > 2)
                        <div class="forum-topic-view-all discussion-forum-button">
                            <a href="{{url('/user/').'/'.$categories['_uid'].'/all-discussion-forum'}}" class="btn btn-secondary btn-sm" title="<?= __tr('View All') ?>" href="#"><?= __tr('View All') ?></a>
                        </div></br></br>
                    @endif
                @endforeach

            </div>
            @else
            <!-- info message -->
            <div class="alert alert-info">
                <?= __tr('Forum Topic not found.') ?>
            </div>
            <!-- / info message -->
            @endif
        </div>
        <!-- / liked people container -->

    </div>
</div>

<div class="modal fade" id="lwCreateTopicModel" tabindex="-1" role="dialog" aria-labelledby="createTopicModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                
                <h5 class="modal-title"><?= __tr('Create Topic') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="user lw-ajax-form lw-form createTopicForm" method="post" action="<?= route('user.write.create_topic') ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="lwTitle"><?= __tr('Title') ?></label>
                        <input type="text" class="form-control" name="title" id="lwTitle" required minlength="3">
                    </div>
                    <div class="form-group">
                        <label for="lwForumCategory"><?= __tr('Forum Category') ?></label>
                        <select name="category_id" class="form-control" id="lwForumCategory" required>
                            <option value="" selected><?= __tr('Choose forum category') ?></option>
                            @foreach($forumCatgories as $forumCategoryKey => $forumCategory)
                            <option value="<?= $forumCategoryKey ?>"><?= $forumCategory ?></option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="lwInterestAdd"><?= __tr('Your Interests/Kinks') ?></label>
                        <select name="interest_id[]" class="form-control multiple-kinks-add" id="lwInterestAdd" multiple="multiple">
                            @foreach($interestsList as $interestKey => $interest)
                            <option value="<?= $interest['_id'] ?>"><?= $interest['title'] ?></option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= __tr('Upload Forum Image') ?></label>
                        <a href="#" class="btn btn-outline-secondary bg-color-red forum-file-upload-button">Upload<input type="file" name="forum_images" class="forum_images lw-file-uploader" id="lwFileUploader" data-remove-media="true" data-instant-upload="true" data-action="<?= route('user.write.upload_temp_media_front') ?>" data-label-idle="<?= __tr("Drag & Drop your picture or __browseAction__", ['__browseAction__' => "<span class='forum_images--label-action'>" . __tr('Browse') . "</span>"]) ?>" data-image-preview-height="170" data-image-crop-aspect-ratio="1:1" data-style-panel-layout="compact circle" data-style-load-indicator-position="center bottom" data-style-progress-indicator-position="right bottom" data-style-button-remove-item-position="left bottom" data-style-button-process-item-position="right bottom" data-callback="afterUploadedFile"></a>
                        <img class="lw-profile-thumbnail lw-photoswipe-gallery-img lw-lazy-img lwProfilePictureStaticImage" data-src="">
                        <input type="hidden" name="forum_images" class="lw-uploaded-file-front">       
                    </div>

                    <div class="form-group">
                        <label for="lwDescription"><?= __tr('Description') ?></label>
                        <textarea rows="2" cols="30" class="form-control summernote" id="lwDescription" name="description" required data-msg="This field is required."></textarea>
                    </div>
                    <input type="hidden" class="custom-control-input" name="status" value="on">
                    <button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block-on-mobile"><?= __tr('Create Topic')  ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('appScripts')
<script>
 new WOW().init();
</script>
<script>
    function afterUploadedFile(responseData) {

        console.log(responseData.data);
      $('.lwProfilePictureStaticImage, .lw-profile-thumbnail').attr('src', responseData.data.path);
      $('.lw-uploaded-file-front').val(responseData.data.fileName);

   }
</script>
@endpush