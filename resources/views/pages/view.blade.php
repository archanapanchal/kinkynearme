<?php 
    $page_seg = Request::segment(1);
?>
<?php if($page_seg == "contact-us") { ?>
@section('head-title', __tr('Contact Us - Kinky Near Me'))
@section('description', __tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.'))
<?php } else if($page_seg == "about-us"){ ?>
@section('head-title', __tr('About Us - Kinky Near Me'))
@section('description', __tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.'))
<?php } else if($page_seg == "privacy-policy"){ ?>
@section('head-title', __tr('Privacy Policy - Kinky Near Me'))
@section('description', __tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.'))
<?php } else if($page_seg == "community-guidelines"){ ?>
@section('head-title', __tr('Community Guidelines - Kinky Near Me'))
@section('description', __tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.'))
<?php } else if($page_seg == "terms-condition"){ ?>
@section('head-title', __tr('Terms & Conditions - Kinky Near Me'))
@section('description', __tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.'))
<?php } else if($page_seg == "cookie-policy"){ ?>
@section('head-title', __tr('Cookie Policy - Kinky Near Me'))
@section('description', __tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.'))
<?php } else if($page_seg == "intellectual-property"){ ?>
@section('head-title', __tr('Intellectual Property - Kinky Near Me'))
@section('description', __tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.'))
<?php } else if($page_seg == "faq"){ ?>
@section('head-title', __tr('Faq - Kinky Near Me'))
@section('description', __tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.'))
<?php } else if($page_seg == "find-matches"){ ?>
@section('head-title', __tr('Fine people near me - Kinky Near Me'))
@section('description', __tr('KinkyNearMe is about connecting people and it gives the best platform to our members to find people, meet & chat.'))
<?php }?>
<section class="about">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumbs">
                    <ol>
                        <li class="breadcrumb-item"><a href="<?= route('landing_page') ?>">Home</a></li>
                        <li class="breadcrumb-item active"><span><?= $pageData['title'] ?></span></li>
                    </ol>
                </div>
            </div>
        </div>
      
        <div class="row">
            <div class="col-md-12">
                <h4 class="heading-title-h4"><?= $pageData['title'] ?></h4>
                <?= $pageData['description'] ?>
            </div>
       </div>
    </div>
</section>
