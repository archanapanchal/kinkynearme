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

<section class="plan-for-you subscription-plan-v1 subscription-plan">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h2 class="heading-title"><?= __tr('SUBSCRIPTION PLAN') ?></h2>
                <p class="title-para"><?= __tr('Monthly Plan you have selected') ?></p>
            </div>
        </div>
        <div id="content" class="tab-content" role="tablist">
            <div id="pane-A" class="card tab-pane fade show active" role="tabpanel" aria-labelledby="tab-A">
                <!-- Note: New place of `data-parent` -->
                <div id="collapse-A" class="collapse show" data-parent="#content" role="tabpanel" aria-labelledby="heading-A">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-5 col-md-6 subscription-border">
                                <div class="subscription-plan-desc">
                                    <div class="d-flex">
                                        <span class="back-button"><a href="<?= route('user.subscription.plans') ?>">back</a></span>
                                        <h2 class="heading-title"><?= strtoupper($planData['title']) ?></h2>
                                    </div>
                                    <?= $planData['description'] ?>
                                    <a href="<?= route('user.subscription.plan.process', ['planId' => $planData['_id']]) ?>" class="btn btn-primary"><?= __tr('PROCEED') ?></a>
                                    <a href="<?= route('user.subscription.plans') ?>" class="change-plan-button"><?= __tr('Change Plan') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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