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
    .load-categories{
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

    .views-replies-count{
        font-weight: bold;
    }

    .topic-title a{
        color: #000000 !important;
    }

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
        <div class="find-people-profile">
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
                    <div class="col-lg-12 mb-2 load-topics" id="topics-<?= $topics['_id'] ?>">
                        <span class="topic-title"><b><a id="forumtopictitle" topic_id="{{$topics['_id']}}" href="{{url('/user/').'/'.$topics['_uid'].'/topic-detail'}}">{{$topics['title']}}</a></b></span>
                        <span class="topic-description"><p>{!! strlen(strip_tags($topics['description'])) > 50 ? substr(strip_tags($topics['description']),0,150)."..." : strip_tags($topics['description']) !!}</p></span>
                    </div>
                    @if(!__isEmpty($topics['forum_interests']))
                        <div class="all-forum-interests mb-2">
                        @foreach($topics['forum_interests'] as $forumInterests)
                            <span class="topic-interests">{{ $forumInterests['interest']['title'] }} </span>
                        @endforeach
                        </div>
                    @endif
                    <div class="views-replies-count">
                        <span class="forum-views-count">VIEWS ({{$topicViews}})</span>
                        <span class="forum-reply-count">REPLIES ({{$topicCount}})</span>
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
<script type="text/javascript">
   

</script>
@endpush