@section('page-title', __tr('Find Matches'))
@section('head-title', __tr('Find Matches'))
@section('keywordName', __tr('Find Matches'))
@section('keyword', __tr('Find Matches'))
@section('description', __tr('Find Matches'))
@section('keywordDescription', __tr('Find Matches'))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- Page Heading -->
<div class="tab-pane active" id="tabs-1" role="tabpanel">
    <div class="find-people-tab tab-inner">
        <div class="tab-heading">
            <div class="tab-left-side">
                <h3><?= __tr('Based on your profile, here are people around you') ?></h3>
            </div>
        </div>

        <p class="results">(<?= $totalCount ?> results found)</p>


        <div class="find-people-profile">
            @if(!__isEmpty($filterData))
            <div class="row" id="lwUserFilterContainer">
                @include('filter.find-matches')
            </div>
            @else
            <!-- info message -->
            <br>
            <div class="alert alert-info">
                <?= __tr('There are no users found.') ?>
            </div>
            <!-- / info message -->
            @endif
        </div>

        @if($hasMorePages)
        <div class="lw-load-more-container">
            <button type="button" class="btn btn-light btn-block lw-ajax-link-action" id="lwLoadMoreButton" data-action="<?= $nextPageUrl ?>" data-callback="loadMoreUsers"><?= __tr('Load More') ?></button>
        </div>
        @endif

    </div>
</div>

<!-- Found matches container -->
@push('appScripts')
<script>
    function loadMoreUsers(responseData) {
        $(function() {
            applyLazyImages();
        });
        var requestData = responseData.data,
            appendData = responseData.response_action.content;
        $('div.find-people-profile > #lwUserFilterContainer').append(appendData);
        $('#lwLoadMoreButton').data('action', requestData.nextPageUrl);
        if (!requestData.hasMorePages) {
            $('.lw-load-more-container').hide();
        }
    }
    // Show advance filter
    $('#lwShowAdvanceFilterLink').on('click', function(e) {
        e.preventDefault();
        $('.lw-advance-filter-container').addClass('lw-expand-filter');
        $('#lwShowAdvanceFilterLink').hide();
        $('#lwHideAdvanceFilterLink').show();
        // $('.lw-advance-filter-container').show();
    });
    // Hide advance filter
    $('#lwHideAdvanceFilterLink').on('click', function(e) {
        e.preventDefault();
        $('.lw-advance-filter-container').removeClass('lw-expand-filter');
        $('#lwShowAdvanceFilterLink').show();
        $('#lwHideAdvanceFilterLink').hide();
        // $('.lw-advance-filter-container').hide();
    });

    

    function onLikeCallback(response) {
        var requestData = response.data;
        //check reaction code is 1 
        if (response.reaction == 1) {
            //$("#user-" + requestData.user_id).fadeOut(1000, function() { $(this).remove(); })
            //location.reload();
        }
    }

    function onFavouriteCallback(response) {

    }
</script>
@endpush