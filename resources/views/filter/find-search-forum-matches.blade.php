<?php 

 // echo "<pre>";
 //    print_r($search_forum_data);exit(); ?>

 <style type="text/css">
    .all-forum-interests{
        margin-left: 15px;
    }

</style>

@if(!__isEmpty($search_forum_data))

<div class="row" id="lwForumTopicsContainer">
    @foreach($search_forum_data as $topic)
        <?php 
            $getInterestData = fetchUserTopicInterestDetail($topic['_id']); 
            $topicCount = $topic['reply_count'];
            $topicViews = $topic['view_count'];
        ?>
        <div class="discussion-forum-main">
            <a id="forumtopictitle" topic_id="{{$topic['_id']}}" href="{{url('/user/').'/'.$topic['_uid'].'/topic-detail'}}">
                <div class="col-lg-12 mb-2 load-topics" id="topics-<?= $topic['_id'] ?>">
                    <span class="topic-title"><b>{{$topic['title']}}</b></span> 
                   
                    <span class="topic-description"><p>{!! strlen(strip_tags($topic['description'])) > 50 ? substr(strip_tags($topic['description']),0,100)."..." : strip_tags($topic['description']) !!}</p></span>
                    <span class="forum-topic-username"><h6>By  @if($topic['user_detail']['_id'] == 1) {{"Kinkynearme"}} @else {{$topic['user_detail']['username'] ?? ""}} @endif</h6></span>
                    <span class="forum-topic-username"><b>Category - </b> {{$topic['category']['title'] }}</span>
                </div>
                @if(!__isEmpty($topic['forum_interests']))
                <div class="all-forum-interests">
                    <b>Interest/Kinks - </b>
                    <?php $forumInterest = ''; ?>
                    @foreach($topic['forum_interests'] as $forumInterests)
                        <?php $forumInterest .= $forumInterests['interest']['title'] .',' ;?>
                    @endforeach
                    <span class="topic-interests"> {{ preg_replace('/(?<!,) {2,}/', ' ', rtrim($forumInterest, ',')) }} </span>
                </div>
                @endif
                <div class="views-replies-count mb-3">
                    <span class="forum-views-count">VIEWS ({{$topicViews}})</span>
                    <span class="forum-reply-count">REPLIES ({{$topicCount}})</span>
                </div>
            </a>
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

