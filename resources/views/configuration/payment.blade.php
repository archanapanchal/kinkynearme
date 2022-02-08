<!-- Page Heading -->
<h3><?= __tr('Payment Settings') ?></h3>
<!-- /Page Heading -->
<hr>
<?php $isExtendedLicence = (getStoreSettings('product_registration', 'licence') === 'dee257a8c3a2656b7d7fbe9a91dd8c7c41d90dc9'); ?>
<!-- Payment Setting Form -->
@if(!$isExtendedLicence)
<div class="alert alert-warning my-3">
	<strong title="Extended Licence Required"><?= __tr('Extended Licence Required') ?></strong> <br>
	<?= __tr('To use the payment gateway to charge customers you need to buy an Extended licence. While you can test it with Regular licence, But you need to purchase Extended licence to use Live Keys') ?>
</div>
@endif
<form class="lw-ajax-form lw-form" method="post" data-callback="onPaymentGatewayFormCallback" action="<?= route('manage.configuration.write', ['pageType' => request()->pageType]) ?>">
	<!-- input field body -->
	<div class="form-group mt-2">
		<!-- paypal settings -->
		<fieldset class="lw-fieldset mb-3">
			<legend class="lw-fieldset-legend"><?= __tr('PayPal') ?></legend>

			<!-- Enable Paypal Checkout field -->
			<div class="custom-control custom-checkbox custom-control-inline">
				<input type="hidden" name="enable_paypal" value="0">
				<input type="checkbox" class="custom-control-input" id="lwEnablePaypal" name="enable_paypal" <?= $configurationData['enable_paypal'] == true ? 'checked' : '' ?> value="true">
				<label class="custom-control-label" for="lwEnablePaypal"><?= __tr('Enable Paypal Checkout')  ?></label>
			</div>
			<!-- / Enable Paypal Checkout field -->

			<span id="lwPaypalCheckoutContainer">
				<!-- use testing paypal checkout input fieldset -->
				<fieldset class="lw-fieldset mb-3">
					<!-- use testing input radio field -->
					<legend class="lw-fieldset-legend">
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="lwUsePaypalCheckoutTest" name="use_test_paypal_checkout" class="custom-control-input" value="1" <?= $configurationData['use_test_paypal_checkout'] == true ? 'checked' : '' ?>>
							<label class="custom-control-label" for="lwUsePaypalCheckoutTest"><?= __tr('Use Testing') ?></label>
						</div>
					</legend>
					<!-- /use testing input radio field -->

					<!-- show after added testing paypal checkout information -->
					<div class="btn-group" id="lwTestPaypalCheckoutExists">
						<button type="button" disabled="true" class="btn btn-success lw-btn"><?= __tr('Testing Paypal Checkout keys are installed.') ?></button>
						<button type="button" class="btn btn-light lw-btn" id="lwUpdateTestPaypalCheckout"><?= __tr('Update') ?></button>
					</div>
					<!-- show after added testing paypal checkout information -->

					<!-- paypal test key exists hidden field -->
					<input type="hidden" name="paypal_test_keys_exist" id="lwPaypalTestKeysExist" value="<?= $configurationData['paypal_checkout_testing_client_id'] ?>" />
					<!-- paypal test key exists hidden field -->

					<div id="lwTestPaypalInputField">
						<!-- Testing Client Id Key -->
						<div class="mb-3">
							<label for="lwPaypalTestClientId"><?= __tr('Client Id') ?></label>
							<input type="text" class="form-control form-control-user" name="paypal_checkout_testing_client_id" id="lwPaypalTestClientId" placeholder="<?= __tr('Client Id') ?>">
						</div>
						<!-- / Testing Client Id Key -->

						<!-- Testing Secret Key -->
						<div class="mb-3">
							<label for="lwPaypalTestSecretKey"><?= __tr('Secret Key') ?></label>
							<input type="text" class="form-control form-control-user" name="paypal_checkout_testing_secret_key" id="lwPaypalTestSecretKey" placeholder="<?= __tr('Secret Key') ?>">
						</div>
						<!-- / Testing Secret Key -->
					</div>
				</fieldset>
				<!-- /use testing paypal checkout input fieldset -->

				<!-- use live paypal checkout input fieldset -->
				<fieldset class="lw-fieldset mb-3">
					<!-- use live input radio field -->
					<legend class="lw-fieldset-legend">
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="lwUsePaypalCheckoutLive" name="use_test_paypal_checkout" class="custom-control-input" value="0" <?= $configurationData['use_test_paypal_checkout'] == false ? 'checked' : '' ?>>
							<label class="custom-control-label" for="lwUsePaypalCheckoutLive"><?= __tr('Use Live') ?></label>
						</div>
					</legend>
					<!-- /use live input radio field -->

					@if($isExtendedLicence)
					<!-- show after added Live paypal checkout information -->
					<div class="btn-group" id="lwLivePaypalCheckoutExists">
						<button type="button" disabled="true" class="btn btn-success lw-btn"><?= __tr('Live Paypal Checkout keys are installed.') ?></button>
						<button type="button" class="btn btn-light lw-btn" id="lwUpdateLivePaypalCheckout"><?= __tr('Update') ?></button>
					</div>
					<!-- show after added Live paypal checkout information -->

					<!-- paypal live key exists hidden field -->
					<input type="hidden" name="paypal_live_keys_exist" id="lwPaypalLiveKeysExist" value="<?= $configurationData['paypal_checkout_live_client_id'] ?>" />
					<!-- paypal live key exists hidden field -->

					<div id="lwLivePaypalInputField">
						<!-- Live Client Id Key -->
						<div class="mb-3">
							<label for="lwPaypalLiveClientId"><?= __tr('Client Id') ?></label>
							<input type="text" class="form-control form-control-user" name="paypal_checkout_live_client_id" id="lwPaypalLiveClientId" placeholder="<?= __tr('Client Id') ?>">
						</div>
						<!-- / Live Client Id Key -->

						<!-- Live Secret Key -->
						<div class="mb-3">
							<label for="lwPaypalLiveSecretKey"><?= __tr('Secret Key') ?></label>
							<input type="text" class="form-control form-control-user" name="paypal_checkout_live_secret_key" id="lwPaypalLiveSecretKey" placeholder="<?= __tr('Secret Key') ?>">
						</div>
						<!-- / Live Secret Key -->
					</div>
					@else
					<div class="alert alert-danger">
						Extended licence required to use live keys
					</div>
					@endif
				</fieldset>
				<!-- /use live paypal checkout input fieldset -->
			</span>
		</fieldset>
		<!-- / paypal settings -->

		<!-- stripe settings -->
		<fieldset class="lw-fieldset mb-3">
			<legend class="lw-fieldset-legend"><?= __tr('Stripe') ?></legend>

			<!-- Enable stripe Checkout field -->
			<div class="custom-control custom-checkbox custom-control-inline">
				<input type="hidden" name="enable_stripe" value="0">
				<input type="checkbox" class="custom-control-input" id="lwEnableStripe" name="enable_stripe" <?= $configurationData['enable_stripe'] == true ? 'checked' : '' ?>>
				<label class="custom-control-label" for="lwEnableStripe"><?= __tr('Enable Stripe Checkout')  ?></label>
			</div>
			<!-- / Enable stripe Checkout field -->

			<span id="lwStripeCheckoutContainer">
				<!-- use testing stripe checkout input fieldset -->
				<fieldset class="lw-fieldset mb-3">
					<!-- use testing input radio field -->
					<legend class="lw-fieldset-legend">
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="lwUseStripeCheckoutTest" name="use_test_stripe" class="custom-control-input" value="1" <?= $configurationData['use_test_stripe'] == true ? 'checked' : '' ?>>
							<label class="custom-control-label" for="lwUseStripeCheckoutTest"><?= __tr('Use Testing') ?></label>
						</div>
					</legend>
					<!-- /use testing input radio field -->

					<!-- show after added testing stipe checkout information -->
					<div class="btn-group" id="lwTestStripeCheckoutExists">
						<button type="button" disabled="true" class="btn btn-success lw-btn"><?= __tr('Testing Stripe Checkout keys are installed.') ?></button>
						<button type="button" class="btn btn-light lw-btn" id="lwUpdateTestStripeCheckout"><?= __tr('Update') ?></button>
					</div>
					<!-- show after added testing stipe checkout information -->

					<!-- stripe test secret key exists hidden field -->
					<input type="hidden" name="stripe_test_keys_exist" id="lwStripeTestKeysExist" value="<?= $configurationData['stripe_testing_secret_key'] ?>" />
					<!-- stripe test secret key exists hidden field -->

					<div id="lwTestStripeInputField">
						<!-- Testing Secret Key Key -->
						<div class="mb-3">
							<label for="lwStripeTestSecretKey"><?= __tr('Secret Key') ?></label>
							<input type="text" class="form-control form-control-user" value="" id="lwStripeTestSecretKey" name="stripe_testing_secret_key" placeholder="<?= __tr('Secret Key') ?>">
						</div>
						<!-- / Testing Secret Key Key -->

						<!-- Testing Publish Key -->
						<div class="mb-3">
							<label for="lwStripeTestPublishKey"><?= __tr('Publish Key') ?></label>
							<input type="text" class="form-control form-control-user" value="" id="lwStripeTestPublishKey" name="stripe_testing_publishable_key" placeholder="<?= __tr('Publish Key') ?>">
						</div>
						<!-- / Testing Publish Key -->
					</div>
				</fieldset>
				<!-- /use testing paypal checkout input fieldset -->

				<!-- use live stripe checkout input fieldset -->
				<fieldset class="lw-fieldset mb-3">
					<!-- use live input radio field -->
					<legend class="lw-fieldset-legend">
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="lwUseStripeCheckoutLive" name="use_test_stripe" class="custom-control-input" value="0" <?= $configurationData['use_test_stripe'] == false ? 'checked' : '' ?>>
							<label class="custom-control-label" for="lwUseStripeCheckoutLive"><?= __tr('Use Live') ?></label>
						</div>
					</legend>
					<!-- /use live input radio field -->
					@if($isExtendedLicence)
					<!-- show after added Live stripe checkout information -->
					<div class="btn-group" id="lwLiveStripeCheckoutExists">
						<button type="button" disabled="true" class="btn btn-success lw-btn"><?= __tr('Live Stripe Checkout keys are installed.') ?></button>
						<button type="button" class="btn btn-light lw-btn" id="lwUpdateLiveStripeCheckout"><?= __tr('Update') ?></button>
					</div>
					<!-- show after added Live stripe checkout information -->

					<!-- stripe live secret key exists hidden field -->
					<input type="hidden" name="stripe_live_keys_exist" id="lwStripeLiveKeysExist" value="<?= $configurationData['stripe_live_secret_key'] ?>" />
					<!-- stripe live secret key exists hidden field -->

					<div id="lwLiveStripeInputField">
						<!-- Live Secret Key Key -->
						<div class="mb-3">
							<label for="lwStripeLiveSecretKey"><?= __tr('Secret Key') ?></label>
							<input type="text" class="form-control form-control-user" value="" id="lwStripeLiveSecretKey" name="stripe_live_secret_key" placeholder="<?= __tr('Secret Key') ?>">
						</div>
						<!-- / Live Secret Key Key -->

						<!-- Live Publish Key -->
						<div class="mb-3">
							<label for="lwStripeLivePublishKey"><?= __tr('Publish Key') ?></label>
							<input type="text" class="form-control form-control-user" value="" id="lwStripeLivePublishKey" name="stripe_live_publishable_key" placeholder="<?= __tr('Publish Key') ?>">
						</div>
						<!-- / Live Publish Key -->
					</div>
					@else
					<div class="alert alert-danger">
						Extended licence required to use live keys
					</div>
					@endif
				</fieldset>
				<!-- /use live stripe checkout input fieldset -->
			</span>
		</fieldset>
		<!-- / stripe settings -->

		<!-- razorpay settings -->
		<fieldset class="lw-fieldset mb-3">
			<legend class="lw-fieldset-legend"><?= __tr('Razorpay') ?></legend>

			<!-- Enable razorpay Checkout field -->
			<div class="custom-control custom-checkbox custom-control-inline">
				<input type="hidden" name="enable_razorpay" value="0">
				<input type="checkbox" class="custom-control-input" id="lwEnableRazorpay" name="enable_razorpay" <?= $configurationData['enable_razorpay'] == true ? 'checked' : '' ?>>
				<label class="custom-control-label" for="lwEnableRazorpay"><?= __tr('Enable Razorpay Checkout')  ?></label>
			</div>
			<!-- / Enable razorpay Checkout field -->

			<span id="lwRazorpayCheckoutContainer">
				<!-- use testing razorpay checkout input fieldset -->
				<fieldset class="lw-fieldset mb-3">
					<!-- use testing input radio field -->
					<legend class="lw-fieldset-legend">
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="lwUseRazorpayCheckoutTest" name="use_test_razorpay" class="custom-control-input" value="1" <?= $configurationData['use_test_razorpay'] == true ? 'checked' : '' ?>>
							<label class="custom-control-label" for="lwUseRazorpayCheckoutTest"><?= __tr('Use Testing') ?></label>
						</div>
					</legend>
					<!-- /use testing input radio field -->

					<!-- show after added testing razorpay checkout information -->
					<div class="btn-group" id="lwTestRazorpayCheckoutExists">
						<button type="button" disabled="true" class="btn btn-success lw-btn"><?= __tr('Testing Razorpay Checkout keys are installed.') ?></button>
						<button type="button" class="btn btn-light lw-btn" id="lwUpdateTestRazorpayCheckout"><?= __tr('Update') ?></button>
					</div>
					<!-- show after added testing razorpay checkout information -->

					<!-- razorpay test secret key exists hidden field -->
					<input type="hidden" name="razorpay_test_keys_exist" id="lwRazorpayTestKeysExist" value="<?= $configurationData['razorpay_testing_key'] ?>" />
					<!-- razorpay test secret key exists hidden field -->

					<div id="lwTestRazorpayInputField">
						<!-- Testing Razorpay Key -->
						<div class="mb-3">
							<label for="lwRazorpayTestKey"><?= __tr('Razorpay Key') ?></label>
							<input type="text" class="form-control form-control-user" value="" id="lwRazorpayTestKey" name="razorpay_testing_key" placeholder="<?= __tr('Razorpay Key') ?>">
						</div>
						<!-- / Testing Razorpay Key -->

						<!-- Testing Razorpay Secret Key -->
						<div class="mb-3">
							<label for="lwRazorpayTestSecretKey"><?= __tr('Razorpay Secret Key') ?></label>
							<input type="text" class="form-control form-control-user" value="" id="lwRazorpayTestSecretKey" name="razorpay_testing_secret_key" placeholder="<?= __tr('Razorpay Secret Key') ?>">
						</div>
						<!-- / Testing Razorpay Secret Key -->
					</div>
				</fieldset>
				<!-- /use testing razorpay checkout input fieldset -->

				<!-- use live Razorpay checkout input fieldset -->
				<fieldset class="lw-fieldset mb-3">
					<!-- use live input radio field -->
					<legend class="lw-fieldset-legend">
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="lwUseRazorpayCheckoutLive" name="use_test_razorpay" class="custom-control-input" value="0" <?= $configurationData['use_test_razorpay'] == false ? 'checked' : '' ?>>
							<label class="custom-control-label" for="lwUseRazorpayCheckoutLive"><?= __tr('Use Live') ?></label>
						</div>
					</legend>
					<!-- /use live input radio field -->
					@if($isExtendedLicence)
					<!-- show after added Live razorpay checkout information -->
					<div class="btn-group" id="lwLiveRazorpayCheckoutExists">
						<button type="button" disabled="true" class="btn btn-success lw-btn"><?= __tr('Live Razorpay Checkout keys are installed.') ?></button>
						<button type="button" class="btn btn-light lw-btn" id="lwUpdateLiveRazorpayCheckout"><?= __tr('Update') ?></button>
					</div>
					<!-- show after added Live razorpay checkout information -->

					<!-- razorpay live secret key exists hidden field -->
					<input type="hidden" name="razorpay_live_keys_exist" id="lwRazorpayLiveKeysExist" value="<?= $configurationData['razorpay_live_key'] ?>" />
					<!-- razorpay live secret key exists hidden field -->

					<div id="lwLiveRazorpayInputField">
						<!-- Live Razorpay Key Key -->
						<div class="mb-3">
							<label for="lwRazorpayLiveKey"><?= __tr('Razorpay Key') ?></label>
							<input type="text" class="form-control form-control-user" value="" id="lwRazorpayLiveKey" name="razorpay_live_key" placeholder="<?= __tr('Razorpay Key') ?>">
						</div>
						<!-- / Live Razorpay Key Key -->

						<!-- Live Secret Key -->
						<div class="mb-3">
							<label for="lwRazorpayLiveSecretKey"><?= __tr('Razorpay Secret Key') ?></label>
							<input type="text" class="form-control form-control-user" value="" id="lwRazorpayLiveSecretKey" name="razorpay_live_secret_key" placeholder="<?= __tr('Razorpay Secret Key') ?>">
						</div>
						<!-- / Live Secret Key -->
					</div>
					@else
					<div class="alert alert-danger">
						Extended licence required to use live keys
					</div>
					@endif
				</fieldset>
				<!-- /use live stripe checkout input fieldset -->
			</span>
		</fieldset>
		<!-- / razorpay settings -->
	</div>
	<!-- / input field body -->

	<!-- Update Button -->
	<a href class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile">
		<?= __tr('Update') ?>
	</a>
	<!-- /Update Button -->
</form>
<!-- /Payment Setting Form -->

@push('appScripts')
<script>
	/*********** Paypal Enable / Disable Checkout start here ***********/
	var isPaypalCheckoutEnable = $('#lwEnablePaypal').is(':checked'),
		isUsePaypalCheckoutTest = $("#lwUsePaypalCheckoutTest").is(':checked'),
		lwUsePaypalCheckoutLive = $("#lwUsePaypalCheckoutLive").is(':checked');
	//paypal checkout is enable then add disable content class
	if (!isPaypalCheckoutEnable) {
		$('#lwPaypalCheckoutContainer').addClass('lw-disabled-block-content');
	}
	//paypal checkout is enable/disabled on change 
	//then add/remove disable content class
	$("#lwEnablePaypal").on('change', function(event) {
		isPaypalCheckoutEnable = $(this).is(":checked");
		//check is enable false then add class
		if (!isPaypalCheckoutEnable) {
			$("#lwPaypalCheckoutContainer").addClass('lw-disabled-block-content');
			//else remove class
		} else {
			$("#lwPaypalCheckoutContainer").removeClass('lw-disabled-block-content');
		}
	});

	//check paypal test mode is true then disable paypal live input field	
	if (isUsePaypalCheckoutTest) {
		$('#lwUpdateLivePaypalCheckout').attr('disabled', true);
		$('#lwLivePaypalInputField').addClass('lw-disabled-block-content');
		//check paypal test mode is false then disable paypal test input field	
	} else if (lwUsePaypalCheckoutLive) {
		$('#lwUpdateTestPaypalCheckout').attr('disabled', true);
		$('#lwTestPaypalInputField').addClass('lw-disabled-block-content');
	}

	//check paypal test mode is true on change 
	//then disable paypal live input field	
	$("#lwUsePaypalCheckoutTest").on('change', function(event) {
		var isUsePaypalCheckoutTest = $(this).is(':checked');
		if (isUsePaypalCheckoutTest) {
			$('#lwUpdateLivePaypalCheckout').attr('disabled', true);
			$('#lwUpdateTestPaypalCheckout').attr('disabled', false);
			$('#lwTestPaypalInputField').removeClass('lw-disabled-block-content');
			$('#lwLivePaypalInputField').addClass('lw-disabled-block-content');
		}
	});

	//check paypal test mode is false on change 
	//then disable paypal test input field	
	$("#lwUsePaypalCheckoutLive").on('change', function(event) {
		var lwUsePaypalCheckoutLive = $(this).is(':checked');
		if (lwUsePaypalCheckoutLive) {
			$('#lwUpdateTestPaypalCheckout').attr('disabled', true);
			$('#lwUpdateLivePaypalCheckout').attr('disabled', false);
			$('#lwLivePaypalInputField').removeClass('lw-disabled-block-content');
			$('#lwTestPaypalInputField').addClass('lw-disabled-block-content');
		}
	});
	/*********** Paypal Enable / Disable Checkout end here ***********/

	/*********** Paypal Testing Keys setting start here ***********/
	var isTestPaypalKeysInstalled = "<?= $configurationData['paypal_checkout_testing_client_id'] ?>",
		lwTestPaypalInputField = $('#lwTestPaypalInputField'),
		lwTestPaypalCheckoutExists = $('#lwTestPaypalCheckoutExists');

	// Check if test paypal keys are installed
	if (isTestPaypalKeysInstalled) {
		lwTestPaypalInputField.hide();
	} else {
		lwTestPaypalCheckoutExists.hide();
	}
	// Update paypal checkout testing keys
	$('#lwUpdateTestPaypalCheckout').click(function() {
		$("#lwPaypalTestKeysExist").val(0);
		lwTestPaypalInputField.show();
		lwTestPaypalCheckoutExists.hide();
	});
	/*********** Paypal Testing Keys setting end here ***********/

	/*********** Paypal Live Keys setting start here ***********/
	var isLivePaypalKeysInstalled = "<?= $configurationData['paypal_checkout_live_client_id'] ?>",
		lwLivePaypalInputField = $('#lwLivePaypalInputField'),
		lwLivePaypalCheckoutExists = $('#lwLivePaypalCheckoutExists');

	// Check if test paypal keys are installed
	if (isLivePaypalKeysInstalled) {
		lwLivePaypalInputField.hide();
	} else {
		lwLivePaypalCheckoutExists.hide();
	}
	// Update paypal checkout testing keys
	$('#lwUpdateLivePaypalCheckout').click(function() {
		$("#lwPaypalLiveKeysExist").val(0);
		lwLivePaypalInputField.show();
		lwLivePaypalCheckoutExists.hide();
	});
	/*********** Paypal Live Keys setting end here ***********/

	/*********** Stripe Enable / Disable Checkout start here ***********/
	var isStripeCheckoutEnable = $('#lwEnableStripe').is(':checked'),
		isUseStripeCheckoutTest = $("#lwUseStripeCheckoutTest").is(':checked'),
		isUseStripeCheckoutLive = $("#lwUseStripeCheckoutLive").is(':checked');

	if (!isStripeCheckoutEnable) {
		$('#lwStripeCheckoutContainer').addClass('lw-disabled-block-content');
	}
	$("#lwEnableStripe").on('change', function(event) {
		isStripeCheckoutEnable = $(this).is(":checked");
		//check is enable false then add class
		if (!isStripeCheckoutEnable) {
			$("#lwStripeCheckoutContainer").addClass('lw-disabled-block-content');
			//else remove class
		} else {
			$("#lwStripeCheckoutContainer").removeClass('lw-disabled-block-content');
		}
	});

	//check stripe test mode is true then disable stripe live input field	
	if (isUseStripeCheckoutTest) {
		$('#lwUpdateLiveStripeCheckout').attr('disabled', true);
		$('#lwLiveStripeInputField').addClass('lw-disabled-block-content');
		//check stripe test mode is false then disable stripe test input field	
	} else if (isUseStripeCheckoutLive) {
		$('#lwUpdateTestStripeCheckout').attr('disabled', true);
		$('#lwTestStripeInputField').addClass('lw-disabled-block-content');
	}

	//check stripe test mode is true on change 
	//then disable stripe live input field	
	$("#lwUseStripeCheckoutTest").on('change', function(event) {
		var isUseStripeCheckoutTest = $(this).is(':checked');
		if (isUseStripeCheckoutTest) {
			$('#lwUpdateLiveStripeCheckout').attr('disabled', true);
			$('#lwUpdateTestStripeCheckout').attr('disabled', false);
			$('#lwTestStripeInputField').removeClass('lw-disabled-block-content');
			$('#lwLiveStripeInputField').addClass('lw-disabled-block-content');
		}
	});

	//check stripe test mode is false on change 
	//then disable stripe test input field	
	$("#lwUseStripeCheckoutLive").on('change', function(event) {
		var isUseStripeCheckoutLive = $(this).is(':checked');
		if (isUseStripeCheckoutLive) {
			$('#lwUpdateTestStripeCheckout').attr('disabled', true);
			$('#lwUpdateLiveStripeCheckout').attr('disabled', false);
			$('#lwLiveStripeInputField').removeClass('lw-disabled-block-content');
			$('#lwTestStripeInputField').addClass('lw-disabled-block-content');
		}
	});
	/*********** Stripe Enable / Disable Checkout end here ***********/

	/*********** Stripe Testing Keys setting start here ***********/
	var isTestStripeKeysInstalled = "<?= $configurationData['stripe_testing_publishable_key'] ?>",
		lwTestStripeInputField = $('#lwTestStripeInputField'),
		lwTestStripeCheckoutExists = $('#lwTestStripeCheckoutExists');

	// Check if test stripe keys are installed
	if (isTestStripeKeysInstalled) {
		lwTestStripeInputField.hide();
	} else {
		lwTestStripeCheckoutExists.hide();
	}
	// Update stripe checkout testing keys
	$('#lwUpdateTestStripeCheckout').click(function() {
		$("#lwStripeTestKeysExist").val(0);
		lwTestStripeInputField.show();
		lwTestStripeCheckoutExists.hide();
	});
	/*********** Stripe Testing Keys setting end here ***********/

	/*********** Stripe Live Keys setting start here ***********/
	var isLiveStripePublishKeysInstalled = "<?= $configurationData['stripe_live_publishable_key'] ?>",
		lwLiveStripeInputField = $('#lwLiveStripeInputField'),
		lwLiveStripeCheckoutExists = $('#lwLiveStripeCheckoutExists');

	// Check if test Stripe keys are installed
	if (isLiveStripePublishKeysInstalled) {
		lwLiveStripeInputField.hide();
	} else {
		lwLiveStripeCheckoutExists.hide();
	}
	// Update Stripe checkout testing keys
	$('#lwUpdateLiveStripeCheckout').click(function() {
		$("#lwStripeLiveKeysExist").val(0);
		lwLiveStripeInputField.show();
		lwLiveStripeCheckoutExists.hide();
	});
	/*********** Stripe Live Keys setting end here ***********/

	/*********** Razorpay Enable / Disable Checkout start here ***********/
	var isRazorpayCheckoutEnable = $('#lwEnableRazorpay').is(':checked'),
		isUseRazorpayCheckoutTest = $("#lwUseRazorpayCheckoutTest").is(':checked'),
		isUseRazorpayCheckoutLive = $("#lwUseRazorpayCheckoutLive").is(':checked');

	if (!isRazorpayCheckoutEnable) {
		$('#lwRazorpayCheckoutContainer').addClass('lw-disabled-block-content');
	}
	$("#lwEnableRazorpay").on('change', function(event) {
		isRazorpayCheckoutEnable = $(this).is(":checked");
		//check is enable false then add class
		if (!isRazorpayCheckoutEnable) {
			$("#lwRazorpayCheckoutContainer").addClass('lw-disabled-block-content');
			//else remove class
		} else {
			$("#lwRazorpayCheckoutContainer").removeClass('lw-disabled-block-content');
		}
	});

	//check stripe test mode is true then disable stripe live input field	
	if (isUseRazorpayCheckoutTest) {
		$('#lwUpdateLiveRazorpayCheckout').attr('disabled', true);
		$('#lwLiveRazorpayInputField').addClass('lw-disabled-block-content');
		//check razorpay test mode is false then disable razorpay test input field	
	} else if (isUseRazorpayCheckoutLive) {
		$('#lwUpdateTestRazorpayCheckout').attr('disabled', true);
		$('#lwTestRazorpayInputField').addClass('lw-disabled-block-content');
	}

	//check razorpay test mode is true on change 
	//then disable razorpay live input field	
	$("#lwUseRazorpayCheckoutTest").on('change', function(event) {
		var isUseRazorpayCheckoutTest = $(this).is(':checked');
		if (isUseRazorpayCheckoutTest) {
			$('#lwUpdateLiveRazorpayCheckout').attr('disabled', true);
			$('#lwUpdateTestRazorpayCheckout').attr('disabled', false);
			$('#lwTestRazorpayInputField').removeClass('lw-disabled-block-content');
			$('#lwLiveRazorpayInputField').addClass('lw-disabled-block-content');
		}
	});

	//check stripe test mode is false on change 
	//then disable stripe test input field	
	$("#lwUseRazorpayCheckoutLive").on('change', function(event) {
		var isUseRazorpayCheckoutLive = $(this).is(':checked');
		if (isUseRazorpayCheckoutLive) {
			$('#lwUpdateTestRazorpayCheckout').attr('disabled', true);
			$('#lwUpdateLiveRazorpayCheckout').attr('disabled', false);
			$('#lwLiveRazorpayInputField').removeClass('lw-disabled-block-content');
			$('#lwTestRazorpayInputField').addClass('lw-disabled-block-content');
		}
	});

	/*********** Razorpay Testing Keys setting start here ***********/
	var isTestRazorpayKeysInstalled = "<?= $configurationData['razorpay_testing_secret_key'] ?>",
		lwTestRazorpayInputField = $('#lwTestRazorpayInputField'),
		lwTestRazorpayCheckoutExists = $('#lwTestRazorpayCheckoutExists');

	// Check if test stripe keys are installed
	if (isTestRazorpayKeysInstalled) {
		lwTestRazorpayInputField.hide();
	} else {
		lwTestRazorpayCheckoutExists.hide();
	}
	// Update razorpay checkout testing keys
	$('#lwUpdateTestRazorpayCheckout').click(function() {
		$("#lwRazorpayTestKeysExist").val(0);
		lwTestRazorpayInputField.show();
		lwTestRazorpayCheckoutExists.hide();
	});
	/*********** Razorpay Testing Keys setting end here ***********/

	/*********** Razorpay Live Keys setting start here ***********/
	var isLiveRazorpaySecretKeysInstalled = "<?= $configurationData['razorpay_live_secret_key'] ?>",
		lwLiveRazorpayInputField = $('#lwLiveRazorpayInputField'),
		lwLiveRazorpayCheckoutExists = $('#lwLiveRazorpayCheckoutExists');

	// Check if test Stripe keys are installed
	if (isLiveRazorpaySecretKeysInstalled) {
		lwLiveRazorpayInputField.hide();
	} else {
		lwLiveRazorpayCheckoutExists.hide();
	}
	// Update Razorpay checkout testing keys
	$('#lwUpdateLiveRazorpayCheckout').click(function() {
		$("#lwRazorpayLiveKeysExist").val(0);
		lwLiveRazorpayInputField.show();
		lwLiveRazorpayCheckoutExists.hide();
	});
	/*********** Razorpay Live Keys setting end here ***********/

	/*********** Razorpay Enable / Disable Checkout end here ***********/

	//on payment setting success callback function
	function onPaymentGatewayFormCallback(responseData) {
		//check reaction code is 1 then reload view
		if (responseData.reaction == 1) {
			showConfirmation('Settings Updated Successfully', null, {
				buttons: [
					Noty.button('Reload', 'btn btn-secondary btn-sm', function() {
						__Utils.viewReload();
					})
				]
			});
		}
	};
</script>
@endpush