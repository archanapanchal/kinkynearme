@section('page-title', __tr('Subscription plans'))
@section('head-title', __tr('Subscription plans'))
@section('keywordName', strip_tags(__tr('Subscription plans')))
@section('keyword', strip_tags(__tr('Subscription plans')))
@section('description', strip_tags(__tr('Subscription plans')))
@section('keywordDescription', strip_tags(__tr('Subscription plans')))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- include header -->
@include('includes.header')
<!-- /include header -->

<script type="text/javascript" src="https://jstest.authorize.net/v3/AcceptUI.js" charset="utf-8"></script>
<section class="plan-for-you subscription-plan-v1">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <span class="back-button"><a href="<?= route('user.profile.build') ?>"><?= __tr('Back') ?></a></span>
                <h2 class="heading-title"><?= __tr('SUBSCRIPTION PLAN') ?></h2>                
                <p class="title-para"><?= __tr('Monthly Plan you have selected') ?></p>
            </div>
        </div>

        <ul id="tabs" class="nav nav-tabs" role="tablist">
            <?php $planTypes = configItem('plan_settings.type');    ?>

            @php
            $i = 0
            @endphp
            <?php //echo "<pre>"; print_r($plans); exit;?>
            @foreach($plans as $type => $planList)
            <?php if($type != 3){ ?>
                <li class="nav-item">
                    <a id="tab-<?= $type ?>" href="#pane-<?= $type ?>" class="nav-link @if ($i == 1) active @endif" data-toggle="tab" role="tab"><?= __tr('Bill') ?> <?= isset($planTypes[$type]) ? $planTypes[$type] : null; ?></a>
                </li>
            <?php } ?>
            @php
            $i++
            @endphp
            @endforeach
        </ul> 
        <div id="content" class="tab-content" role="tablist">

            @php
            $i = 0
            @endphp

            @foreach($plans as $type => $planList)
                <?php if($type != 3){ ?>
                    <div id="pane-<?= $type ?>" class="card tab-pane fade @if ($i == 1) show active @endif" role="tabpanel" aria-labelledby="tab-<?= $type ?>">
                        <!-- Note: New place of `data-parent` -->
                        <div id="collapse-<?= $type ?>" class="collapse @if ($i == 1) show @endif" data-parent="#content" role="tabpanel" aria-labelledby="heading-<?= $type ?>">
                            <div class="card-body">
                                <div class="row mobile-hide">
                                    @foreach($planList as $type => $plan)

                                                    <form  class="subscription-border">
                                                                    
                                                                <div class="subscription-plan-desc">
                                                                    <h2 class="heading-title"><?= strtoupper($plan['title']) ?></h2>
                                                                    <?php 
                                                                        if ($plan['title'] == "Gold") { ?>
                                                                           <span class="tag">popular</span>
                                                                        <?php } 

                                                                        if ($plan['price'] != 0) { ?>
                                                                           
                                                                        <span class="plan_price">$<?= $plan['price'] ?></span>
                                                                       <?php  }
                                                                       $totalCharge = $plan['price'] + 5.95;
                                                                        ?>
                                                                    <?= $plan['content'] ?>
                                                                    
                                                                    <input type="hidden" id="section_amount_choose" name="amount" value="<?= $totalCharge ?>" />
                                                                    <input type="hidden" id="section_plan_choose" name="section_plan" value="<?= $plan['_id'] ?>" />
                                                                    <input type="hidden" id="section_plan_name_choose" name="section_plan" value="<?= $plan['title'] ?>" />
                                                                    <input type="hidden" id="section_plan_type_choose" name="section_plan_type" value="<?= $plan['plan_type'] ?>" />
                                                                    
                                                                    
                                                                    <button type="button"
                                                                        class="select_plan_choose btn btn-primary choose-button"><?= __tr('PROCEED') ?>
                                                                    </button>
                                                                
                                                                </div>
                                                        </form>
                                                    @endforeach


                                    <form id="paymentForm_choose" method="post" action="<?= route('user.subscription_charge') ?>" style="display:none">
                                                        {{ csrf_field() }}
                                                            <input type="hidden" id="req_amount_choose" name="amount" value="">
                                                            <input type="hidden" id="req_plan_id_choose" name="plan_id" value="">
                                                            <input type="hidden" id="req_plan_name_choose" name="plan_name" value="">
                                                            <input type="hidden" id="req_plan_type_choose" name="plan_type" value="">
                                                            <input type="hidden" name="dataValue" id="dataValue" />
                                                            <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
                                                            <button type="button"
                                                                        class="AcceptUI btn btn-primary choose-button AcceptUI_chhose"
                                                                        data-billingAddressOptions='{"show":true, "required":false}' 
                                                                        data-apiLoginID="95EAnb9tB8" 
                                                                        data-clientKey="45G6Hr7DmcfR8u3GM3Fzb5z4pnQ33xVvK7n5qAEZtYdxy9efRG42svh7Lau5XLVy"
                                                                        data-acceptUIFormBtnTxt="Submit" 
                                                                        data-acceptUIFormHeaderTxt="Card Information : To maintain discretion, your payment will appear as 'KNM Media LLC' on your billing statement. Your invoice includes your Membership and a $5.95 handling fee."
                                                                        data-paymentOptions='{"showCreditCard": true, "showBankAccount": false}' 
                                                                        data-responseHandler="responseHandler">Pay
                                                                    </button>
                                                    </form>


                                </div>
                                <div    class="owl-carousel sld2 logos mobile-show">
                                    @foreach($planList as $type => $plan)
                                    <div class="col-lg-3 col-md-6 col-sm-12 col-sx-12 subscription-border">
                                        <div class="subscription-plan-desc">
                                            <h2 class="heading-title"><?= strtoupper($plan['title']) ?></h2>
                                            <?= $plan['content'] ?>
                                            <a href="<?= route('user.subscription.plan.process', ['planId' => $plan['_id']]) ?>" class="btn btn-primary choose-button"><?= __tr('PROCEED') ?></a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <!-- slider-end -->
                            </div>
                        </div>
                    </div>
                <?php } ?>
            @php
            $i++
            @endphp
            @endforeach
        </div>
    </div>
</section>



@push('appScripts')
    <script>
        $(".select_plan_choose").on("click",function(){
            var section_amount = $(this).prev().prev().prev().prev( "#section_amount_choose" ).val();
            var section_plan = $(this).prev().prev().prev( "#section_plan_choose" ).val();
            var section_plan_name = $(this).prev().prev( "#section_plan_name_choose" ).val();
            var section_plan_type = $(this).prev( "#section_plan_type_choose" ).val();

           
            
            $('#paymentForm_choose > input#req_amount_choose').val(section_amount);
            $('#paymentForm_choose > input#req_plan_id_choose').val(section_plan);
            $('#paymentForm_choose > input#req_plan_name_choose').val(section_plan_name);
            $('#paymentForm_choose > input#req_plan_type_choose').val(section_plan_type);
        $( ".AcceptUI" ).trigger( "click" );
        //alert(section_amount);
    });


    function responseHandler(response) {
        
        if (response.messages.resultCode === "Error") {
            var i = 0;
            while (i < response.messages.message.length) {
                console.log(
                    response.messages.message[i].code + ": " +
                    response.messages.message[i].text
                );
                i = i + 1;
            }
        } else {
            paymentFormUpdate(response.opaqueData);
        }
    }


    function paymentFormUpdate(opaqueData) {
        document.getElementById("dataDescriptor").value = opaqueData.dataDescriptor;
        document.getElementById("dataValue").value = opaqueData.dataValue;

        // If using your own form to collect the sensitive data from the customer,
        // blank out the fields before submitting them to your server.
        /*document.getElementById("cardNumber").value = "";
        document.getElementById("expMonth").value = "";
        document.getElementById("expYear").value = "";
        document.getElementById("cardCode").value = "";*/

        document.getElementById("paymentForm_choose").submit();
    }
    
</script>
@endpush


<!-- include footer -->
@include('includes.footer')
<!-- /include footer -->