@section('page-title', __tr('Discussion Forum'))
@section('head-title', __tr('Discussion Forum'))
@section('keywordName', __tr('Discussion Forum'))
@section('keyword', __tr('Discussion Forum'))
@section('description', __tr('Discussion Forum'))
@section('keywordDescription', __tr('Discussion Forum'))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())
@push('header')
<link rel="stylesheet" href="<?= __yesset('dist/summernote/summernote.min.css') ?>" />
<link rel="stylesheet" href="<?= __yesset('dist/css/select2.min.css') ?>" />
<style type="text/css">
    /*.filepond--root.forum_images{
        height: 0px !important;
    }
    .filepond--drop-label{
        display: none;
    }*/

    /*.filepond--browser{
        display: none;
    }*/
</style>
@endpush
@push('footer')
<script src="<?= __yesset('dist/summernote/summernote.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
       $('.multiple-kinks-add').select2();
       $('.multiple-kinks-edit').select2();
   });
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
                <h3><?= __tr('Forum Topic - '.$forumCategoriesTopics['title']) ?></h3>
            </div>
        </div>

        <!-- liked people container -->
        <div class="find-people-profile discussion-forum-cat-main">
            @if(!__isEmpty($forumCategoriesTopics))

            <a class="btn btn-light btn-sm back-button" style="float: right;" href="<?= route('user.view.discussion-forum') ?>">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> <?= __tr('Back to Discussion Forum') ?>
            </a>

            </br></br>

            <div class="row" id="lwForumTopicsContainer">
                <div class="col-lg-12 mb-5 load-categories" id="categories-<?= $forumCategoriesTopics['_id'] ?>">
                    {{$forumCategoriesTopics['title']}}
                    
                </div>

                @foreach($forumCategoriesTopics['forum_topics'] as $topics)
                    <?php 
                        $getInterestData = fetchUserTopicInterestDetail($topics['_id']); 
                        $topicCount = count($topics['replies']);
                        $topicViews = $topics['view_count'];
                    ?>
                    <div class="disucssion-forum-cat-list">
                        <a id="forumtopictitle" topic_id="{{$topics['_id']}}" href="{{url('/user/').'/'.$topics['_uid'].'/topic-detail'}}">
                            <div class="col-lg-12 mb-2 load-topics" id="topics-<?= $topics['_id'] ?>">
                                <span class="topic-title"><b>{{$topics['title']}}</b></span>
                                <span class="topic-description"><p>{!! strlen(strip_tags($topics['description'])) > 50 ? substr(strip_tags($topics['description']),0,150)."..." : strip_tags($topics['description']) !!}</p></span>
                            </div>
                            @if(!__isEmpty($topics['forum_interests']))
                                <div class="col-lg-12 all-forum-interests mb-2">
                                    <b>Interest/Kinks - </b>
                                <?php $forumInterest = ''; ?>
                                @foreach($topics['forum_interests'] as $forumInterests)
                                    <?php $forumInterest .= $forumInterests['interest']['title'] .',' ;?>
                                @endforeach
                                    <span class="topic-interests">{{ preg_replace('/(?<!,) {2,}/', ' ', rtrim($forumInterest, ',')) }} </span>
                                </div>
                            @endif
                            <div class="views-replies-count">
                                <span class="forum-views-count">VIEWS ({{$topicViews}})</span>
                                <span class="forum-reply-count">REPLIES ({{$topicCount}})</span>
                            </div>
                        </a>
                        <div class="discussion-forum-sub-section">
                            @if(!__isEmpty(getUserID()) && getUserID() == $topics['users__id'])
                                @if((getUserPlan(getUserID()) != config('constant.silver')) && getUserPlan(getUserID()) != config('constant.gold'))
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
                                                        <select name="category_id" class="form-control" id="lwForumCategory" required>
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
                                                        <textarea rows="2" cols="30" class="form-control summernote" id="lwDescription" name="description" required><?= $topics['description'] ?></textarea>
                                                    </div>
                                                    <div class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="hidden" class="custom-control-input" name="status" value="on">
                                                    </div>
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

@push('appScripts')
<script>
    function afterUploadedFile(responseData) {
        console.log(responseData.data);
      $('.lwProfilePictureStaticImage, .lw-profile-thumbnail').attr('src', responseData.data.path);
      $('.lw-uploaded-file-front').val(responseData.data.fileName);

   }
</script>
@endpush