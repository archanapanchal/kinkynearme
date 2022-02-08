<!-- include header -->
@section('head-title', __tr('Kinky Near Me - KNM Media LLC -  Dating & Community Hybrid Site'))
@section('description', __tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.'))
@include('includes.header')
<!-- /include header -->

    <section class="banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12"></div>
                <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12">
                    <form class="user lw-ajax-form lw-form banner-form" method="post" action="<?= route('user.sign_up.process') ?>" data-show-processing="true" data-secured="true" data-unsecured-fields="first_name,last_name,g-recaptcha-response">
                        <div class="form-title">
                            <h2 class="heading-title"><?= __tr('FULFILL YOUR FETISH FANTASY') ?></h2>
                            <p class="title-para"><?= __tr('Register with us & find your kinky partner') ?></p>
                        </div>                        
                        <div class="form-field-fontent">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputAddress"><?= __tr('First name (Private)') ?></label>
                                    <input type="text" class="form-control form-control-user" name="first_name" placeholder="<?= __tr('Ex. John') ?>" required minlength="3">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputAddress"><?= __tr('Last name (Private)') ?></label>
                                    <input type="text" class="form-control form-control-user" name="last_name" placeholder="<?= __tr('Ex. Smith') ?>" required minlength="3">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4"><?= __tr('Email address (Private)') ?></label>
                                    <input type="email" class="form-control form-control-user" name="email" placeholder="<?= __tr('Ex. johnsmith@gmail.com') ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputAddress"><?= __tr('Username') ?></label>
                                    <input type="text" class="form-control form-control-user" name="username" placeholder="<?= __tr('Ex. john') ?>" required minlength="5">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputPassword4"><?= __tr('Password') ?></label>
                                    <input type="password" class="form-control form-control-user" name="password" placeholder="<?= __tr('******') ?>" required minlength="6">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputPassword4"><?= __tr('Confirm Password') ?></label>
                                    <input type="password" class="form-control form-control-user" name="repeat_password" placeholder="<?= __tr('******') ?>" required minlength="6">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="g-recaptcha" data-sitekey="6LeVDOUdAAAAACBoEYeF6tON_xIlSx2gBEgyex2U"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-lg-6 col-md-12 col-sm-12">
                                    <div class="form-check">
                                        <input type="hidden" name="accepted_terms">
                                        <input type="checkbox" class="form-check-input" id="acceptTerms" name="accepted_terms" value="1" required>
                                        <label class="form-check-label" for="acceptTerms">
                                        <?= __tr('I agree to the') ?><a href="<?= getStoreSettings('terms_and_conditions_url') ?>"> <?= __tr('Terms & Conditions') ?></a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <a href class="lw-ajax-form-submit-action btn btn-primary btn-user btn-block">
                                <?= __tr('GET STARTED') ?>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section class="kinky-partner">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2 class="heading-title">FIND YOUR KINKY PARTNER</h2>
                    <p class="title-para">Search for Matches who share your preferences and kinks with the any search options available.  Register to find a Match and start messaging today!</p>
                </div>
            </div>
            <div class="row partner-icon">
                <div class="col-lg-3 col-md-6">
                    <div class="icon-box">
                        <img class="bg-img" src="<?= url('dist/images/Create profile.png') ?>">
                        <img class="icon-img" src="<?= url('dist/images/create profile graphic.png') ?>">
                    </div>
                    <div class="partner-desc">
                        <h4 class="partner-title">step 1</h4>
                        <p class="partner-title-para">Create profile</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="icon-box">
                        <img class="bg-img" src="<?= url('dist/images/Like others profile.png') ?>">
                        <img class="icon-img" src="<?= url('dist/images/like graphic.png') ?>">
                    </div>
                    <div class="partner-desc">
                        <h4 class="partner-title">step 2</h4>
                        <p class="partner-title-para">Like other’s profile</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="icon-box">
                        <img class="bg-img" src="<?= url('dist/images/Find match.png') ?>">
                        <img class="icon-img" src="<?= url('dist/images/matched graphic.png') ?>">
                    </div>
                    <div class="partner-desc">
                        <h4 class="partner-title">step 3</h4>
                        <p class="partner-title-para">Find match</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="icon-box">
                        <img class="bg-img" src="<?= url('dist/images/Start dating.png') ?>">
                        <img class="icon-img" src="<?= url('dist/images/Start dating graphic.png') ?>">
                    </div>
                    <div class="partner-desc">
                        <h4 class="partner-title">step 4</h4>
                        <p class="partner-title-para">Connect and watch the sparks fly</p>
                    </div>
                </div>
            </div>
            <div class="row partner-button">
                <div class="col-md-12">
                    <a href="<?= route('user.sign_up') ?>" class="btn btn-primary">GET STARTED</a>
                </div>
            </div>
        </div>
    </section>
    <section class="for-everyone">
        <div class="container">
            <div class="row">
                <div class="col-md-7 text-left">
                    <h2 class="heading-title">FOR EVERYONE WITH A FETISH</h2>
                    <p class="title-para">Online dating isn’t easy and finding partners who are into the same kinky things can be difficult. KinkyNearMe is a community that brings dating, social networking, and education together. KinkyNearMe is here to help you find exactly who, and what, you are looking for. Whether you are wanting casual or committed, exclusive or shared, online or in-person—KinkyNearMe is here to connect you to the right person or people.​​​​</p>
                    <p class="title-para">We have paired traditional search filter matching with an extensive list of common and not-so-common kinks. It’s simple. Put in your kinks, find your match, start chatting and see what happens. KinkyNearMe connects like-minded people—both online and in person. Whatever your kink or level of experience, our Members are about connecting, having fun, experimentation with education, sharing of experiences, respect, privacy, and most importantly, consent.​​​​</p>
                    <p class="title-para">Join our kink and sex-positive community to find the right match near you.</p>
                    <div class="partner-button">
                        <a href="<?= route('user.sign_up') ?>" class="btn btn-primary">JOIN THE COMMUNITY</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="find-free-chat">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center">
                    <div class="find-free-chat-left-side">
                        <h2 class="heading-title">FIND KINKSTERS<br> LIKE YOU</h2>
                        <div class="find-free-chat-img">
                            <img src="<?= url('dist/images/Find-kinksterslike-you.png') ?>">
                        </div>
                        <p class="title-para">Looking for that special sub-one? Or perhaps you’ve got a special kink? There’s someone for everyone out there. With just a few clicks, start meeting kinksters near you.</p>
                        <div class="partner-button">
                            <a href="<?= route('user.sign_up') ?>" class="btn btn-primary">FIND NEARBY KINKSTER</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="find-free-chat-right-side">
                        <h2 class="heading-title">FREE CHAT WITH <br>YOUR MATCHES</h2>
                        <div class="find-free-chat-img">
                            <img src="<?= url('dist/images/Free-chat withyour-matches.png') ?>">
                        </div>
                        <p class="title-para">Connecting with others in the community is quite easy. All you have to do is start liking profiles that you’re interested in. When you find a match, just send them a message.</p>
                        <div class="partner-button">
                            <a href="<?= route('user.sign_up') ?>" class="btn btn-primary">FIND A MATCH NOW</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="plan-for-you">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2 class="heading-title">THE RIGHT PLAN FOR YOU</h2>
                    <p class="title-para">Whether you are a casual member or an active contributor, KinkyNearMe has a plan and payment<br> option to fit what you’re looking for.</p>
                </div>
            </div>
            <ul id="tabs" class="nav nav-tabs" role="tablist">
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
            </ul>
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
                                        <?php 
                                        if ($plan['title'] == "Gold") { ?>
                                           <span class="tag">popular</span>
                                        <?php } 

                                        if ($plan['price'] != 0) { ?>
                                            
                                        <span class="plan_price">$<?= $plan['price'] ?></span>
                                       <?php  }

                                        ?>
                                        <?= $plan['content'] ?>
                                        <a href="{{ url('/user/sign-up') }}" class="btn btn-primary choose-button"><?= __tr('Choose') ?></a>
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
                                        <a href="{{ url('/user/sign-up') }}" class="btn btn-primary choose-button"><?= __tr('Choose') ?></a>
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
    <section class="forums-community">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-left">
                    <div class="find-free-chat-left-side">
                        <div class="forums-community-img">
                            <img src="<?= url('dist/images/forums.png') ?>">
                        </div>
                        <h2 class="heading-title">FORUMS</h2>
                        <p class="title-para">Are you looking to learn more about the Kink Lifestyle? Connect with other Kinksters to share your experiences or ask your questions of the more seasoned people who can share their insight and ex- periences. Want to connect with your community who share your specific interest? KinkyNearMe.com is a welcoming community...</p>
                        <div class="partner-button">
                            <a href="#" class="btn btn-primary">LEARN MORE</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-left">
                    <div class="find-free-chat-right-side">
                        <div class="forums-community-img">
                            <img src="<?= url('dist/images/community.png') ?>">
                        </div>
                        <h2 class="heading-title">COMMUNITY RULES <br>& GUIDELINES</h2>
                        <p class="title-para">Our number one priority is developing a community, Membership and relationships that create a fun and safe place for adult Members. We are an inclusive community, However, we offer both a block and report user option for anyone you encounter who isn’t playing by the rules...</p>
                        <div class="partner-button">
                            <a href="<?php echo url('/community-guidelines'); ?>" class="btn btn-primary">LEARN MORE</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('appScripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

<!-- include footer -->
@include('includes.footer')
<!-- /include footer -->