@if(isAdmin() || (isset($isadmin)))
<!-- Footer -->
<footer class="sticky-footer @if(isAdmin()) bg-white @endif">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span><?= __tr('Copyright © __storeName__ __copyrightYear__', [
                        '__storeName__' => getStoreSettings('name'),
                        '__copyrightYear__' => date('Y')
                    ]) ?> </span>
    </div>
</footer>
<!-- End of Footer -->
@else
</div>
<!-- Footer -->
<footer>
    <div class="container">
      
        <div class="row">
            <div class="col-lg-3 col-md-3 no-padd">
                <div class="Copyright-footer text-left">
                    <img src="<?= getStoreSettings('footer_logo_image_url') ?>">
                    <p><?= __tr('Copyright © __year__. All rights reserved.', [
                    '__year__' => date('Y'),
                    ]) ?></p>
                </div>
            </div>
            <div class="col-lg-9 col-md-9 no-padd">
                <div class="footer-link text-left">
                    <ul>
                        <li><a href="<?= route('landing_page') ?>">home</a></li>
                        <li><a href="<?= route('page.view', ['pageId' => 'about-us']) ?>">about us</a></li>
                        <li><a href="<?= route('page.view', ['pageId' => 'contact-us']) ?>">contact us</a></li>
                        <li><a href="<?= route('page.view', ['pageId' => 'privacy-policy']) ?>">privacy</a></li>
                        <li><a href="<?= route('page.view', ['pageId' => 'terms-condition']) ?>">terms</a></li>
                        <li><a href="<?= route('page.view', ['pageId' => 'faq']) ?>">faq</a></li>
                        <li><a href="<?= route('page.view', ['pageId' => 'cookie-policy']) ?>">cookie policy</a></li>
                        <li><a href="<?= route('page.view', ['pageId' => 'intellectual-property']) ?>">intellectual property</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- End of Footer -->
@endif

<!-- Messenger Dialog -->
<div class="modal fade" id="messengerDialog" tabindex="-1" role="dialog" aria-labelledby="messengerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button id="lwChatSidebarToggle" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <h5 class="modal-title"><?= __tr('Chat') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= __tr('Close') ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="lwChatDialogLoader" style="display: none;">
                    <div class="d-flex justify-content-center m-5">
                        <div class="spinner-border" role="status">
                            <span class="sr-only"><?= __tr('Loading...') ?></span>
                        </div>
                    </div>
                </div>
                <div id="lwMessengerContent"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg user_detail_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
            <div class="tab-pane active" id="tabs-6" role="tabpanel">
                <div class="my-account">
                   <div id="my-profile" class="tabcontent">
                    
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>


<!-- Messenger Dialog -->
<img src="<?= asset('imgs/ajax-loader.gif') ?>" style="position:absolute;height:1px;width:1px;">
<script>
    window.appConfig = {
        debug: "<?= config('app.debug') ?>",
        csrf_token: "<?= csrf_token() ?>"
    }
</script>

<?= __yesset([
    'dist/pusher-js/pusher.min.js',
    'dist/js/vendorlibs-public.js',
    'dist/js/vendorlibs-datatable.js',
    'dist/js/vendorlibs-photoswipe.js',
    'dist/js/vendorlibs-smartwizard.js',
    'dist/js/owl.carousel.js',
    'dist/js/main.js',
    'dist/js/card_script.js',
    'dist/js/jquery.payform.min.js',
    'dist/js/select2.min.js'

], true) ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.textcomplete/1.8.5/jquery.textcomplete.min.js" integrity="sha512-7DIA0YtDMlg4BW1e0pXjd96R5zJwK8fJullbvGWbuvkCYgEMkME0UFfeZIGfQGYjSwCSeFRG5MIB5lhhEvKheg==" crossorigin="anonymous"></script>
@stack('footer')

<script>

    $('select[name="children"]').change(function() {
         var select_children = $(this).val();
         if (select_children == "no") {

            $('label[for="no_of_children"]').css("pointer-events","none");
            $('select[name="no_of_children"]').css("pointer-events","none");
         }else{
            $('label[for="no_of_children"]').css("pointer-events","painted");
            $('select[name="no_of_children"]').css("pointer-events","painted");
         }  
    });
    
    $('.display_user_detail').on('click',function(e){

        var username = $(this).attr('value');

        var requestUrl = '<?= route('user.frind_profile_data') ?>';

        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
        e.preventDefault();

        $.ajax({
            url: requestUrl,
            type: 'get',
            data: {
                username: username
            },
            success: function(response){ 
              $('.tabcontent').html(response);
                $('.user_detail_modal').modal('show'); 
            }
          });

        /*formData = {
                'username': username
            };
        __DataRequest.get(requestUrl, formData, {}, function(responseData) {
            //$('.user_detail_modal .tabcontent').html(responseData);
            $('.user_detail_modal').modal('show'); 
        });*/
    });
    
</script>


<script>
        $(document).ready(function(){
            
          $(".search-link").click(function(){
            $(".advance-search-v2").slideToggle();
            $(".advance-search-v2").css('display','block');
          });
        });

        $(document).ready(function() {
           $('.multiple-city').select2();
       });

        var $selectLocationCity = $('#selectLocationCityAdvanceSearch').selectize({

        // plugins: ['restore_on_backspace'],
        valueField: 'id',
        labelField: 'cities_full_name',
        searchField: [
            'cities_full_name'
        ],
        // options: [],
        create: false,
        // loadThrottle: 2000,
        maxItems: 1,
        render: {
            option: function(item, escape) {
                return '<div><span class="title"><span class="name">' + escape(item.cities_full_name) + '</span></span></div>';
            }
        },
        load: function(query, callback) {
            if (!query.length || (query.length < 2)) {
                return callback([]);
            } else {
                __DataRequest.post("<?= route('user.read.search_static_cities') ?>", {
                    'search_query': query
                }, function(responseData) {
                    callback(responseData.data.search_result);
                });
            }
        },
        onChange: function(value) {
            if (!value.length) {
                return;
            };

            $('#cityId').val(value);
            /*__DataRequest.post("<?= route('user.write.store_city') ?>", {
                'selected_city_id': value
            }, function(responseData) {
                if (responseData.reaction == 1) {
                    __Utils.viewReload();
                }
            });*/
        }
    });

    var selectLocationCityControl = $selectLocationCity[0].selectize;
    selectLocationCityControl.clear(true);
    selectLocationCityControl.clearOptions(true);
</script>
@if(!isAdmin())
<script>
    (function($) {
      "use strict"; // Start of use strict

      // Smooth scrolling using jQuery easing
      $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
          var target = $(this.hash);
          target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
          if (target.length) {
            $('html, body').animate({
              scrollTop: (target.offset().top - 70)
            }, 1000, "easeInOutExpo");
            return false;
          }
        }
      });

      // Closes responsive menu when a scroll trigger link is clicked
      $('.js-scroll-trigger').click(function() {
        $('.navbar-collapse').collapse('hide');
      });

      if ($("#mainNav").length) {

          // Activate scrollspy to add active class to navbar items on scroll
          $('body').scrollspy({
            target: '#mainNav',
            offset: 100
          });

          // Collapse Navbar
          var navbarCollapse = function() {
            if ($("#mainNav").offset().top > 100) {
              $("#mainNav").addClass("navbar-shrink");
            } else {
              $("#mainNav").removeClass("navbar-shrink");
            }
          };
        }
      // Collapse now if page is not at top
      navbarCollapse();
      // Collapse the navbar when page is scrolled
      $(window).scroll(navbarCollapse);

    })(jQuery); // End of use strict
</script>
@endif

<script>
    (function() {
        $.validator.messages = $.extend({}, $.validator.messages, {
            required: '<?= __tr("This field is required.") ?>',
            remote: '<?= __tr("Please fix this field.") ?>',
            email: '<?= __tr("Please enter a valid email address.") ?>',
            url: '<?= __tr("Please enter a valid URL.") ?>',
            date: '<?= __tr("Please enter a valid date.") ?>',
            dateISO: '<?= __tr("Please enter a valid date (ISO).") ?>',
            number: '<?= __tr("Please enter a valid number.") ?>',
            digits: '<?= __tr("Please enter only digits.") ?>',
            equalTo: '<?= __tr("Please enter the same value again.") ?>',
            maxlength: $.validator.format('<?= __tr("Please enter no more than {0} characters.") ?>'),
            minlength: $.validator.format('<?= __tr("Please enter at least {0} characters.") ?>'),
            rangelength: $.validator.format('<?= __tr("Please enter a value between {0} and {1} characters long.") ?>'),
            range: $.validator.format('<?= __tr("Please enter a value between {0} and {1}.") ?>'),
            max: $.validator.format('<?= __tr("Please enter a value less than or equal to {0}.") ?>'),
            min: $.validator.format('<?= __tr("Please enter a value greater than or equal to {0}.") ?>'),
            step: $.validator.format('<?= __tr("Please enter a multiple of {0}.") ?>')
        });
    })();
</script>
<?= __yesset([
    'dist/js/common-app.*.js'
], true) ?>
<script>
    __Utils.setTranslation({
        'processing': "<?= __tr('processing') ?>",
        'uploader_default_text': "<span class='filepond--label-action'><?= __tr('Drag & Drop Files or Browse') ?></span>",
        'gif_no_result': "<?= __tr('Result Not Found') ?>",
        "message_is_required": "<?= __tr('Message is required') ?>",
        "sticker_name_label": "<?= __tr('Stickers') ?>",
        "chat_placeholder": "<?= __tr('type message...') ?>"
    });

    var userLoggedIn = '<?= isLoggedIn() ?>',
        enablePusher = '<?= getStoreSettings('allow_pusher') ?>';

    if (userLoggedIn && enablePusher) {
        var userUid = '<?= getUserUID() ?>',
            pusherAppKey = '<?= getStoreSettings('pusher_app_key') ?>',
            __pusherAppOptions = {
                cluster: '<?= getStoreSettings('pusher_app_cluster_key') ?>',
                forceTLS: true,
            };

    }
</script>
<!-- Include Audio Video Call Component -->
@include('messenger.audio-video')
<!-- /Include Audio Video Call Component -->
<script>
    //check user loggedIn or not
    if (userLoggedIn && enablePusher) {
        //if messenger dialog is open then hide new message dot
        $("#messengerDialog").on('click', function() {
            var messengerDialogVisibility = $("#messengerDialog").is(':visible');
            if (messengerDialogVisibility) {
                $(".lw-new-message-badge").hide();
            }
        });

        //subscribe pusher notification
        subscribeNotification('event.user.notification', pusherAppKey, userUid, function(responseData) {
            //get notification list
            var requestData = responseData.getNotificationList,
                getNotificationList = requestData.notificationData,
                getNotificationCount = requestData.notificationCount;
            //update notification count
            __DataRequest.updateModels({
                'totalNotificationCount': getNotificationCount, //total notification count
            });
            //check is not empty
            if (!_.isEmpty(getNotificationList)) {
                var template = _.template($("#lwNotificationListTemplate").html());
                $("#lwNotificationContent").html(template({
                    'notificationList': getNotificationList,
                }));
            }
            //check is not empty
            if (responseData) {
                switch (responseData.type) {
                    case 'user-likes':
                        if (responseData.showNotification != 0) {
                            showSuccessMessage(responseData.message);
                        }
                        break;
                    case 'user-gift':
                        if (responseData.showNotification != 0) {
                            showSuccessMessage(responseData.message);
                        }
                        break;
                    case 'profile-visitor':
                        if (responseData.showNotification != 0) {
                            showSuccessMessage(responseData.message);
                        }
                        break;
                    case 'user-login':
                        if (responseData.showNotification != 0) {
                            showSuccessMessage(responseData.message);
                        }
                        break;
                    default:
                        showSuccessMessage(responseData.message);
                        break;
                }
            }
        });

        subscribeNotification('event.user.chat.messages', pusherAppKey, userUid, function(responseData) {
            var messengerDialogVisibility = $("#messengerDialog").is(':visible');
            //if messenger dialog is not open then show notification dot
            if (!messengerDialogVisibility) {
                $(".lw-new-message-badge").show();
            }
            // Message chat
            if (responseData.requestFor == 'MESSAGE_CHAT') {
                if (currentSelectedUserUid == responseData.toUserUid) {
                    __Messenger.appendReceivedMessage(responseData.type, responseData.message, responseData.createdOn);
                }
                // Set user message count
                if (responseData.userId != currentSelectedUserId) {
                    var incomingMsgEl = $('.lw-incoming-message-count-' + responseData.userId),
                        messageCount = 1;
                    if (!_.isEmpty(incomingMsgEl.text())) {
                        messageCount = parseInt(incomingMsgEl.text()) + 1;
                    }
                    incomingMsgEl.text(messageCount);
                    $('.lw-messenger-contact-list .list-group.list-group-flush').prepend($('a.lw-user-chat-list#' + responseData.userId));
                    $('a.lw-user-chat-list#' + responseData.userId +' .lw-contact-status').removeClass('lw-away lw-offline').addClass('lw-online');
                }

                // Show notification of incoming messages
                if (!messengerDialogVisibility && responseData.showNotification) {
                    showSuccessMessage(responseData.notificationMessage);
                }
            }

            // Message request
            if (responseData.requestFor == 'MESSAGE_REQUEST') {
                if (responseData.userId == currentSelectedUserId) {
                    handleMessageActionContainer(responseData.messageRequestStatus, false);
                    if (!_.isEmpty(responseData.message)) {
                        __Messenger.appendReceivedMessage(responseData.type, responseData.message, responseData.createdOn);
                    }
                } else {
                    // Show notification of incoming messages
                    if (!messengerDialogVisibility && responseData.showNotification) {
                        showSuccessMessage(responseData.notificationMessage);
                    }
                }
            }

        });
    };

    //for cookie terms 
    function showCookiePolicyDialog() {
        if (__Cookie.get('cookie_policy_terms_accepted') != '1') {
            $('#lwCookiePolicyContainer').show();
        } else {
            $('#lwCookiePolicyContainer').hide();
        }
    };

    showCookiePolicyDialog();

    $("#lwCookiePolicyButton").on('click', function() {
        __Cookie.set('cookie_policy_terms_accepted', '1', 1000);
        showCookiePolicyDialog();
    });

    // Get messenger chat data
    function getChatMessenger(url, isAllChatMessenger) {
        var $allMessageChatButtonEl = $('#lwAllMessageChatButton'),
            $lwMessageChatButtonEl = $('#lwMessageChatButton');
        // check if request for all messenger 
        if (isAllChatMessenger) {
            var isAllMessengerChatLoaded = $allMessageChatButtonEl.data('chat-loaded');
            if (!isAllMessengerChatLoaded) {
                $allMessageChatButtonEl.attr('data-chat-loaded', true);
                $lwMessageChatButtonEl.attr('data-chat-loaded', false);
                fetchChatMessages(url);
            }
        } else {
            var isMessengerLoaded = $lwMessageChatButtonEl.data('chat-loaded');
            if (!isMessengerLoaded) {
                $lwMessageChatButtonEl.attr('data-chat-loaded', true);
                $allMessageChatButtonEl.attr('data-chat-loaded', false);
                fetchChatMessages(url);
            }
        }
    };

    // Fetch messages from server
    function fetchChatMessages(url) {
        $('#lwChatDialogLoader').show();
        $('#lwMessengerContent').hide();
        __DataRequest.get(url, {}, function(responseData) {
            $('#lwChatDialogLoader').hide();
            $('#lwMessengerContent').show();
        });
    };

    $.extend( $.fn.dataTable.defaults, {
                "language"        : {
                    "decimal":        "",
                    "emptyTable":     '<?= __tr("No data available in table") ?>',
                    "info":           '<?= __tr("Showing _START_ to _END_ of _TOTAL_ entries") ?>',
                    "infoEmpty":      "<?= __tr('Showing 0 to 0 of 0 entries') ?>",
                    "infoFiltered":   "<?= __tr('(filtered from _MAX_ total entries)') ?>",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "<?= __tr('Show _MENU_ entries') ?>",
                    "loadingRecords": "<?= __tr('Loading...') ?>",
                    "processing":     '<?= __tr("Processing...") ?>',
                    "search":         "<?= __tr('Search:') ?>",
                    "zeroRecords":    "<?= __tr('No matching records found') ?>",
                    "paginate": {
                        "first":      "<?= __tr('First') ?>",
                        "last":       "<?= __tr('Last') ?>",
                        "next":      "<?= __tr('Next') ?>",
                        "previous":   "<?= __tr('Previous') ?>"
                    },
                    "aria": {
                        "sortAscending":  "<?= __tr(': activate to sort column ascending') ?>",
                        "sortDescending": "<?= __tr(': activate to sort column descending') ?>"
                    }
                    }
            });
</script>
@stack('appScripts')