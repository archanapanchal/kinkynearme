<?php $userName = $userData['userName'];  ?>
@section('page-title', strip_tags($userData['userName']))
@section('head-title')
    <?php echo "My account - ".$userData['userName']." - Kinky Near Me"; ?>
@endsection

@section('page-url', url()->current())

@if(isset($userData['aboutMe']))
@section('keywordName', strip_tags($userProfileData['aboutMe']))
@section('keyword', strip_tags($userProfileData['aboutMe']))
@section('description', strip_tags($userProfileData['aboutMe']))
@section('keywordDescription', strip_tags($userProfileData['aboutMe']))
@endif

@if(isset($userData['profilePicture']))
@section('page-image', $userData['profilePicture'])
@endif
@if(isset($userData['coverPicture']))
@section('twitter-card-image', $userData['coverPicture'])
@endif

<?php // echo "<pre>"; print_r($userSpecificationTexts);exit(); ?>
<script type="text/javascript" src="https://jstest.authorize.net/v3/AcceptUI.js" charset="utf-8"></script>

    <div class="tab-pane active" id="tabs-6" role="tabpanel">
        <div class="my-account">
            @if($isOwnProfile)
            <div class="tab">                
                <button class="tablinks my-profile" onclick="openCity(event, 'my-profile')" id="defaultOpen">My Profile</button>
                <button class="tablinks manage-pass" onclick="openCity(event, 'manage-pass')">Manage Password</button>
                <button class="tablinks subscription" onclick="openCity(event, 'subscription')">Subscription</button>

                <?php 
                if ($userProfileData['subscription_detail'] == 'no') { ?>
                    <button class="tablinks">Blocked Profiles</button>
                    <button class="tablinks">Notification Settings</button>
                    <button class="tablinks delete-profile" onclick="openCity(event, 'delete-profile')">Delete Profile</button>
                <?php }?>

                <?php 
                if ($userProfileData['subscription_detail'] == 'yes') { ?>
                    <button class="tablinks blocked-profiles" onclick="openCity(event, 'blocked-profiles')">Blocked Profiles</button>
                    <button class="tablinks settings" onclick="openCity(event, 'settings')">Notification Settings</button>
                    <button class="tablinks delete-profile" onclick="openCity(event, 'delete-profile')">Delete Profile</button>

                <?php }?>

                    
                 

                
            </div>
            @endif
                <div id="my-profile" class="tabcontent">

                    <div class="my-profile-section">
                        <div class="left-right-side-my-profile">
                            <div class="left-side-my-profile">
                                <img class="lw-profile-thumbnail lw-photoswipe-gallery-img lw-lazy-img" id="lwProfilePictureStaticImage" data-src="<?= imageOrNoImageAvailable($userData['profilePicture']) ?>">
                            </div>

                            <div class="right-side-my-profile">
                                <div class="profile-title-edit d-flex-class">
                                    <div class="profile-title">

                                        @if(!$isOwnProfile)
                                        <a class="mr-3 btn-link btn d-none" onclick="getChatMessenger('<?= route('user.read.individual_conversation', ['specificUserId' => $userData['userId']]) ?>')" href id="lwMessageChatButton" data-chat-loaded="false" data-toggle="modal" data-target="#messengerDialog"><i class="far fa-comments fa-3x"></i>
                                        </a>

                                        @endif

                                        <h3><?= $userData['fullName'] ?></h3>
                                        <!-- show user online, idle or offline status -->
                                        @if($userOnlineStatus == 1)
                                        <span><img src="<?= url('dist/images/dots-thik.svg') ?>"><?= __tr("Online") ?></span>
                                        @elseif($userOnlineStatus == 2)
                                        <span><img src="<?= url('dist/images/dots-thik.svg') ?>"><?= __tr("Idle") ?></span>
                                        @elseif($userOnlineStatus == 3)
                                        <span><img src="<?= url('dist/images/dots-thik.svg') ?>"><?= __tr("Offline") ?></span>
                                        @endif
                                        <!-- /show user online, idle or offline status -->
                                        
                                    </div>
                                    @if($isOwnProfile)
                                    <div class="profile-edit">
                                        <a href="<?= route('user.edit.profile_view', ['username' => $userData['userName']]) ?>" class="edit">Edit Profile</a>
                                        <!-- <a href="#" class="profile-icon"><img src="<?php //url('dist/images/share-icon.png') ?>"></a> -->
                                    </div>
                                    @endif
                                </div>
                                <!-- end -->
                                <div class="address-social-icon d-flex-class">
                                    <div class="address">
                                        <p>@if(!__isEmpty($userData['userAge'])) <?= __tr($userData['userAge']) ?>,@endif <?= __ifIsset($userProfileData['gender_text'], $userProfileData['gender_text'], '-') ?><br><?= $userProfileData['city'] ?><?php if (!empty($userProfileData['city'])) {
                                            echo ",";
                                        } ?> <?= $userProfileData['country_name'] ?></p>
                                    </div>
                                    <div class="social-icon">
                                        @if(!$isOwnProfile)
                                            <a class="icon d-none" onclick="getChatMessenger('<?= route('user.read.individual_conversation', ['specificUserId' => $userData['userId']]) ?>')" href id="lwMessageChatButton" data-chat-loaded="false" data-toggle="modal" data-target="#messengerDialog"><img src="<?= url('dist/images/message.svg') ?>"></a>

                                            <a class="icon" ><img src="<?= url('dist/images/message.svg') ?>"></a>

                                            <a href data-action="<?= route('user.write.like_dislike', ['toUserUid' => $userData['userUId'], 'like' => 1]) ?>" data-method="post" data-callback="onLikeCallback" title="Like" class="icon lw-ajax-link-action lw-like-action-btn" id="lwLikeBtn">
                                                <span class="<?= (isset($userLikeData['like']) and $userLikeData['like'] == 1) ? 'lw-is-active' : '' ?>"><img src="<?= url('dist/images/heart-icon.svg') ?>"></span>
                                            </a>

                                            <a href data-action="<?= route('user.write.favourite', ['toUserUid' => $userData['userUId'], 'favourite' => 1]) ?>" data-method="post" data-callback="onFavouriteCallback" title="Favorite" class="icon lw-ajax-link-action lw-favourite-action-btn" id="lwFavouriteBtn">
                                                <span class="<?= (isset($userFavouriteData['favourite']) and $userFavouriteData['favourite'] == 1) ? 'lw-is-active' : '' ?>"><img src="<?= url('dist/images/star.svg') ?>"></span>
                                            </a>

                                        @endif
                                    </div>
                                </div>
                                <!-- end -->
                                <div class="open-for-relation">
                                    <!-- <div class="open-title-edit d-flex-class">
                                        <h5>Open for relationships</h5>
                                        @if($isOwnProfile)
                                        <a href="#" class="edit"></a>
                                        @endif
                                    </div> -->

                                    <div class="open-title-edit d-flex-class">
                                        @if(!empty($userSpecificationTexts['our_sexual_orientation']))
                                            <h5>My Sexual Orientation <?= $userSpecificationTexts['our_sexual_orientation'] ?></h5>
                                        @else
                                            <h5>Open for relationships</h5>
                                        @endif
                                        
                                        
                                    </div>
                                    <div class="open-desc">
                                        <ul>
                                            <li>
                                                <span>Looking for</span>
                                                <p>@if(!empty($userSpecificationData))
                                                        @foreach(collect($userSpecificationData['step-1']['items'])->chunk(2) as $specification) 
                                                            @foreach($specification as $itemKey => $item)
                                                                @if($item['name'] == 'looking_for')
                                                                    <?php 
                                                                        //$selected_options = json_decode($item['selected_options']);
                                                                       //preg_replace('/(,)(-)(?=[^\s])/', ', ', str_replace(['"','[',']'], '',  ucfirst($item['selected_options'])))
                                                                    ?>
                                                                    {{ucwords(str_replace(array(",", "-"), array(", ", " "), $item['selected_options']))}}

                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                    @endif
                                                     
                                            </li>
                                            <li>
                                                <span>Interests/Kinks</span>
                                                <p>
                                                    @if(!empty($userSpecificationData))
                                                        @foreach(collect($userSpecificationData['step-1']['items'])->chunk(2) as $specification) 
                                                            @foreach($specification as $itemKey => $item)
                                                                @if($item['name'] == 'kinks')
                                                                    <?php 
                                                                        //$selected_options = json_decode($item['selected_options']);
                                                                       //preg_replace('/(,)(-)(?=[^\s])/', ', ', str_replace(['"','[',']'], '',  ucfirst($item['selected_options'])))
                                                                    ?>
                                                                    {{ucwords(str_replace(array(",", "-"), array(", ", " "), $item['selected_options']))}}
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                    @endif
                                                </p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- end -->
                                <div class="physical-appearance">
                                    <div class="open-desc d-flex-class">
                                        <span>Physical Appearance</span>
                                        <span>Ethnicity</span>
                                    </div>
                                    <div class="open-desc">
                                        <ul>
                                            <li>
                                                <img src="<?= url('dist/images/app-1.png') ?>">
                                                <p><?= $userSpecificationTexts['body_type'] ?></p>
                                            </li>
                                            <li>
                                                <img src="<?= url('dist/images/app-2.png') ?>">
                                                <p><?= $userSpecificationTexts['hair_color'] ?></p>
                                            </li>
                                            <li>
                                                <img src="<?= url('dist/images/app-5.png') ?>">
                                                <p><?= $userSpecificationTexts['eye_color'] ?></p>
                                            </li>
                                            <li>
                                                <img src="<?= url('dist/images/app-4.png') ?>">
                                                <p><?= $userSpecificationTexts['height'] ?></p>
                                            </li>
                                            <li class="open-desc-ethnicity">
                                                <img src="<?= url('dist/images/app-3.png') ?>">
                                                <p><?= $userSpecificationTexts['ethnicity'] ?></p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- end -->
                                <div class="physical-appearance life-style">
                                    <div class="open-desc d-flex-class">
                                        <span>Lifestyle</span>
                                    </div>
                                    <div class="open-desc">
                                        <ul>
                                            <li>
                                                <img src="<?= url('dist/images/app-6.png') ?>">
                                                <p><?= $userSpecificationTexts['smoke'] ?></p>
                                            </li>
                                            <li>
                                                <img src="<?= url('dist/images/app-7.png') ?>">
                                                <p><?= $userSpecificationTexts['drink'] ?></p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- end -->
                            </div>
                            <!-- end -->
                        </div>

                        @if(isset($userProfileData['aboutMe']) and $userProfileData['aboutMe'])
                            <div class="my-profile-about">
                                <p class="about-title"><?= __tr('About Me') ?></p>
                                <div class="about-content">
                                    <?= __ifIsset($userProfileData['aboutMe'], $userProfileData['aboutMe'], '-') ?>
                                </div>
                            </div>
                        @endif


                        @if(!__isEmpty($photosData) or $isOwnProfile)
                        <div class="my-profile-images">
                            <p class="about-title"><?= __tr('More photos') ?></p>
                            @if(!__isEmpty($photosData))
                            <div class="row">
                                @foreach($photosData as $key => $photo)
                                <?php
                                   $image_url_ex = explode('/users',$photo['image_url']);
                                   $image_url_ex1 = explode('/',$image_url_ex[1]);
                                ?>
                                <div class="col-md-3"><img class="lw-user-photo lw-photoswipe-gallery-img lw-lazy-img" data-img-index="<?= $key ?>" data-src="<?= imageOrNoImageAvailable($image_url_ex[0].'/users/'.getUserID().'/'.$image_url_ex1[2].'/'.$image_url_ex1[3]) ?>"></div>
                                @endforeach
                              
                            </div>
                            @else
                            <p><?= __tr('Oops... No images found...') ?></p>
                            @endif
                        </div>
                        @endif

                        @if(!empty($userData['user_videos'])) 
                            <div class="my-profile-video">
                                <p class="about-title"><?= __tr('More videos') ?></p>
                                <div class="row">
                                    @foreach ($userData['user_videos'] as $key => $video)
                                        <div class="col-md-3"><iframe width="420" height="315" src="<?= $video['url'] ?>"></iframe> </div>
                                    @endforeach
                                  
                                </div>
                            </div>
                        @endif
                        
                    </div>
                </div>
                <div id="manage-pass" class="tabcontent form-login" style="display:none;">
                  <h4 class="mangage-pass-title">Change Password</h4>
                  
                  <form class="form-field-fontent lw-ajax-form lw-form <?= (isset($userPassword) and $userPassword == 'NO_PASSWORD') ? 'lw-disabled-block-content' : '' ?>" method="post" action="<?= route('user.change_password.process') ?>" data-callback="onChangePasswordCallback" id="lwChangePasswordForm">
                     <div class="form-group">
                        <label for="inputAddress">Old Password</label>
                        <input type="password" class="third" name="current_password" placeholder="******" required minlength="6" id="lwCurrentPassword">
                        @error('current_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                     </div>
                     <div class="form-group">
                        <label for="inputAddress">New Password</label>
                        <input type="password" class="third" name="new_password" placeholder="******" id="lwNewPassword" required minlength="6">
                        @error('new_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                     </div>
                     <div class="form-group">
                        <label for="inputAddress">Confirm password</label>
                        <input type="password" name="new_password_confirmation" class="third" placeholder="******" id="lwNewPassword" required minlength="6">
                        @error('new_password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                     </div>
                     <div class="login-button">
                        
                        <button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile">reset password</button>
                    </div>
                  </form>
                </div>
                <div id="subscription" class="tabcontent"  style="display:none;">
                  <div class="profile-title-edit d-flex-class">
                     <div class="profile-title">
                        
                        <h3><?php echo $userData['first_name']." ".$userData['last_name']; ?></h3>
                        <span><img src="{{ url('dist/images/dots-thik.svg') }}">Online</span>
                     </div>

                     <div class="profile-edit">
                        <?php 
                        if ($userProfileData['subscription_detail'] == "no") { ?>
                            <a href="#" data-toggle="modal" data-target=".choose_new_pan">Choose Plan</a>
                        <?php } ?>
                                           
                     </div>
                  </div>
                  
                  <?php
                        if (empty($plan_deatail)) { ?>
                                <div class="subscription-info">
                                 <table class="table">
                                    <tbody>
                                       <tr>
                                          <td>Subscription Plan detail not found !!</td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                        <?php }else{
                                $startDate = $plan_deatail['created_at'];  
                                $expiryDate = $plan_deatail['expiry_at'];  
                                $subscription_date_data = date("F d Y", strtotime($startDate));  
                                $expiry_date_data = date("F d Y", strtotime($expiryDate));
                                $subscription_duration = 'Quarterly';                                
                                if ($plan_deatail['plan_type'] == 1) {
                                    $subscription_duration = 'Monthly';
                                   
                                } else if($plan_deatail['plan_type'] == 3){
                                    $subscription_duration = '7 days';
                                }?>

                                <div class="subscription-info">
                                 <table class="table">
                                    <tbody>
                                       <tr>
                                          <td>
                                             <span>Current active plan</span>
                                             <p><?php echo $plan_deatail['title']; ?></p>
                                          </td>
                                          <td>
                                             <span>Subscription Duration</span>
                                             <p><?php echo $subscription_duration; ?></p>
                                          </td>
                                          <td>
                                             <span>Start Date</span>
                                             <p><?php echo $subscription_date_data; ?></p>
                                          </td>
                                          <td>
                                             <span>Expires on</span>
                                             <p><?php echo $expiry_date_data; ?></p>
                                          </td>
                                          <td>
                                             <span>Renewal</span>

                                             
                                             <?php
                                                    //echo "<pre>";print_r($plan_deatail['renewal_sts']);exit();
                                                 if ($plan_deatail['renewal_sts'] == 1) { ?>
                                                     <button type="button" id="renew_subscription" class="btn btn-sm btn-toggle active" data-toggle="button" aria-pressed="true" autocomplete="on">
                                                      <div class="handle"></div>
                                                   </button>
                                                 <?php } else { ?>
                                                     <button type="button" id="renew_subscription" class="btn btn-sm btn-toggle first-off active" data-toggle="button" aria-pressed="false" autocomplete="off">
                                                      <div class="handle"></div>
                                                   </button>
                                               <?php }
                                                    //echo "<pre>";print_r($renewal_sts);exit();    
                                             ?>

                                             <!-- <a href id="renew_subscription" data-method="post" title="Subscription" class="btn btn-sm btn-toggle active" id="lwFavouriteBtn" data-toggle="button" aria-pressed="true" autocomplete="off"><div class="handle"></div>
                                             </a> -->

                                             
                                          </td>
                                          <td class="change-plan choose-button"><a href="#" data-toggle="modal" data-target=".choose_already_selecsted_pan">CHANGE PLAN</a></td>

                                          <td class="cancel-sub"><a class="discussion-forum-delete-button" title="<?= __tr('Delete Topic') ?>" href="#" data-toggle="modal" data-target="#lwCancelSubscriptionProfileModel">Cancel Subscription</a></td>

                                          <!-- <td class="cancel-sub"><a href id="cancel_subscription" data-method="post" title="Subscription" id="lwFavouriteBtn" data-toggle="button" aria-pressed="true" autocomplete="off"><div class="handle"></div>Cancel Subscription</a></td> -->
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>

                      <?php  }
                    ?>
               </div>


                <div class="modal fade" id="lwCancelSubscriptionProfileModel" tabindex="-1" role="dialog" aria-labelledby="cancelSubscriptionProfileModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= __tr('Cancel Subscription') ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form class="user lw-ajax-form lw-form" method="GET" action="<?= route('user.cancel.subscription') ?>">
                                    <?= __tr("After Cancel Subscription you won't be able to access your account.?") ?>
                                    <hr />
                                    <input type="hidden" name="userUId" value="{{$userData['userUId']}}">
                                    <button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block-on-mobile"><?= __tr('Cancel Subscription')  ?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

               <div class="modal fade bd-example-modal-lg choose_already_selecsted_pan plan-for-you" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    
                                    <h2 class="heading-title"><?= __tr('SUBSCRIPTION PLAN') ?></h2>                
                                    <p class="title-para"><?= __tr('Monthly Plan you have selected') ?></p>
                                </div>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>

                          <ul id="tabs" class="nav nav-tabs" role="tablist">
                                <?php $planTypes = configItem('plan_settings.type');    ?>

                                @php
                                $i = 0
                                @endphp

                                @foreach($planbytype as $type => $planList)
                                <?php if($type != 3){ ?>
                                    <li class="nav-item">
                                        <a id="tab-<?= $type ?>" href="#pane-<?= $type ?>" class="nav-link @if ($i == 1) active @endif" data-toggle="tab" role="tab"><?= __tr('Bill') ?> <?= isset($planTypes[$type]) ? $planTypes[$type] : null; ?></a>
                                    </li>
                                <?php }?>
                                @php
                                $i++
                                @endphp
                                @endforeach
                            </ul>
                            <div id="content" class="tab-content" role="tablist">

                            @php
                            $i = 0
                            @endphp

                            @foreach($planbytype as $type => $planList)
                                <?php if($type != 3){ ?>
                                    <div id="pane-<?= $type ?>" class="card tab-pane fade @if ($i == 1) show active @endif" role="tabpanel" aria-labelledby="tab-<?= $type ?>">
                                        <!-- Note: New place of `data-parent` -->
                                        <div id="collapse-<?= $type ?>" class="collapse @if ($i == 1) show @endif" data-parent="#content" role="tabpanel" aria-labelledby="heading-<?= $type ?>">

                                            <div class="card-body">
                                                <div class="row mobile-hide">

                                                    <?php 
                                                        //echo "<pre>";print_r($planList);exit();
                                                    ?>

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

                                                                        ?>
                                                                    <?= $plan['content'] ?>

                                                                    <?php 
                                                                    $totalCharge = $plan['price'] + 5.95;
                                                                    ?>


                                                                    
                                                                    <input type="hidden" id="section_amount" name="amount" value="<?= $totalCharge ?>" />
                                                                    <input type="hidden" id="section_plan" name="section_plan" value="<?= $plan['_id'] ?>" />                              
                                                                    <input type="hidden" id="section_plan_name" name="section_plan_name" value="<?= $plan['title'] ?>" />
                                                                    <input type="hidden" id="section_plan_type" name="section_plan_type" value="<?= $plan['plan_type'] ?>" />

                                                                    <?php
                                                                    if (!empty($plan_deatail)) {
                                                                if ($plan_deatail['title'] == $plan['title'] && $plan_deatail['plan_type'] == $plan['plan_type']) { ?>
                                                                    <a href="" style="pointer-events:none;" class="btn btn-primary choose-button selected-plan">SELECTED</a>
                                                                    <?php }else{ ?>
                                                                    <!-- <input type="submit" name="submit" value="" class="btn btn-primary choose-button" /> -->
                                                                    <button type="button"
                                                                        class="select_plan btn btn-primary choose-button"><?= __tr('PROCEED') ?>
                                                                    </button>
                                                                <?php } ?>
                                                           <?php } ?>
                                                                </div>
                                                        </form>

                                                    @endforeach

                                                    

                                                </div>
                                                <div class="owl-carousel sld2 logos mobile-show">
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
                  </div>
                </div>

                <form id="paymentForm" method="POST" action="{{ url('charge') }}" style="display:none">
                                                        {{ csrf_field() }}
                                                            <input type="hidden" id="req_amount" name="amount" value="">
                                                            <input type="hidden" id="req_plan_id" name="plan_id" value="">
                                                            <input type="hidden" id="req_plan_name" name="plan_name" value="">
                                                            <input type="hidden" id="req_plan_type" name="plan_type" value="">
                                                            <input type="hidden" name="dataValue" id="dataValue" />
                                                            <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
                                                            <button type="button"
                                                                        class="AcceptUI btn btn-primary choose-button"
                                                                        data-billingAddressOptions='{"show":true, "required":false}' 
                                                                        data-apiLoginID="95EAnb9tB8" 
                                                                        data-clientKey="45G6Hr7DmcfR8u3GM3Fzb5z4pnQ33xVvK7n5qAEZtYdxy9efRG42svh7Lau5XLVy"
                                                                        data-acceptUIFormBtnTxt="Submit" 
                                                                        data-acceptUIFormHeaderTxt="Card Information : To maintain discretion, your payment will appear as 'KNM Media LLC' on your billing statement. Your invoice includes your Membership and a $5.95 handling fee."
                                                                        data-paymentOptions='{"showCreditCard": true, "showBankAccount": false}' 
                                                                        data-responseHandler="responseHandler">Pay
                                                                    </button>
                                                    </form>


                <div class="modal fade bd-example-modal-lg choose_new_pan plan-for-you" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    
                                    <h2 class="heading-title"><?= __tr('SUBSCRIPTION PLAN') ?></h2>                
                                    <p class="title-para"><?= __tr('Monthly Plan you have selected') ?></p>
                                </div>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>

                          <ul id="tabs" class="nav nav-tabs" role="tablist">
                                <?php $planTypes = configItem('plan_settings.type');    ?>

                                @php
                                $i = 0
                                @endphp

                                @foreach($planbytype as $type => $planList)
                                <li class="nav-item">
                                    <a id="tab-<?= $type ?>" href="#pane_b-<?= $type ?>" class="nav-link @if ($i == 0) active @endif" data-toggle="tab" role="tab"><?= __tr('Bill') ?> <?= isset($planTypes[$type]) ? $planTypes[$type] : null; ?></a>
                                </li>
                                @php
                                $i++
                                @endphp
                                @endforeach
                            </ul>
                            <div id="content" class="tab-content" role="tablist">

                            @php
                            $i = 0
                            @endphp

                            @foreach($planbytype as $type => $planList)
                            <div id="pane_b-<?= $type ?>" class="card tab-pane fade @if ($i == 0) show active @endif" role="tabpanel" aria-labelledby="tab-<?= $type ?>">
                                <!-- Note: New place of `data-parent` -->
                                <div id="collapse-<?= $type ?>" class="collapse @if ($i == 0) show @endif" data-parent="#content" role="tabpanel" aria-labelledby="heading-<?= $type ?>">
                                    

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
                                                            
                                                            <input type="hidden" id="section_amount" name="amount" value="<?= $totalCharge ?>" />
                                                            <input type="hidden" id="section_plan" name="section_plan" value="<?= $plan['_id'] ?>" />
                                                            <input type="hidden" id="section_plan_name" name="section_plan" value="<?= $plan['title'] ?>" />
                                                            <input type="hidden" id="section_plan_type" name="section_plan" value="<?= $plan['plan_type'] ?>" />
                                                            
                                                            
                                                            <button type="button"
                                                                class="select_plan btn btn-primary choose-button"><?= __tr('PROCEED') ?>
                                                            </button>
                                                        
                                                        </div>
                                                </form>

                                            @endforeach

                                           <!--  <form id="paymentForm_choose" method="POST" action="{{ url('charge') }}" style="display:none">
                                                {{ csrf_field() }}
                                                    <input type="hidden" id="req_amount_choose" name="amount" value="">
                                                    <input type="hidden" id="req_plan_id_choose" name="plan_id" value="">
                                                    <input type="hidden" id="req_plan_name_choose" name="plan_name" value="">
                                                    <input type="hidden" id="req_plan_type_choose" name="plan_type" value="">
                                                    <input type="hidden" name="dataValue" id="dataValue" />
                                                    <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
                                                    <button type="button"
                                                                class="AcceptUI btn btn-primary choose-button AcceptUI_chhose"
                                                                id="AcceptUI_chhose"
                                                                data-billingAddressOptions='{"show":true, "required":false}' 
                                                                data-apiLoginID="95EAnb9tB8" 
                                                                data-clientKey="45G6Hr7DmcfR8u3GM3Fzb5z4pnQ33xVvK7n5qAEZtYdxy9efRG42svh7Lau5XLVy"
                                                                data-acceptUIFormBtnTxt="Submit" 
                                                                data-acceptUIFormHeaderTxt="Card Information : To maintain discretion, your payment will appear as 'KNM Media LLC' on your billing statement. Your invoice includes your Membership and a $5.95 handling fee."
                                                                data-paymentOptions='{"showCreditCard": true, "showBankAccount": false}' 
                                                                data-responseHandler="responseHandler">Pay
                                                            </button>
                                            </form> -->


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
                  </div>
                </div>

                <div class="modal fade" id="exampleModal_data<?php echo $plan['price'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Please Add Your card Detail</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                            <form action="{{ url('charge') }}" method="post" id="plandetailform">
                                {{ csrf_field() }}
                          <div class="modal-body">
                            <input type="hidden" name="amount" value="<?= $plan['price'] ?>" />
                            <input type="hidden" name="paln_id_detail" value="<?= $plan['_id'] ?>" />
                            <input type="hidden" name="paln_title_detail" value="<?= $plan['title'] ?>" />
                            <input type="hidden" name="plan_type_detail" value="<?= $plan['plan_type'] ?>" />
                            <input type="hidden" name="users_id_detail" value="<?= $userData['userId'] ?>" /> 
                              <div class="form-group">
                                <label for="cardNumber" class="col-form-label" style="display: inline-block;max-width: 100%;margin-bottom: 5px;font-weight: 700;">Card Number</label>             
                                <input style="line-height: 40px;height: auto;padding: 0 16px;display: block;width: 100%;height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.42857143;color: #555;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);box-shadow: inset 0 1px 1px rgba(0,0,0,.075);-webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;-o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;" type="text" name="cc_number" class="form-control" id="cardNumber">
                              </div>

                              <div class="form-group CVV">
                                <label for="CVV" name="cvv" class="col-form-label" style="display: inline-block;max-width: 100%;margin-bottom: 5px;font-weight: 700;">CVV</label>
                                <input type="text" style="line-height: 40px;height: auto;padding: 0 16px;display: block;width: 100%;height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.42857143;color: #555;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);box-shadow: inset 0 1px 1px rgba(0,0,0,.075);-webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;-o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;" class="form-control" id="cvv">
                              </div>

                              <div class="form-group" id="expiration-date">
                                <label style="display: inline-block;max-width: 100%;margin-bottom: 5px;font-weight: 700;">Expiration Date</label>
                                <select name="expiry_month" style="padding: 10px;">
                                    <option value="01">January</option>
                                    <option value="02">February </option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                                <select name="expiry_year" style="padding: 10px;">
                                    <option value="21"> 2021</option>
                                    <option value="22"> 2022</option>
                                    <option value="23"> 2023</option>
                                    <option value="24"> 2024</option>
                                    <option value="25"> 2025</option>
                                    <option value="26"> 2026</option>
                                    <option value="27"> 2027</option>
                                </select>
                              </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <input type="submit" name="submit" id="confirm-purchase" value="<?= __tr('Confirm') ?>" class="btn btn-default" />
                          </div>
                            </form>
                        </div>
                      </div>
                </div>


               <div id="blocked-profiles" class="tabcontent" style="display:none;" >
                  <h4 class="mangage-pass-title">Blocked Profiles</h4>
                  <div class="form-field-fontent">
                     <?php 
                    if (empty($block_user_collection)) { ?>
                        <div class="block-profile-left-right-side d-flex-class">
                            <div class="block-profile-left-side d-flex-class">
                               <div class="img-left-side"></div>
                               <div class="content-left-side">
                                  No blocked User Found!!
                               </div>
                            </div>
                            
                         </div>
                    <?php } else {
                        $user_profile = '';
                        foreach ($block_user_collection as $key => $block_user) { 
                            /*if (empty($block_user['profile_picture'])) {
                                $user_profile = noThumbImageURL();
                            } else {
                                $user_profile = $block_user['profile_picture'];
                            }*/
                        ?>
                        <!-- profile_picture -->
                        <div class="block-profile-left-right-side d-flex-class">
                            <div class="block-profile-left-side d-flex-class">
                               <div class="img-left-side"><img src="<?= $block_user['profile_picture'] ?>"></div>
                               <div class="content-left-side">
                                  <h5><?= $block_user['userFullName'] ?></h5>
                                  <p class="address"><?= $block_user['countryName'] ?></p>
                                  <p class="desig">{{ucwords(str_replace(array(",", "-"), array(", ", " "),$block_user['kinks']))}}</p>
                               </div>
                            </div>
                            <div class="block-profile-right-side change-plan">
                                <a href id="unblock_user" value="<?php echo $block_user['to_users__id']; ?>">UNBLOCK</a>
                            </div>
                         </div>

                     <?php } 
                    } ?>
                     
                  </div>
               </div>

               <?php
                    
                    
                    if (isset($userProfileData['user_setting_section_detail'])) {
                        
                        $section_detail_array_key = array_keys($userProfileData['user_setting_section_detail']);
                        
                    }else{ 
                        $section_detail_array_key = array();
                    }
               ?>
               <div id="settings" class="tabcontent" style="display:none;">
                  <h4 class="mangage-pass-title">Settings</h4>
                  <div class="form-field-fontent">
                     <div class="block-profile-left-right-side d-flex-class">
                        <div class="block-profile-left-side d-flex-class">
                           <div class="setting-para">
                              <p>Notify when someone is a match </p>
                           </div>
                        </div>
                        <div class="block-profile-right-side setting-check-box">

                            <?php 
                            if (in_array('setting_someone_match',$section_detail_array_key))
                            { 
                                if ($userProfileData['user_setting_section_detail']['setting_someone_match'] == 1) {
                                    echo "<button type='button' name='someone_match' id='someone_match' class='btn btn-sm btn-toggle active' data-toggle='button' aria-pressed='true' meta-value='1' autocomplete='off'>
                                  <div class='handle'></div>
                               </button>";
                                }else{
                                    echo "<button type='button' name='someone_match' id='someone_match' class='btn btn-sm btn-toggle first-off active' data-toggle='button' aria-pressed='true' meta-value='0' autocomplete='off'>
                                    <div class='handle'></div>
                                 </button>";
                                }

                                ?>
                                
                            <?php }else{  ?>
                                <button type="button" name="someone_match" id="someone_match" class="btn btn-sm btn-toggle first-off active" data-toggle="button" aria-pressed="true" autocomplete="off">
                                    <div class="handle"></div>
                                 </button>
                            <?php } ?>

                           
                        </div>
                     </div>
                     <div class="block-profile-left-right-side d-flex-class">
                        <div class="block-profile-left-side d-flex-class">
                           <div class="setting-para">
                              <p>Notify when someone marks me as favorite </p>
                           </div>
                        </div>
                        <div class="block-profile-right-side setting-check-box">
                            <?php
                            if (in_array('someone_favorite',$section_detail_array_key)){ 

                                if ($userProfileData['user_setting_section_detail']['someone_favorite'] == 1) {
                                    echo "<button type='button' name='someone_marks_favorite' id='someone_marks_favorite' class='btn btn-sm btn-toggle active' data-toggle='button' aria-pressed='true' meta-value='1' autocomplete='off'>
                                  <div class='handle'></div>
                               </button>";
                                }else{
                                    echo "<button type='button' name='someone_marks_favorite' id='someone_marks_favorite' class='btn btn-sm btn-toggle first-off active' data-toggle='button' aria-pressed='true' meta-value='0' autocomplete='off'>
                                    <div class='handle'></div>
                                 </button>";
                                }

                                ?>
                                
                            <?php }else{ ?>
                                <button type="button" name="someone_marks_favorite" id="someone_marks_favorite" class="btn btn-sm btn-toggle first-off active" data-toggle="button" aria-pressed="true" autocomplete="off">
                                    <div class="handle"></div>
                                 </button>
                            <?php } ?>

                           
                        </div>
                     </div>
                     <div class="block-profile-left-right-side d-flex-class">
                        <div class="block-profile-left-side d-flex-class">
                           <div class="setting-para">
                              <p>Notify when someone likes my profile</p>
                           </div>
                        </div>
                        <div class="block-profile-right-side setting-check-box">
                            <?php
                            if (in_array('someone_likes_favorite',$section_detail_array_key)){ 

                                if ($userProfileData['user_setting_section_detail']['someone_likes_favorite'] == 1) {
                                    echo "<button type='button' name='someone_likes_favorite' id='someone_likes_favorite' class='btn btn-sm btn-toggle active' data-toggle='button' aria-pressed='true' meta-value='1' autocomplete='off'>
                                  <div class='handle'></div>
                               </button>";
                                }else{
                                    echo "<button type='button' name='someone_likes_favorite' id='someone_likes_favorite' class='btn btn-sm btn-toggle first-off active' data-toggle='button' meta-value='0' aria-pressed='true' autocomplete='off'>
                                    <div class='handle'></div>
                                 </button>";
                                }

                                ?>
                                
                            <?php }else{ ?>
                                    <button type="button" name="someone_likes_favorite" id="someone_likes_favorite" class="btn btn-sm btn-toggle first-off active" data-toggle="button" aria-pressed="true" autocomplete="off">
                                    <div class="handle"></div>
                                 </button>
                            <?php } ?>
                           
                        </div>
                     </div>
                     <div class="block-profile-left-right-side d-flex-class">
                        <div class="block-profile-left-side d-flex-class">
                           <div class="setting-para">
                              <p>Notify when someone comments on my Forum topic</p>
                           </div>
                        </div>
                        <div class="block-profile-right-side setting-check-box">
                            <?php
                            if (in_array('someone_comments_topic',$section_detail_array_key)){ 

                                if ($userProfileData['user_setting_section_detail']['someone_comments_topic'] == 1) {
                                    echo "<button type='button' name='someone_comments_topic' id='someone_comments_topic' class='btn btn-sm btn-toggle active' data-toggle='button' aria-pressed='true' meta-value='1' autocomplete='off'>
                                  <div class='handle'></div>
                               </button>";
                                }else{
                                    echo "<button type='button' name='someone_comments_topic' id='someone_comments_topic' class='btn btn-sm btn-toggle first-off active' data-toggle='button' meta-value='0' aria-pressed='true' autocomplete='off'>
                                    <div class='handle'></div>
                                 </button>";
                                }

                                ?>
                                
                            <?php }else{ ?>
                                <button type="button" name="someone_comments_topic" id="someone_comments_topic" class="btn btn-sm btn-toggle first-off active" data-toggle="button" aria-pressed="true" autocomplete="off">
                                    <div class="handle"></div>
                                 </button>
                            <?php } ?>
                           
                        </div>
                     </div>
                     <div class="block-profile-left-right-side d-flex-class">
                        <div class="block-profile-left-side d-flex-class">
                           <div class="setting-para">
                              <p>Notify when subscription is about to renew</p>
                           </div>
                        </div>
                        <div class="block-profile-right-side setting-check-box">
                            <?php
                            if (in_array('subscription_renew',$section_detail_array_key)){ 

                                if ($userProfileData['user_setting_section_detail']['subscription_renew'] == 1) {
                                    echo "<button type='button' name='subscription_renew' id='subscription_renew' class='btn btn-sm btn-toggle active' data-toggle='button' aria-pressed='true' meta-value='1' autocomplete='off'>
                                  <div class='handle'></div>
                               </button>";
                                }else{
                                    echo "<button type='button' name='subscription_renew' id='subscription_renew' class='btn btn-sm btn-toggle first-off active' data-toggle='button' meta-value='0' aria-pressed='true' autocomplete='off'>
                                    <div class='handle'></div>
                                 </button>";
                                }

                                ?>
                                
                            <?php }else{ ?>
                                <button type="button" name="subscription_renew" id="subscription_renew" class="btn btn-sm btn-toggle first-off active" data-toggle="button" aria-pressed="true" autocomplete="off">
                                    <div class="handle"></div>
                                 </button>
                            <?php } ?>
                           
                        </div>
                     </div>
                  </div>
               </div>
               <div id="delete-profile" class="tabcontent" style="display:none;">
                  <h4 class="mangage-pass-title">Delete Profile</h4>
                  <div class="form-field-fontent">
                     <div class="block-profile-left-right-side d-flex-class">
                        <div class="block-profile-left-side d-flex-class">
                           <div class="setting-para">
                              <p>Delete your Profile</p>
                           </div>
                        </div>
                        <div class="block-profile-right-side setting-check-box">

                            
                            <a class="profile-delete-button" title="<?= __tr('Delete Topic') ?>" href="#" data-toggle="modal" data-target="#lwDeleteProfileModel"> <i class="fas fa-trash-alt"></i>Delete Profile</a>

                           <!--  <a href="<?= route('user.soft_delete.profile', ['username' => $userData['userName']]) ?>" onclick="return confirm('Are you sure you want to delete your profile?')" class="edit"><i class="fas fa-trash-alt"></i> Delete Profile</a> -->
                        </div>
                     </div>                     
                  </div>
               </div>

        </div>
    </div>


    <div class="modal fade" id="lwDeleteProfileModel" tabindex="-1" role="dialog" aria-labelledby="deleteProfileModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= __tr('Delete Profile?') ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="user lw-ajax-form lw-form" method="POST" action="<?= route('user.soft_delete.profile', ['username' => $userData['userName']]) ?>">
                        <?= __tr('Are you sure you want to delete your profile?') ?>
                        <hr />
                        <button type="submit" class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block-on-mobile"><?= __tr('Delete Profile')  ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    
    <!-- user report Modal-->
    <div class="modal fade" id="lwReportUserDialog" tabindex="-1" role="dialog" aria-labelledby="userReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userReportModalLabel"><?= __tr('Abuse Report to __username__', [
                                                                            '__username__' => $userData['fullName']
                                                                        ]) ?></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form class="lw-ajax-form lw-form" id="lwReportUserForm" method="post" data-callback="userReportCallback" action="<?= route('user.write.report_user', ['sendUserUId' => $userData['userUId']]) ?>">
                    <div class="modal-body">
                        <!-- reason input field -->
                        <div class="form-group">
                            <label for="lwUserReportReason"><?= __tr('Reason') ?></label>
                            <textarea class="form-control" rows="3" id="lwUserReportReason" name="report_reason" required></textarea>
                        </div>
                        <!-- / reason input field -->
                    </div>

                    <!-- modal footer -->
                    <div class="modal-footer mt-3">
                        <button class="btn btn-light btn-sm" id="lwCloseUserReportDialog"><?= __tr('Cancel') ?></button>
                        <button type="submit" class="btn btn-primary btn-sm lw-ajax-form-submit-action btn-user lw-btn-block-mobile"><?= __tr('Report') ?></button>
                    </div>
                </form>
                <!-- modal footer -->
            </div>
        </div>
    </div>
    <!-- /user report Modal-->


    <!-- User block Confirmation text html -->
    <div id="lwBlockUserConfirmationText" style="display: none;">
        <h3><?= __tr('Are You Sure!') ?></h3>
        <strong><?= __tr('You want to block this user.') ?></strong>
    </div>
    <!-- /User block Confirmation text html -->

@push('appScripts')

<script type="text/javascript">
      
    $(".select_plan").on("click",function(event){
        var section_amount = $(this).prev().prev().prev().prev( "#section_amount" ).val();
        var section_plan = $(this).prev().prev().prev( "#section_plan" ).val();
        var section_plan_name = $(this).prev().prev( "#section_plan_name" ).val();
        var section_plan_type = $(this).prev( "#section_plan_type" ).val();
        
        $('#paymentForm > input#req_amount').val(section_amount);
        $('#paymentForm > input#req_plan_id').val(section_plan);
        $('#paymentForm > input#req_plan_name').val(section_plan_name);
        $('#paymentForm > input#req_plan_type').val(section_plan_type);
        
       $( ".AcceptUI" ).trigger( "click" );
        //alert(section_amount);
    });

    /*$(".select_plan_choose").on("click",function(event){


        var section_amount = $(this).prev().prev().prev().prev( "#section_amount_choose" ).val();
        var section_plan = $(this).prev().prev().prev( "#section_plan_choose" ).val();
        var section_plan_name = $(this).prev().prev( "#section_plan_name_choose" ).val();
        var section_plan_type = $(this).prev( "#section_plan_type_choose" ).val();
        //alert(section_plan_type);
        
        $('#paymentForm_choose > input#req_amount_choose').val(section_amount);
        $('#paymentForm_choose > input#req_plan_id_choose').val(section_plan);
        $('#paymentForm_choose > input#req_plan_name_choose').val(section_plan_name);
        $('#paymentForm_choose > input#req_plan_type_choose').val(section_plan_type);

        $( ".AcceptUI" ).trigger( "click" );
    });*/


    


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

        document.getElementById("paymentForm").submit();
    }
</script>

<script>
    $("#someone_match").on("click",function(){
        var someone_match = $(this).attr("meta-value");
        if (someone_match == 1) {someone_match = 0} else {someone_match = 1}
            
        var requestUrl = '<?= route('user.profile_settinig_insert_update.subscription') ?>',
            formData = {
                    'userUId': '<?= $userData['userUId'] ?>',
                    'profile_key': 'setting_someone_match',
                    'someone_match': someone_match,
                };
            __DataRequest.get(requestUrl, formData, {}, function(responseData) {
                __Utils.viewReload();
            });
    });
</script>

<script>
    $("#someone_marks_favorite").on("click",function(){
        var someone_favorite = $(this).attr("meta-value");
        
        if (someone_favorite == 1) {someone_favorite = 0} else {someone_favorite = 1}
            
        var requestUrl = '<?= route('user.profile_settinig_insert_update.subscription') ?>',
            formData = {
                    'userUId': '<?= $userData['userUId'] ?>',
                    'profile_key': 'someone_favorite',
                    'someone_match': someone_favorite,
                };
            __DataRequest.get(requestUrl, formData, {}, function(responseData) {
                __Utils.viewReload();
            });
    });
</script>

<script>
    $("#someone_likes_favorite").on("click",function(){
        var someone_likes_favorite = $(this).attr("meta-value");
        if (someone_likes_favorite == 1) {someone_likes_favorite = 0} else {someone_likes_favorite = 1}
            
        var requestUrl = '<?= route('user.profile_settinig_insert_update.subscription') ?>',
            formData = {
                    'userUId': '<?= $userData['userUId'] ?>',
                    'profile_key': 'someone_likes_favorite',
                    'someone_match': someone_likes_favorite,
                };
            __DataRequest.get(requestUrl, formData, {}, function(responseData) {
                __Utils.viewReload();
            });
    });
</script>

<script>
    $("#someone_comments_topic").on("click",function(){
        var someone_comments_topic = $(this).attr("meta-value");
        if (someone_comments_topic == 1) {someone_comments_topic = 0} else {someone_comments_topic = 1}
            
        var requestUrl = '<?= route('user.profile_settinig_insert_update.subscription') ?>',
            formData = {
                    'userUId': '<?= $userData['userUId'] ?>',
                    'profile_key': 'someone_comments_topic',
                    'someone_match': someone_comments_topic,
                };
            __DataRequest.get(requestUrl, formData, {}, function(responseData) {
                __Utils.viewReload();
            });
    });
</script>

<script>
    $("#subscription_renew").on("click",function(){
        var subscription_renew = $(this).attr("meta-value");
        if (subscription_renew == 1) {subscription_renew = 0} else {subscription_renew = 1}
            
        var requestUrl = '<?= route('user.profile_settinig_insert_update.subscription') ?>',
            formData = {
                    'userUId': '<?= $userData['userUId'] ?>',
                    'profile_key': 'subscription_renew',
                    'someone_match': subscription_renew,
                };
            __DataRequest.get(requestUrl, formData, {}, function(responseData) {
                __Utils.viewReload();
            });
    });
</script>
    
    

<script>
         new WOW().init();
      </script> 
<script>
   

    $( "form#plandetailform" ).submit(function( e ) {
          $('#cardNumber').keyup(function() {
                if ($.payform.validateCardNumber($('#cardNumber').val()) == false) {
                    $('#card-number-field').removeClass('has-success');
                    $('#card-number-field').addClass('has-error');
                } else {
                    $('#card-number-field').removeClass('has-error');
                    $('#card-number-field').addClass('has-success');
                }

                
            });

            var isCardValid = $.payform.validateCardNumber($('#cardNumber').val());
            var isCvvValid = $.payform.validateCardCVC($("#cvv").val());

            if (!isCardValid) {
                alert("Wrong card number");
                e.preventDefault();
            } else if (!isCvvValid) {
                alert("Wrong CVV");
                e.preventDefault();
            }

        });

</script>
<script>
  

    
    /*$('#plandetailform').submit(function(e) {
    
alert("afklfjalfla");
          
            e.preventDefault();

            $('#cardNumber').keyup(function() {
                

                if ($.payform.validateCardNumber($('#cardNumber').val()) == false) {
                    $('#card-number-field').removeClass('has-success');
                    $('#card-number-field').addClass('has-error');
                } else {
                    $('#card-number-field').removeClass('has-error');
                    $('#card-number-field').addClass('has-success');
                }

                
            });


            var isCardValid = $.payform.validateCardNumber($('#cardNumber').val());
            var isCvvValid = $.payform.validateCardCVC($("#cvv").val());

            if (!isCardValid) {
                alert("Wrong card number");
            } else if (!isCvvValid) {
                alert("Wrong CVV");
            } else {
                // Everything is correct. Add your form submission code here.
                alert("Everything is correct");
            }
        });*/

</script>

<script>
    function onChangePasswordCallback(response) {
        if (response.reaction == 1) {
            $("#lwChangePasswordForm")[0].reset();
        }
    }
</script>

<script>
    var activeTab = window.localStorage.getItem('activeTab');
    if(activeTab){
        $('.tabcontent').css('display','none');
        $('#'+activeTab).css("display", "block");
        $('.'+activeTab).addClass('active');
    } else {
        document.getElementById("defaultOpen").click();
    }
 function openCity(evt, cityName) {
     window.localStorage.setItem('activeTab', cityName);
       var i, tabcontent, tablinks;
       tabcontent = document.getElementsByClassName("tabcontent");
       for (i = 0; i < tabcontent.length; i++) {
         tabcontent[i].style.display = "none";
       }
       tablinks = document.getElementsByClassName("tablinks");
       for (i = 0; i < tablinks.length; i++) {
         tablinks[i].className = tablinks[i].className.replace(" active", "");
       }
       document.getElementById(cityName).style.display = "block";
       evt.currentTarget.className += " active";
 }
 
 // Get the element with id="defaultOpen" and click on it
// document.getElementById("defaultOpen").click();
</script>

<script>
    
    // Get user profile data
    function getUserProfileData(response) {
        // If successfully stored data
        if (response.reaction == 1) {
            __DataRequest.get("<?= route('user.get_profile_data', ['username' => getUserAuthInfo('profile.username')]) ?>", {}, function(responseData) {
                var requestData = responseData.data;
                var specificationUpdateData = [];
                _.forEach(requestData.userSpecificationData, function(specification) {
                    _.forEach(specification['items'], function(item) {
                        specificationUpdateData[item.name] = item.value;
                    });
                });
                __DataRequest.updateModels('userData', requestData.userData);
                __DataRequest.updateModels('profileData', requestData.userProfileData);
                __DataRequest.updateModels('specificationData', specificationUpdateData);
            });
        }
    }

    /**************** User Like Dislike Fetch and Callback Block Start ******************/
    //add disabled anchor tag class on click
    $(".lw-like-action-btn, .lw-dislike-action-btn").on('click', function() {
        $('.lw-like-dislike-box').addClass("lw-disable-anchor-tag");
    });
    //on like Callback function
    function onLikeCallback(response) {
        var requestData = response.data;
        //check reaction code is 1 and status created or updated and like status is 1
        if (response.reaction == 1 && requestData.likeStatus == 1 && (requestData.status == "created" || requestData.status == 'updated')) {
            __DataRequest.updateModels({
                'userLikeStatus': '<?= __tr('Liked') ?>', //user liked status
                'userDislikeStatus': '<?= __tr('Dislike') ?>', //user dislike status
            });
            //add class
            $(".lw-animated-like-heart").toggleClass("lw-is-active");
            //check if updated then remove class in dislike heart
            if (requestData.status == 'updated') {
                $(".lw-animated-broken-heart").toggleClass("lw-is-active");
            }
        }
        //check reaction code is 1 and status created or updated and like status is 2
        if (response.reaction == 1 && requestData.likeStatus == 2 && (requestData.status == "created" || requestData.status == 'updated')) {
            __DataRequest.updateModels({
                'userLikeStatus': '<?= __tr('Like') ?>', //user like status
                'userDislikeStatus': '<?= __tr('Disliked') ?>', //user disliked status
            });
            //add class
            $(".lw-animated-broken-heart").toggleClass("lw-is-active");
            //check if updated then remove class in like heart
            if (requestData.status == 'updated') {
                $(".lw-animated-like-heart").toggleClass("lw-is-active");
            }
        }
        //check reaction code is 1 and status deleted and like status is 1
        if (response.reaction == 1 && requestData.likeStatus == 1 && requestData.status == "deleted") {
            __DataRequest.updateModels({
                'userLikeStatus': '<?= __tr('Like') ?>', //user like status
            });
            $(".lw-animated-like-heart").toggleClass("lw-is-active");
        }
        //check reaction code is 1 and status deleted and like status is 2
        if (response.reaction == 1 && requestData.likeStatus == 2 && requestData.status == "deleted") {
            __DataRequest.updateModels({
                'userDislikeStatus': '<?= __tr('Dislike') ?>', //user like status
            });
            $(".lw-animated-broken-heart").toggleClass("lw-is-active");
        }
        //remove disabled anchor tag class
        _.delay(function() {
            $('.lw-like-dislike-box').removeClass("lw-disable-anchor-tag");
        }, 1000);
    }

    //on favourite Callback function
    function onFavouriteCallback(response) {
        return;
        var requestData = response.data;
        //check reaction code is 1 and status created or updated and like status is 1
        if (response.reaction == 1 && requestData.likeStatus == 1 && (requestData.status == "created" || requestData.status == 'updated')) {
            __DataRequest.updateModels({
                'userLikeStatus': '<?= __tr('Liked') ?>', //user liked status
                'userDislikeStatus': '<?= __tr('Dislike') ?>', //user dislike status
            });
            //add class
            $(".lw-animated-like-heart").toggleClass("lw-is-active");
            //check if updated then remove class in dislike heart
            if (requestData.status == 'updated') {
                $(".lw-animated-broken-heart").toggleClass("lw-is-active");
            }
        }
        //check reaction code is 1 and status created or updated and like status is 2
        if (response.reaction == 1 && requestData.likeStatus == 2 && (requestData.status == "created" || requestData.status == 'updated')) {
            __DataRequest.updateModels({
                'userLikeStatus': '<?= __tr('Like') ?>', //user like status
                'userDislikeStatus': '<?= __tr('Disliked') ?>', //user disliked status
            });
            //add class
            $(".lw-animated-broken-heart").toggleClass("lw-is-active");
            //check if updated then remove class in like heart
            if (requestData.status == 'updated') {
                $(".lw-animated-like-heart").toggleClass("lw-is-active");
            }
        }
        //check reaction code is 1 and status deleted and like status is 1
        if (response.reaction == 1 && requestData.likeStatus == 1 && requestData.status == "deleted") {
            __DataRequest.updateModels({
                'userLikeStatus': '<?= __tr('Like') ?>', //user like status
            });
            $(".lw-animated-like-heart").toggleClass("lw-is-active");
        }
        //check reaction code is 1 and status deleted and like status is 2
        if (response.reaction == 1 && requestData.likeStatus == 2 && requestData.status == "deleted") {
            __DataRequest.updateModels({
                'userDislikeStatus': '<?= __tr('Dislike') ?>', //user like status
            });
            $(".lw-animated-broken-heart").toggleClass("lw-is-active");
        }
        //remove disabled anchor tag class
        _.delay(function() {
            $('.lw-like-dislike-box').removeClass("lw-disable-anchor-tag");
        }, 1000);
    }
    /**************** User Like Dislike Fetch and Callback Block End ******************/

    //user report callback
    function userReportCallback(response) {
        //check success reaction is 1
        if (response.reaction == 1) {
            var requestData = response.data;
            //form reset after success
            $("#lwReportUserForm").trigger("reset");
            //close dialog after success
            $('#lwReportUserDialog').modal('hide');
            //reload view after 2 seconds on success reaction
            _.delay(function() {
                __Utils.viewReload();
            }, 1000)
        }
    }

    //close User Report Dialog
    $("#lwCloseUserReportDialog").on('click', function(e) {
        e.preventDefault();
        //form reset after success
        $("#lwReportUserForm").trigger("reset");
        //close dialog after success
        $('#lwReportUserDialog').modal('hide');
    });

</script>

    <?php if(!empty($plan_deatail)){ ?>

        <script>
            /*renew subscription Plan */
            $("#renew_subscription").on('click', function(e) {
                var renew_subscription = $(this).attr('aria-pressed');
                if (renew_subscription == 'true') {renew_subscription = 1} else {renew_subscription = 0}
                
                var requestUrl = '<?= route('user.renew.subscription') ?>',
                    formData = {
                            'userUId': '<?= $userData['userUId'] ?>',
                            'plan_type': '<?= $plan_deatail['plan_type'] ?>',
                            'renew_subscription': renew_subscription,
                        };
                __DataRequest.get(requestUrl, formData, {}, function(responseData) {
                        __Utils.viewReload();
                    });

            });    
        </script>

    <?php } ?>
<script>
    
   

    /*Cancel subscription Plan */
    $("#cancel_subscription").on('click', function(e) {
       // return confirm("Are you sure?");
         if (confirm("After Cancel Subscription you won't be able to access your account.")) {
            var requestUrl = '<?= route('user.cancel.subscription') ?>',
            formData = {
                    'userUId': '<?= $userData['userUId'] ?>'
                };
                setTimeout(function(){ window.location = "{{route('user.login',['username' => getUserAuthInfo('profile.username')])}}"; },2000);

        __DataRequest.get(requestUrl, formData, {}, function(responseData) {
                //Location.reload();
                window.location.href = "{{route('user.login',['username' => getUserAuthInfo('profile.username')])}}";
            });


         }
        

    });

    $('#unblock_user').on('click',function(){
        var unblock_user_id = $(this).attr('value')

        if (confirm("Are you sure you want to unblock this profile?.")) {
            var requestUrl = '<?= route('user.unblock_user') ?>',
            formData = {
                    'userUid': unblock_user_id
                };
            
        __DataRequest.get(requestUrl, formData, {}, function(responseData) {
                console.log("responseData");
            });

         }

    });

    

    //block user confirmation
    $("#lwBlockUserBtn").on('click', function(e) {
        var confirmText = $('#lwBlockUserConfirmationText');
        //show confirmation 
        showConfirmation(confirmText, function() {
            var requestUrl = '<?= route('user.write.block_user') ?>',
                formData = {
                    'block_user_id': '<?= $userData['userUId'] ?>',
                };
            // post ajax request
            __DataRequest.post(requestUrl, formData, function(response) {
                if (response.reaction == 1) {
                    __Utils.viewReload();
                }
            });
        });
    });

    // Click on edit / close button 
    $('#lwEditBasicInformation, #lwCloseBasicInfoEditBlock').click(function(e) {
        e.preventDefault();
        showHideBasicInfoContainer();
    });
    // Show / Hide basic information container
    function showHideBasicInfoContainer() {
        $('#lwUserBasicInformationForm').toggle();
        $('#lwStaticBasicInformation').toggle();
        $('#lwCloseBasicInfoEditBlock').toggle();
        $('#lwEditBasicInformation').toggle();
    }
    // Show hide specification user settings
    function showHideSpecificationUser(formId, event) {
        event.preventDefault();
        $('#lwEdit' + formId).toggle();
        $('#lw' + formId + 'StaticContainer').toggle();
        $('#lwUser' + formId + 'Form').toggle();
        $('#lwClose' + formId + 'Block').toggle();
    }
    // Click on profile and cover container edit / close button 
    $('#lwEditProfileAndCoverPhoto, #lwCloseProfileAndCoverBlock').click(function(e) {
        e.preventDefault();
        showHideProfileAndCoverPhotoContainer();
    });
    // Hide / show profile and cover photo container
    function showHideProfileAndCoverPhotoContainer() {
        $('#lwProfileAndCoverStaticBlock').toggle();
        $('#lwProfileAndCoverEditBlock').toggle();
        $('#lwEditProfileAndCoverPhoto').toggle();
        $('#lwCloseProfileAndCoverBlock').toggle();
    }
    // After successfully upload profile picture
    function afterUploadedProfilePicture(responseData) {
        $('#lwProfilePictureStaticImage, .lw-profile-thumbnail').attr('src', responseData.data.image_url);
    }
    // After successfully upload Cover photo
    function afterUploadedCoverPhoto(responseData) {
        $('#lwCoverPhotoStaticImage').attr('src', responseData.data.image_url);
    }
</script>
@endpush
