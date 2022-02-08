</div>
<!-- Footer -->
  <footer>
    <div class="container">
      
      <div class="row">
          <div class="col-lg-3 col-md-3 no-padd">
            <div class="Copyright-footer text-left">
              <img src="<?= url('dist/images/footer-logo.png') ?>">
              <p><?= __tr('Copyright © __year__. All rights reserved.', [
                '__year__' => date('Y'),
              ]) ?></p>
            </div>
          </div>
          <div class="col-lg-9 col-md-9 no-padd">
            <div class="footer-link text-left">
              <ul>
                <li><a href="<?= route('landing_page') ?>">home</a></li>
                <li><a href="<?= route('page.view', ['pageId' => 1]) ?>">about us</a></li>
                <li><a href="">contact us</a></li>
                <li><a href="<?= route('page.view', ['pageId' => 3]) ?>">privacy</a></li>
                <li><a href="<?= route('page.view', ['pageId' => 2]) ?>">terms</a></li>
                <li><a href="">cookie policy</a></li>
                <li><a href="">intellectual property</a></li>
              </ul>
            </div>
          </div>
        </div>
    </div>
  </footer>

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
<!-- Messenger Dialog -->

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
    'dist/js/main.js'
], true) ?>

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
      // Collapse now if page is not at top
      navbarCollapse();
      // Collapse the navbar when page is scrolled
      $(window).scroll(navbarCollapse);

    })(jQuery); // End of use strict
  </script>

  @stack('footer')
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
@stack('appScripts')

</body>

</html>