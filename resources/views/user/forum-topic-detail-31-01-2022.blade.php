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
@endpush
@push('footer')
<script src="<?= __yesset('dist/summernote/summernote.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
       $('.multiple-kinks-add').select2();
       $('.multiple-kinks-edit').select2();
   });
</script>
@endpush
<style type="text/css">
   /* .load-forum-topic{
        background-color: gray;
        color: #ffffff;
        font-weight: bold;
    }

    .topic-interests{
        margin-right: 15px;
        background-color: lightgray;
    }

    .select2-container{
        display: block !important;
    }

    .all-forum-interests{
        margin-left: 15px;
    }

    a.btn-secondary{
        color: #ffffff;
    }

    .forum-replies-section .forum-reply{
        margin-left: 40px;
    }

    .reply-user-name{
        font-weight: bold;
    }

    .topic-category{
        margin-left: 20px;
    }
*/
</style>

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
            @endphp

            <div class="row" id="lwForumTopicsContainer">
                <div class="col-lg-12 mb-5 load-forum-topic" id="forum-topic-<?= $forumTopicDetail['_id'] ?>">
                    {{$forumTopicDetail['title']}}
                </div>
                <?php $getInterestData = fetchUserTopicInterestDetail($forumTopicDetail['_id']); ?>
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
                <form class="user lw-ajax-form lw-form" method="post" action="<?= route('user.write.reply-to-topic') ?>">
                    <input type="hidden" name="topic_id" value="{{$forumTopicDetail['_id']}}">
                    <input type="hidden" name="topic_title" value="{{$forumTopicDetail['title']}}">
                    <div class="form-group">
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
<script type="text/javascript">
   

</script>
@endpush