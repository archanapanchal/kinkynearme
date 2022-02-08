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
</style>
@endpush
@push('footer')
<script src="<?= __yesset('dist/summernote/summernote.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
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
                <h3><?= __tr('Forum Topic Detail') ?></h3>
            </div>
        </div>

        <!-- liked people container -->
        <div class="find-people-profile discussion-forum-detail-main">
            @if(!__isEmpty($forumTopicDetail))

             <a class="btn btn-light btn-sm back-button" style="float: right;" href="{{url('/user/').'/'.$forumTopicDetail['category']['_uid'].'/all-discussion-forum'}}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> <?= __tr('Back to Category Discussion Forum') ?>
            </a>
            </br></br>

            @php 
                $countForumTopicReplies = count($forumTopicDetail['replies']); 
                $getInterestData = fetchUserTopicInterestDetail($forumTopicDetail['_id']);
            @endphp

            <div class="row" id="lwForumTopicsContainer">

                <div class="discussion-forum-sub-section">
                    @if(!__isEmpty(getUserID()) && getUserID() == $forumTopicDetail['users__id'])
                        @if((getUserPlan(getUserID()) != config('constant.silver')) && getUserPlan(getUserID()) != config('constant.gold'))
                        <a class="discussion-forum-edit-button" title="<?= __tr('Edit Topic') ?>" href="#" data-toggle="modal" topicUId="{{$forumTopicDetail['_uid']}}">Edit Topic</a> 
                        <a class="discussion-forum-delete-button" title="<?= __tr('Delete Topic') ?>" href="#" data-toggle="modal" data-target="#lwDeleteTopicModel-{{$forumTopicDetail['_uid']}}">Delete Topic</a>
                        @endif

                        <div class="modal fade" id="lwEditTopicModel-{{$forumTopicDetail['_uid']}}" tabindex="-1" role="dialog" aria-labelledby="createTopicModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><?= __tr('Edit Topic') ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="user lw-ajax-form lw-form editTopicForm" method="post" action="<?= route('user.write.update_topic', ['forumTopicUId' => $forumTopicDetail['_uid']]) ?>">
                                            <input type="hidden" value="<?= $forumTopicDetail['_uid'] ?>" class="form-control" name="forumTopicUid">
                                            <input type="hidden" value="<?= $forumTopicDetail['_id'] ?>" class="form-control" name="topic_id">
                                            <div class="form-group">
                                                <label for="lwTitle"><?= __tr('Title') ?></label>
                                                <input type="text" class="form-control" name="title" id="lwTitle" required minlength="3" value="{{$forumTopicDetail['title']}}">
                                            </div>
                                            <div class="form-group">
                                                <label for="lwForumCategory"><?= __tr('Forum Category') ?></label>
                                                <select name="category_id" class="form-control" id="lwForumCategory" required>
                                                    <option value="" selected><?= __tr('Choose forum category') ?></option>
                                                    @foreach($forumCatgories as $forumCategoryKey => $forumCategory)
                                                    <option value="<?= $forumCategoryKey ?>" <?= (__ifIsset($forumTopicDetail['category_id']) and $forumCategoryKey == $forumTopicDetail['category_id']) ? 'selected' : '' ?>><?= $forumCategory ?></option>
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
                                                    if($forumTopicDetail['image'] != ""){
                                                        $forumImageUrl = getMediaUrl($forumImageFolderPath, $forumTopicDetail['image']);
                                                    }
                                                ?>
                                                <img class="lw-profile-thumbnail lw-photoswipe-gallery-img lw-lazy-img lwProfilePictureStaticImage" data-src="<?= imageOrNoImageAvailable($forumImageUrl) ?>">
                                                <input type="hidden" name="forum_images" class="lw-uploaded-file-front">       
                                            </div>

                                            <div class="form-group">
                                                <label for="lwDescription"><?= __tr('Description') ?></label>
                                                <textarea rows="2" cols="30" class="form-control summernote" id="lwDescription" name="description" required><?= $forumTopicDetail['description'] ?></textarea>
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

                        <div class="modal fade" id="lwDeleteTopicModel-{{$forumTopicDetail['_uid']}}" tabindex="-1" role="dialog" aria-labelledby="deleteTopicModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><?= __tr('Delete topic?') ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="user lw-ajax-form lw-form" method="post" action="{{url('/user/').'/'.$forumTopicDetail['_uid'].'/delete-forum-topic'}}">
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
                <div class="col-lg-12 mb-5 load-forum-topic" id="forum-topic-<?= $forumTopicDetail['_id'] ?>">
                    {{$forumTopicDetail['title']}}
                </div>
                <div class="forum-image">
                    <?php 
                        $forumImageFolderPath = getPathByKey('forum_images', ['{_uid}' => authUID()]);
                        $forumImageUrl = noThumbImageURL();
                        if($forumTopicDetail['image'] != ""){
                            $forumImageUrl = getMediaUrl($forumImageFolderPath, $forumTopicDetail['image']);
                        }
                    ?>
                    <img class="lw-profile-thumbnail lw-photoswipe-gallery-img lw-lazy-img" data-src="<?= imageOrNoImageAvailable($forumImageUrl) ?>">
                </div>
                
                <div class="col-lg-12 mb-2 load-topics" id="topics-<?= $forumTopicDetail['_id'] ?>">
                    <span class="topic-description"><p>{!! $forumTopicDetail['description'] !!}</p></span>
                </div>
                <div class="col-lg-12 topic-category  mb-3"><b>Category - </b> {{$forumTopicDetail['category']['title']}}</div>
                
            </div>
            @if(!__isEmpty($forumTopicDetail['forum_interests']))
                <div class="all-forum-interests mb-3">
                    <b>Interest/Kinks - </b>
                    <?php $forumInterest = ''; ?>
                    @foreach($forumTopicDetail['forum_interests'] as $forumInterests)
                        <?php $forumInterest .= $forumInterests['interest']['title'] .',' ;?>
                    @endforeach
                    <span class="topic-interests"> {{ preg_replace('/(?<!,) {2,}/', ' ', rtrim($forumInterest, ',')) }} </span>
                </div>
            @endif
            <div class="forum-replies-section mb-3">
                <div class="replydiv">
                    <h5>Replies ({{$countForumTopicReplies}})</h5>
                    @if((getUserPlan(getUserID()) == config('constant.gold')) || (getUserPlan(getUserID()) == config('constant.platinum')) )
                    <a class="btn btn-secondary btn-sm discussion-forum-button" title="<?= __tr('Reply') ?>" href="#" data-toggle="modal" data-target="#lwReplyToTopicModel"><?= __tr('Reply to this topic') ?></a>
                    @endif
                </div>
                <div class="forum-reply mb-2">
                    @if(!__isEmpty($forumTopicDetail['replies']))
                        @foreach($forumTopicDetail['replies'] as $reply)
                            <?php 
                                $profilePictureFolderPath = getPathByKey('profile_photo', ['{_uid}' => $reply['user_detail']['_uid']]);
                                $profilePictureUrl = noThumbImageURL();
                                if($reply['user_detail']['user_profile']['profile_picture'] != ""){
                                    $profilePictureUrl = getMediaUrl($profilePictureFolderPath, $reply['user_detail']['user_profile']['profile_picture']);
                                }
                            ?>
                            <div class="userdetmain">
                                <img height="50" width="50" class="lw-profile-thumbnail lw-photoswipe-gallery-img lw-lazy-img" id="lwProfilePictureStaticImage" src="<?= imageOrNoImageAvailable($profilePictureUrl) ?>">
                                <div class="userdet">
                                    <span class="reply-user-name">{{ $reply['user_detail']['username']}}</span>
                                    <span class="reply-comment">{!! $reply['comment'] !!}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif      

                </div>
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

<div class="modal fade" id="lwReplyToTopicModel" tabindex="-1" role="dialog" aria-labelledby="replyToTopicModel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= __tr('Reply') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="user forum-reply-topic-main lw-ajax-form lw-form" method="post" action="<?= route('user.write.reply-to-topic') ?>">
                    <input type="hidden" name="topic_id" value="{{$forumTopicDetail['_id']}}">
                    <input type="hidden" name="topic_title" value="{{$forumTopicDetail['title']}}">
                    <div class="form-group forum-topic-comment">
                        <label for="lwComment"><?= __tr('Comment') ?></label>
                        <textarea rows="2" cols="30" class="form-control summernote" id="lwComment" name="comment" required></textarea>
                    </div>
                    <button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block-on-mobile"><?= __tr('Reply')  ?></button>
                </form>
            </div>
        </div>
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