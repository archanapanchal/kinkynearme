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


<section class="plan-for-you subscription-plan-v1">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <span class="back-button"><a href="<?= route('user.profile.build') ?>"><?= __tr('Back') ?></a></span>
                <h2 class="heading-title"><?= __tr('SUBSCRIPTION PLAN') ?></h2>
                <p class="title-para"><?= __tr('Monthly Plan you have selected') ?></p>
            </div>
        </div>

        <!-- <ul id="tabs" class="nav nav-tabs" role="tablist">
            <?php $planTypes = configItem('plan_settings.type');    ?>

            @php
            $i = 0
            @endphp

            @foreach($plans as $type => $planList)
            <li class="nav-item">
                <a id="tab-<?= $type ?>" href="#pane-<?= $type ?>" class="nav-link @if ($i == 0) active @endif" data-toggle="tab" role="tab"><?= __tr('Bill') ?> <?= isset($planTypes[$type]) ? $planTypes[$type] : null; ?></a>
            </li>
            @php
            $i++
            @endphp
            @endforeach
        </ul> -->
        <div id="content" class="tab-content" role="tablist">

            @php
            $i = 0
            @endphp

            @foreach($plans as $type => $planList)
            <div id="pane-<?= $type ?>" class="card tab-pane fade @if ($i == 0) show active @endif" role="tabpanel" aria-labelledby="tab-<?= $type ?>">
                <!-- Note: New place of `data-parent` -->
                <div id="collapse-<?= $type ?>" class="collapse @if ($i == 0) show @endif" data-parent="#content" role="tabpanel" aria-labelledby="heading-<?= $type ?>">
                    <div class="card-body">
                        <div class="row mobile-hide">
                            @foreach($planList as $type => $plan)
                            <div class="col-lg-3 col-md-6 subscription-border">
                                <div class="subscription-plan-desc">
                                    <h2 class="heading-title"><?= strtoupper($plan['title']) ?></h2>
                                    <?= $plan['content'] ?>
                                    <a href="<?= route('user.subscription.plan.process', ['planId' => $plan['_id']]) ?>" class="btn btn-primary choose-button"><?= __tr('PROCEED') ?></a>
                                    
                                </div>
                            </div>
                            @endforeach
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
            @php
            $i++
            @endphp
            @endforeach
        </div>
    </div>
</section>



@push('appScripts')
    <script>
    
</script>
@endpush


<!-- include footer -->
@include('includes.footer')
<!-- /include footer -->