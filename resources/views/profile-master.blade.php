<!-- include header -->
@include('includes.header')
<!-- /include header -->
    <section class="tab-section account-tab">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php if (Request::segment(2) != "edit_profile" && Request::segment(1) != "charge" && Request::segment(2) != "payment_success" && Request::segment(2) != "payment_fail") {?>
                    <ul class="nav nav-tabs" role="tablist">

                        <?php
                           $subscription_detail =  App\Yantrana\Components\User\Models\UserSubscription::where('users__id', getUserID())->where('status', 1)->get()->toArray();

                           if (!empty($subscription_detail)) {
                            $plan_id = $subscription_detail[0]['plan_id'];
                            $check_7_days_plan = App\Yantrana\Components\Plans\Repositories\ManagePlansRepository::fetch($plan_id);

                            if ($check_7_days_plan->title == "7 Days Trial") {
                                   $plan_checked = "none";
                            }else{
                                $plan_checked = "";
                            }

                        }                           
                        ?>

                        <?php 
                        $expiry_date = "";
                        $currentdate = "";
                        if(!empty($subscription_detail)){
                            if (isset($subscription_detail[0]['expiry_at'])) {
                               $theDate    = new DateTime($subscription_detail[0]['expiry_at']);
                                $expDate = $theDate->format('Y-m-d');
                                $expiry_date = strtotime($expDate);
                                $currentdate = strtotime(date("Y-m-d"));
                            }

                            $theDate    = new DateTime($subscription_detail[0]['expiry_at']);
                            $expDate = $theDate->format('Y-m-d');
                            $expiry_date = strtotime($expDate);
                            $currentdate = strtotime(date("Y-m-d"));
                        }
                      
                        if ($userProfileData['subscription_detail'] == 'yes'&& $expiry_date >= $currentdate) { 
                            //if ($userProfileData['subscription_detail'] == 'yes') { ?>
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 1) active @endif" href="<?= route('user.read.find_matches') ?>" role="tab">People near me</a>
                        </li>
                        <li class="nav-item">

                            <a class="nav-link @if($active_tab == 2) active @endif" href="<?= route('user.like') ?>" role="tab">Likes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 3) active @endif" style="pointer-events:<?= $plan_checked ?>" href="<?= route('user.like_your_profile') ?>" role="tab">Liked You</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 4) active @endif" href="<?= route('user.mutual_like_view') ?>" role="tab">Matches</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 5) active @endif" @if(getUserPlan(Auth::user()->_id) != config('constant.trial')) href="<?= route('user.my_favourite_view') ?>" @endif role="tab">Favorite</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 9) active @endif" @if((getUserPlan(Auth::user()->_id) != config('constant.trial')) && getUserPlan(Auth::user()->_id) != config('constant.silver')) href="<?= route('user.who_view_profile') ?>"  @endif role="tab">Who Viewed Your Profile</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 6) active @endif" style="pointer-events:<?= $plan_checked ?>" href=" @if(!empty($userMatchData['data'])) <?= url('user/messages') ?> @endif" role="tab" >Messages</a>
                        </li>
                       
                        
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 7) active @endif" style="pointer-events:<?= $plan_checked ?>" @if(getUserPlan(Auth::user()->_id) != config('constant.trial')) href="<?= url('user/discussion-forum') ?>" @endif role="tab">Forum</a>
                        </li>
                        

                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 8) active @endif" href="<?= route('landing_page') ?>" role="tab">My account</a>
                        </li>
                        <?php }  else { ?>

                            @if(!empty($subscription_detail))
                            <li class="nav-item">
                                <a class="nav-link @if($active_tab == 1) active @endif" href="#" role="tab"style="pointer-events:none;">People near me dd</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($active_tab == 2) active @endif" href="#" role="tab"style="pointer-events:none;">Like</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($active_tab == 3) active @endif" href="#" role="tab"style="pointer-events:none;">Liked Your Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($active_tab == 4) active @endif" href="#" role="tab"style="pointer-events:none;">Matches</a>
                            </li>
                            <li class="nav-item">
                               <a class="nav-link @if($active_tab == 5) active @endif" href="#" role="tab"style="pointer-events:none;">Favorite</a>
                            </li>
                            <li class="nav-item">
                               <a class="nav-link @if($active_tab == 9) active @endif" @if((getUserPlan(Auth::user()->_id) != config('constant.trial')) || getUserPlan(Auth::user()->_id) != config('constant.silver')) href=""  @endif role="tab" style="pointer-events:none;">Who Viewed Your Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($active_tab == 6) active @endif" href="#" role="tab"style="pointer-events:none;">Messages</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($active_tab == 7) active @endif" href="#" role="tab" style="pointer-events:none;">Forum</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($active_tab == 8) active @endif" href="" role="tab">My account</a>
                            </li>
                            @endif 
                        <?php }?>

                        <?php 
                        if ($userProfileData['subscription_detail'] == 'no') { ?>

                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 1) active @endif" href="#" role="tab"style="pointer-events:none;">People near me</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 2) active @endif" href="#" role="tab"style="pointer-events:none;">Like</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 3) active @endif" href="#" role="tab"style="pointer-events:none;">Liked Your Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 4) active @endif" href="#" role="tab"style="pointer-events:none;">Matches</a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link @if($active_tab == 5) active @endif" href="#" role="tab"style="pointer-events:none;">Favorite</a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link @if($active_tab == 9) active @endif" href="#" role="tab"style="pointer-events:none;">Who Viewed Your Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 6) active @endif" href="#" role="tab"style="pointer-events:none;">Messages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 7) active @endif" href="#" role="tab" style="pointer-events:none;">Forum</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($active_tab == 8) active @endif" href="<?= route('landing_page') ?>" role="tab">My account</a>
                        </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                    
                    <!-- Tab panes -->
                    <div class="tab-content">

                        @if(isset($pageRequested))
                        <?php echo $pageRequested; ?>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
                    

    <div class="lw-cookie-policy-container row p-4 d-none" id="lwCookiePolicyContainer">
        <div class="col-sm-11">
            @include('includes.cookie-policy')
        </div>
        <div class="col-sm-1 mt-2"><button id="lwCookiePolicyButton" class="btn btn-primary"><?= __tr('OK') ?></button></div>
    </div>
    <!-- include footer -->
    @include('includes.footer')
    <!-- /include footer -->

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?= __tr('Ready to Leave?') ?></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?= __tr('Select "Logout" below if you are ready to end your current session.') ?>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?= __tr('Not now') ?></button>
                    <a class="btn btn-primary" href="<?= route('user.logout') ?>"><?= __tr('Logout') ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- /Logout Modal-->
</body>

</html>