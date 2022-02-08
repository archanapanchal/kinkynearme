
<section class="tab-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-1" role="tab">People near me</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Matches</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">Favorite</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tabs-4" role="tab">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-5" role="tab">Discussion forum</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-6" role="tab">My account</a>
                    </li>
                </ul>
                
                <!-- Tab panes -->
                <div class="tab-content">

                    <div class="tab-pane active" id="tabs-4" role="tabpanel">
                        <a class="nav-link d-none" onclick="getChatMessenger2('<?= route('user.read.all_conversation') ?>', true)" id="lwAllMessageChatButton2" data-chat-loaded="false">
                            <span class="badge badge-danger badge-counter lw-new-message-badge"></span>
                            <i class="far fa-comments"></i>
                        </a>

                        <div id="lwChatDialogLoader2" style="display: none;">
                            <div class="d-flex justify-content-center m-5">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only"><?= __tr('Loading...') ?></span>
                                </div>
                            </div>
                        </div>
                        <button id="lwChatSidebarToggle" class="btn btn-link d-md-none rounded-circle mr-3"> <i class="fa fa-bars"></i> </button>
                        <div id="lwMessengerContent"></div>

                        <br>
                        <br>
                    </div>

                </div>
            </div>
        </div>
    </div>


</section>


@push('appScripts')
<script>
// Get messenger chat data
    function getChatMessenger2(url, isAllChatMessenger) {
        var $allMessageChatButtonEl = $('#lwAllMessageChatButton2'),
            $lwMessageChatButtonEl = $('#lwMessageChatButton2');
        // check if request for all messenger 
        if (isAllChatMessenger) {
            var isAllMessengerChatLoaded = $allMessageChatButtonEl.data('chat-loaded');
            if (!isAllMessengerChatLoaded) {
                $allMessageChatButtonEl.attr('data-chat-loaded', true);
                $lwMessageChatButtonEl.attr('data-chat-loaded', false);
                fetchChatMessages2(url);
            }
        } else {
            var isMessengerLoaded = $lwMessageChatButtonEl.data('chat-loaded');
            if (!isMessengerLoaded) {
                $lwMessageChatButtonEl.attr('data-chat-loaded', true);
                $allMessageChatButtonEl.attr('data-chat-loaded', false);
                fetchChatMessages2(url);
            }
        }
    };

    // Fetch messages from server
    function fetchChatMessages2(url) {
        $('#lwChatDialogLoader2').show();
        $('#lwMessengerContent').hide();
        __DataRequest.get(url, {}, function(responseData) {
            $('#lwChatDialogLoader2').hide();
            $('#lwMessengerContent').show();
        });
    };

    window.onload = $('#lwAllMessageChatButton2').trigger('click');
</script>
@endpush
