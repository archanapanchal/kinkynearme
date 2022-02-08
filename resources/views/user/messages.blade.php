

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
                        <button id="lwChatSidebarToggle" class="btn btn-link d-md-none rounded-circle mr-3"> <i class="fa fa-bars"></i> Chat</button>
                        <div id="lwMessengerContent"></div>

                        <br>
                        <br>
                    </div>


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
