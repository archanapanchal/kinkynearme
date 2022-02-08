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

    .topic-title a{
        color: #000000 !important;
    }

    .views-replies-count{
        font-weight: bold;
        margin-left: 15px;
    }

    .forum-topic-view-all{
        margin-left: 30px;
    }

    .forum-topic-username{
        color: gray;
    }


</style>

<!-- Page Heading -->
<div class="tab-pane active" id="tabs-1" role="tabpanel">
    <div class="find-people-tab tab-inner">
        <div class="tab-heading">
            <div class="tab-left-side">
                <h3><?= __tr('Forum Topic') ?></h3>
            </div>
        </div>

        <!-- liked people container -->
        <div class="find-people-profile">
            @if(!__isEmpty($forumCategoriesTopics))

            <a class="btn btn-secondary btn-sm" title="<?= __tr('Create Topic') ?>" href="#" data-toggle="modal" data-target="#lwCreateTopicModel"><?= __tr('Create Topic') ?></a>
            </br></br>

            <div class="row" id="lwForumTopicsContainer">
                @foreach($forumCategoriesTopics as $categories)
                    <div class="col-lg-12 mb-5 load-categories" id="categories-<?= $categories['_id'] ?>">
                        {{$categories['title']}}
                        
                    </div>
                    @php 
                        $countForumTopic = count($categories['forum_topics']); 
                        $twoTopicList = array_slice($categories['forum_topics'], 1, 2, true);
                    @endphp 

                    @foreach($categories['forum_topics'] as $topics)
                        <?php 
                            $getInterestData = fetchUserTopicInterestDetail($topics['_id']); 
                            $topicCount = count($topics['replies']);
                            $topicViews = $topics['view_count'];
                        ?>
                        <div class="col-lg-12 mb-2 load-topics" id="topics-<?= $topics['_id'] ?>">
                            <span class="topic-title"><b><a id="forumtopictitle" topic_id="{{$topics['_id']}}" href="{{url('/user/').'/'.$topics['_uid'].'/topic-detail'}}">{{$topics['title']}}</a></b></span> 
                            @if(!__isEmpty(getUserID()) && getUserID() == $topics['users__id'])
                            
                            <a title="<?= __tr('Edit Topic') ?>" href="#" data-toggle="modal" data-target="#lwEditTopicModel-{{$topics['_uid']}}"><i class="fa fa-edit" style="font-size:18px;color:red"></i></a> 
                            <a title="<?= __tr('Delete Topic') ?>" href="#" data-toggle="modal" data-target="#lwDeleteTopicModel-{{$topics['_uid']}}"><i class="fa fa-trash" style="font-size:18px;color:red"></i></a>

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
                                            <form class="user lw-ajax-form lw-form" method="post" action="<?= route('user.write.update_topic', ['forumTopicUId' => $topics['_uid']]) ?>">
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
                                                    <select name="interest_id[]" class="form-control multiple-kinks-edit" id="lwInterestEdit" required multiple="multiple">
                                                        @foreach($interestsList as $interestKey => $interest)
                                                        <option value="<?= $interest['_id'] ?>" <?= (__ifIsset($getInterestData) and in_array($interest['_id'], $getInterestData)) ? 'selected' : '' ?>><?= $interest['title'] ?></option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                

                                                <div class="form-group">
                                                    <label for="lwDescription"><?= __tr('Description') ?></label>
                                                    <textarea rows="2" cols="30" class="form-control summernote" id="lwDescription" name="description" required><?= $topics['description'] ?></textarea>
                                                </div>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                                    <input type="checkbox" class="custom-control-input" id="activeCheck" name="status" checked="true" <?= $topics['status'] == 1 ? 'checked' : '' ?>>
                                                    <label class="custom-control-label" for="activeCheck"><?= __tr('Active')  ?></label>
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
                                                <button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block-on-mobile"><?= __tr('Delete Account')  ?></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            @endif
                            <span class="topic-description"><p>{!! strlen(strip_tags($topics['description'])) > 50 ? substr(strip_tags($topics['description']),0,100)."..." : strip_tags($topics['description']) !!}</p></span>
                            <span class="forum-topic-username"><h6>By  @if($topics['user_detail']['_id'] == 1) {{"Kinkynearme"}} @else {{$topics['user_detail']['username'] ?? ""}} @endif</h6></span>
                        </div>
                        <div class="views-replies-count mb-3">
                            <span class="forum-views-count">VIEWS ({{$topicViews}})</span>
                            <span class="forum-reply-count">REPLIES ({{$topicCount}})</span>
                        </div>
                    @endforeach

                    @if($countForumTopic > 2)
                        </br></br>
                        <div class="forum-topic-view-all">
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
                <form class="user lw-ajax-form lw-form" method="post" action="<?= route('user.write.create_topic') ?>">
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
                        <select name="interest_id[]" class="form-control multiple-kinks-add" id="lwInterestAdd" required multiple="multiple">
                            @foreach($interestsList as $interestKey => $interest)
                            <option value="<?= $interest['_id'] ?>"><?= $interest['title'] ?></option>
                            @endforeach
                        </select>
                    </div>
                    

                    <div class="form-group">
                        <label for="lwDescription"><?= __tr('Description') ?></label>
                        <textarea rows="2" cols="30" class="form-control summernote" id="lwDescription" name="description" required></textarea>
                    </div>
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" id="activeCheck" name="status" checked="true">
                        <label class="custom-control-label" for="activeCheck"><?= __tr('Active')  ?></label>
                    </div>
                    <button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block-on-mobile"><?= __tr('Create Topic')  ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('appScripts')
<script type="text/javascript">
   

</script>
@endpush