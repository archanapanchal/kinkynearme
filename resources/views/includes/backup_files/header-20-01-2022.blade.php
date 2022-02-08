<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="<?= config('CURRENT_LOCALE_DIRECTION') ?>" @if(isAdmin()) class="lw-light-theme" @endif>

<?php //echo "<pre>"; print_r($userSpecificationData);exit(); ?>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1.0, user-scalable=no">
	<meta name="description" content="@yield('description')">
	<meta name="author" content="">
	<!-- <title>@yield('head-title') : <?php //echo  getStoreSettings('name') ?></title> -->
	<title>@yield('head-title')</title>
	<!-- Custom fonts for this template-->
	<link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet">
	<link rel="shortcut icon" href="<?= getStoreSettings('favicon_image_url') ?>" type="image/x-icon">
	<link rel="icon" href="<?= getStoreSettings('favicon_image_url') ?>" type="image/x-icon">

	<!-- Primary Meta Tags -->
	<meta name="title" content="@yield('page-title')">
	<meta name="description" content="@yield('description')">
	<meta name="keywordDescription" property="og:keywordDescription" content="@yield('keywordDescription')">
	<meta name="keywordName" property="og:keywordName" content="@yield('keywordName')">
	<meta name="keyword" content="@yield('keyword')">
	<!-- Google Meta -->
	<meta itemprop="name" content="@yield('page-title')">
	<meta itemprop="description" content="@yield('description')">
	<meta itemprop="image" content="@yield('page-image')">
	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="website">
	<meta property="og:url" content="@yield('page-url')">
	<meta property="og:title" content="@yield('page-title')">
	<meta property="og:description" content="@yield('description')">
	<meta property="og:image" content="@yield('page-image')">
	<!-- Twitter -->
	<meta property="twitter:card" content="@yield('twitter-card-image')">
	<meta property="twitter:url" content="@yield('page-url')">
	<meta property="twitter:title" content="@yield('page-title')">
	<meta property="twitter:description" content="@yield('description')">
	<meta property="twitter:image" content="@yield('page-image')">

	<!-- Custom styles for this template-->
	@if(isAdmin())
	<?= __yesset([
		'dist/css/public-assets-app*.css',
		'dist/fa/css/all.min.css',
		"dist/css/vendorlibs-datatable.css",
		"dist/css/vendorlibs-photoswipe.css",
		"dist/css/vendorlibs-smartwizard.css",
		'dist/css/custom*.css',
		'dist/css/messenger*.css',
		'dist/css/login-register*.css'
	], true) ?>
	@else
	<?= __yesset([
		'dist/css/bootstrap.css',
	    'dist/fa/css/all.min.css',
	    "dist/css/vendorlibs-photoswipe.css",
		"dist/css/vendorlibs-smartwizard.css",
		'dist/css/noty.css',
	    'dist/css/styles.css',
	    'dist/css/responsive.css',
	    'dist/css/messenger*.css',
	    'dist/css/animate.css',
	    'dist/css/select2.min.css'
	], true) ?>
	@endif
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@if(getApplicableTheme() == 'light')
<?= __yesset([
		'dist/css/light-theme*.css',
	], true) ?>
@endif
	@stack('header')
</head>

@if(!isAdmin() && (!isset($isadmin)))
@include('includes.recaptchalib')

<body id="page-top">

	<?php 

	if (!empty(getUserID())) { ?>
		<div id="cookiePopup">
		      <h4>Cookie Consent</h4>
		      <p >Our website uses cookies to provide your browsing experience and relavent informations.Before continuing to use our website, you agree & accept of our  <a href="<?php echo url('/cookie-policy'); ?>">Cookie Policy & Privacy</a></p>
		     <button id="acceptCookie" >Accept</button>
		     <button id="cancelCookie">Cancel</button>  
		    </div>
	<?php } ?>
	

	<script type="text/javascript">
	      // set cookie according to you
	      var cookieName= "KinkiSiteCookieStatus";
	      var cookieValue="Kinki Site";
	      var cookieExpireDays= 30;

	      // when users click accept button
	      let acceptCookie= document.getElementById("acceptCookie");
	      let cancelCookie= document.getElementById("cancelCookie");
	      acceptCookie.onclick= function(){
	          createCookie(cookieName, cookieValue, cookieExpireDays);

	          var userUid = '<?= getUserID(); ?>';

	          var requestUrl = '<?= route('user.create_cookie_flag') ?>',
	            formData = {
	                    'userUid': userUid,
	                    'cookie_flag': 1,
	                };
	            
	        __DataRequest.get(requestUrl, formData, {}, function(responseData) {
	                

	                var userUid = '<?= getUserID(); ?>';

		          var requestUrl = '<?= route('user.create_cookie_flag') ?>',
		            formData = {
		                    'userUid': userUid,
		                    'cookie_flag': 0,
		                };
		            
		        __DataRequest.get(requestUrl, formData, {}, function(responseData) {
		                console.log("responseData");
		            });

	            });

	      }
	      cancelCookie.onclick= function(){
	          declineCookie();
	      }

	      // function to set cookie in web browser
	       let createCookie= function(cookieName, cookieValue, cookieExpireDays){
	        let currentDate = new Date();
	        currentDate.setTime(currentDate.getTime() + (cookieExpireDays*24*60*60*1000));
	        let expires = "expires=" + currentDate.toGMTString();
	        document.cookie = cookieName + "=" + cookieValue + ";" + expires + ";path=/";
	        if(document.cookie){
	          document.getElementById("cookiePopup").style.display = "none";
	        }else{
	          alert("Unable to set cookie. Please allow all cookies site from cookie setting of your browser");
	        }

	       }

	       let declineCookie= function(){
	       		sessionStorage.setItem("declineCookie","yes");
	       		document.getElementById("cookiePopup").style.display = "none";
	       }

	      // get cookie from the web browser
	      let getCookie= function(cookieName){
	        let name = cookieName + "=";
	        let decodedCookie = decodeURIComponent(document.cookie);
	        let ca = decodedCookie.split(';');
	        for(let i = 0; i < ca.length; i++) {
	          let c = ca[i];
	          while (c.charAt(0) == ' ') {
	            c = c.substring(1);
	          }
	          if (c.indexOf(name) == 0) {
	            return c.substring(name.length, c.length);
	          }
	        }
	        return "";
	      }
	      // check cookie is set or not
	      let checkCookie= function(){

	      		let session_var = sessionStorage.getItem("declineCookie");
	      		/*if(session_var == 'yes'){

	      			document.getElementById("cookiePopup").style.display = "none";
	      		}*/

	          let check=getCookie(cookieName);
	          if(check==""){
	          	
	          	if(session_var == 'yes'){
	          	
	      			document.getElementById("cookiePopup").style.display = "none";
	      		}else{
	      			
	              document.getElementById("cookiePopup").style.display = "block";
	      		}

	          }else{
	              document.getElementById("cookiePopup").style.display = "none";
	          }
	      }
	      checkCookie();
	</script>

	<div class="wrapper">
      <div class="advance-search advance-search-v2" style="display: none;">
         <a type="button" class="close-search-popup">X</a>
         <div class="padd-left-right-30 border-bottom">
            <div class="row align-center">
               <div class="col-md-8">
                  <div class="advance-search-left">
                     <h5>Advanced Search</h5>
                     <p>Search your partner by providing your preferences</p>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="advance-search-right">
                     <div class="action-button">
                        <button type="button" class="btn btn-outline-secondary bg-color">SEARCH</button>
                        <button type="button" class="btn btn-outline-secondary">clear</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="padd-left-right-30 save-search border-bottom">
            <div class="row align-center">
               <div class="col-md-8">
                  <div class="advance-search-left d-flex-class">
                     <p>Saved Searches</p>
                     <div class="city"> <a href="#">in my city<i class="far fa-trash-alt"></i></a> <a href="#">In my state<i class="far fa-trash-alt"></i></a> </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="advance-search-right">
                     <div class="action-button">
                        <button type="button" class="btn btn-outline-secondary">SAVE SEARCH CRITERIA</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="padd-left-right-30 search-location">
            <div class="location">
               <button type="button" class="btn btn-outline-secondary">Search by Location</button>
               <p>Search by ZipCode</p>
            </div>
            <div class="cty-st-ctry-age-rang row">
               <div class="cty-sts-ctry col-md-6">
                  <p>City / State / Country</p>
                  <div class="cty-sts-ctry-btn">
                     <div class="city"> <a href="#">Los Angeles<i class="fa fa-times"></i></a> <a href="#">San Fransisco<i class="fa fa-times"></i></a> </div>
                  </div>
               </div>
               <div class="age-range col-md-6">
                  <p>Age Range</p>
                  <div slider id="slider-distance">
                     <div>
                        <div inverse-left style="width: 70%;"></div>
                        <div inverse-right style="width: 70%;"></div>
                        <div range style="left: 30%; right: 40%;"></div> <span thumb style="left: 30%;"></span> <span thumb style="left: 60%;"></span>
                        <div sign style="left: 30%;"> <span id="value">23</span> </div>
                        <div sign style="left: 60%;"> <span id="value">40</span> </div>
                     </div>
                     <input type="range" tabindex="0" value="30" max="100" min="0" step="1" oninput="
                                                   this.value=Math.min(this.value,this.parentNode.childNodes[5].value-1);
                                                   var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
                                                   var children = this.parentNode.childNodes[1].childNodes;
                                                   children[1].style.width=value+'%';
                                                   children[5].style.left=value+'%';
                                                   children[7].style.left=value+'%';children[11].style.left=value+'%';
                                                   children[11].childNodes[1].innerHTML=this.value;" />
                     <input type="range" tabindex="0" value="60" max="100" min="0" step="1" oninput="
                                                   this.value=Math.max(this.value,this.parentNode.childNodes[3].value-(-1));
                                                   var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
                                                   var children = this.parentNode.childNodes[1].childNodes;
                                                   children[3].style.width=(100-value)+'%';
                                                   children[5].style.right=(100-value)+'%';
                                                   children[9].style.left=value+'%';children[13].style.left=value+'%';
                                                   children[13].childNodes[1].innerHTML=this.value;" /> </div>
               </div>
            </div>
            <div class="phy-life-style row">
               <div class="physical-app col-md-6">
                  <p>Physical Appearance</p>
                  @if(!empty($userSpecificationData))
                  <div class="select-bar row">
                     <div class="form-field col-md-6">
                        <label><img src="<?php echo url('/dist/images/app-2.png'); ?>" />Hair Colour</label>
                        <div class="select-input">
                           @foreach(collect($userSpecificationData['step-2']['items'])->chunk(2) as $specification) 
		                        @foreach($specification as $itemKey => $item)
                               	@if($item['name'] == 'hair_color')
                                    <select name="hair_color" id="select_gender">
                                        @foreach($item['options'] as $genderKey => $options)
                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                        @endforeach
                                    </select>
                                	@endif   
	                           @endforeach
                           @endforeach
                        </div>
                     </div>
                     <div class="form-field col-md-6">
                        <label><img src="<?php echo url('/dist/images/app-5.png'); ?>" />Eye Colour</label>
                        <div class="select-input">
                           @foreach(collect($userSpecificationData['step-2']['items'])->chunk(2) as $specification) 
		                        @foreach($specification as $itemKey => $item)
                               	@if($item['name'] == 'eye_color')
                                    <select name="eye_color" id="select_gender">
                                        @foreach($item['options'] as $genderKey => $options)
                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                        @endforeach
                                    </select>
                                	@endif   
	                           @endforeach
                           @endforeach
                        </div>
                     </div>
                  </div>
                  @endif
               </div>
               <div class="life-style-app col-md-6">
                  <p>Lifestyle</p>
                  @if(!empty($userSpecificationData))
                  <div class="select-bar row">
                     <div class="form-field col-md-6">
                        <label><img src="<?php echo url('/dist/images/app-6.png'); ?>" />Do they Smoke ?</label>
                        <div class="select-input">
                           @foreach(collect($userSpecificationData['step-3']['items'])->chunk(2) as $specification) 
		                        @foreach($specification as $itemKey => $item)
                               	@if($item['name'] == 'smoke')
                                    <select name="smoke" id="select_gender">
                                        @foreach($item['options'] as $genderKey => $options)
                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                        @endforeach
                                    </select>
                                	@endif   
	                           @endforeach
                           @endforeach
                        </div>
                     </div>
                     <div class="form-field col-md-6">
                        <label><img src="<?php echo url('/dist/images/app-7.png'); ?>" />Do they Drink ?</label>
                        <div class="select-input">
                           @foreach(collect($userSpecificationData['step-3']['items'])->chunk(2) as $specification) 
		                        @foreach($specification as $itemKey => $item)
                                	@if($item['name'] == 'drink')
                                    <select name="drink" id="select_gender">
                                        @foreach($item['options'] as $genderKey => $options)
                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                        @endforeach
                                    </select>
                                	@endif
	                           @endforeach
                           @endforeach
                        </div>
                     </div>
                     <div class="form-field col-md-6">
                        <label>Willing to Relocate ?</label>
                        <div class="select-input">
                           @foreach(collect($userSpecificationData['step-4']['items'])->chunk(2) as $specification) 
		                        @foreach($specification as $itemKey => $item)
                                	@if($item['name'] == 'relocate')
                                    <select name="relocate" id="select_gender">
                                        @foreach($item['options'] as $genderKey => $options)
                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                        @endforeach
                                    </select>
                                	@endif   
	                           @endforeach
                           @endforeach
                        </div>
                     </div>
                     <div class="form-field col-md-6">
                        <label>Maritial Status</label>
                        <div class="select-input">
                           @foreach(collect($userSpecificationData['step-4']['items'])->chunk(2) as $specification) 
		                        @foreach($specification as $itemKey => $item)
                                	@if($item['name'] == 'married')
                                    <select name="married" id="select_gender">
                                        @foreach($item['options'] as $genderKey => $options)
                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                        @endforeach
                                    </select>
                                	@endif  
	                           @endforeach
                           @endforeach
                        </div>
                     </div>
                     <div class="form-field col-md-6">
                        <label>Do they have Children?</label>
                        <div class="select-input">
                           @foreach(collect($userSpecificationData['step-4']['items'])->chunk(2) as $specification) 
		                        @foreach($specification as $itemKey => $item)
                                	@if($item['name'] == 'children')
                                    <select name="children" id="select_gender">
                                        @foreach($item['options'] as $genderKey => $options)
                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                        @endforeach
                                    </select>
                                	@endif
	                           @endforeach
                           @endforeach
                        </div>
                     </div>
                     <div class="form-field col-md-6">
                        <label>No. of Children</label>
                        <div class="select-input">
                           @foreach(collect($userSpecificationData['step-4']['items'])->chunk(2) as $specification) 
		                        @foreach($specification as $itemKey => $item)
                                	@if($item['name'] == 'no_of_children')
                                    <select name="no_of_children" id="select_gender">
                                        @foreach($item['options'] as $genderKey => $options)
                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                        @endforeach
                                    </select>
                                	@endif   
	                           @endforeach
                           @endforeach
                        </div>
                     </div>
                  </div>
                  @endif
               </div>
            </div>
            <div class="show-mamber">
               <p>Show only members</p>
               <div class="check-member-box">
                  <div class="form-group check-box">
                     <input type="checkbox" id="html" />
                     <label for="html">Who are currently Online</label>
                  </div>
                  <div class="form-group check-box">
                     <input type="checkbox" id="css" />
                     <label for="css">Who have Photos</label>
                  </div>
                  <div class="form-group check-box">
                     <input type="checkbox" id="javascript" />
                     <label for="javascript">Who has Videos</label>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php
       if (!empty($search_data)) { ?>
         <div class="advance-search" style="display:block;">
            <div class="row">
               <div class="col-md-4 border-right">
                  <div class="advance-search-left">
                     <h5>People</h5>
                     <?php
                     if (isset($search_data['search_data']['reaction_code'])) { ?>
                        <div class="block-profile-left-side d-flex-class">
                              <div class="img-left-side">
                                 <?php echo $search_data['search_data']['message']; ?>
                              </div>
                           </div>
                           <span class='see-more-button'>Total 0</span>
                     <?php } else {
                        foreach ($search_data as $key => $search) { 
                           ?>
                           <div class="block-profile-left-side d-flex-class">
                                 <div class="img-left-side"><img src="<?php echo $search['profilePicture']; ?>"></div>
                                 <div class="content-left-side">
                                    <h5><?php echo $search['username']; ?></h5>
                                 </div>
                              </div>
                     <?php } 
                     echo "<span class='see-more-button'>Total".count($search_data)."</span>";
                    } ?>
                     
                     <button type="button" class="btn btn-outline-secondary"> view-all</button>
                  </div>
               </div>
               <div class="col-md-8">
                  <div class="advance-search-right">
                     <h5>Forum Topics</h5>
                     <div class="advance-search-right-inner">
                        <div class="advance-search-content">
                           <p>Not built yet!!</p>
                        </div>
                     </div>
                     <button type="button" class="btn btn-outline-secondary"> view-all</button>
                  </div>
               </div>
            </div>
         </div>
      <?php } ?>
		<header class="header @auth header-2 @endif">
		    <nav class="navbar navbar-expand-lg">
		        <div class="container">
		            <a href="<?= route('landing_page') ?>" class="navbar-brand">
		            <img class="lw-logo-img" src="<?= getStoreSettings('logo_image_url') ?>" alt="<?= getStoreSettings('name') ?>">
		            </a>
		            @auth
                  <button type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler navbar-toggler-right"><i class="fa fa-bars"></i></button>
                   <div id="navbarSupportedContent" class="collapse navbar-collapse">
		            	<?php 
		            		if (!empty($search_data)) {
		            			if (isset($search_data['search_data']['reaction_code'])) {
		            				$search_string = $search_data['serch_string'];
		            			}else{
		            				$search_string = $search_data[0]['serch_string'];
		            			}
		            		}else{
		            			$search_string = "";
		            		}
		            	?>
		            	<div class="search-box middle-bar-header">
			            		<form action="" method="get">
					                <div class="input-group">
					                    <div class="form-outline">
					                        <input type="search" name="search" id="form1" class="form-control" placeholder="Search by name, interest/kinks" value="<?= $search_string ?>" />
					                    </div>
					                    <input type="submit" name="" value="search" class="btn btn-primary"></input>
					                </div>
					            </form>
				                <div class="advance-link">
				                <p class="search-content">Ex. Blindfold, Elvira, lonleybert, New York</p>
				                <a herf="#" class="search-link">Advanced search</a></div>
			                
			            </div>
			            <div class="right-side-bar-header">
			                <div class="notification-bar"><img src="<?= url('dist/images/notification-icon.svg') ?>"><span class="count-num">0</span></div>
			                <div class="admin-bar">
			                	<img class="lw-profile-thumbnail lw-lazy-img" data-src="<?= getUserAuthInfo('profile.profile_picture_url') ?>">
			                    <div class="form-field">
		                        	<div class="select-input">
		                            	<div class="dropdown">
		                            		<button type="button" class="dropdown-toggle" data-toggle="dropdown"><?= getUserAuthInfo('profile.full_name') ?></button>
	                            			<div class="dropdown-menu">
		                                		<a class="dropdown-item" href="<?= route('landing_page') ?>"><?= __tr('Account') ?></a>
		                                		<a class="dropdown-item" href="<?= route('user.logout') ?>"><?= __tr('Logout') ?></a>
		                               		</div>
		                             	</div>
		                           	</div>
		                        </div>
			                </div>
			            </div>
			        </div>
		            @else
		            	
		            	<button type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler navbar-toggler-right"><i class="fa fa-bars"></i></button>
			            <div id="navbarSupportedContent" class="collapse navbar-collapse">
			                <ul class="navbar-nav ml-auto" id="mainNav">
			                    <li class="nav-item active"><a href="<?php echo url('/'); ?>" class="nav-link">Home<span class="sr-only">(current)</span></a></li>
			                    <li class="nav-item"><a href="<?php echo url('/about-us'); ?>" class="nav-link">About us</a></li>
			                    <li class="nav-item"><a href="<?php echo url('/contact-us'); ?>" class="nav-link">Contact Us</a></li>
			                    <li class="nav-item"><a href="<?php echo url('/privacy-policy'); ?>" class="nav-link">Privacy</a></li>
			                    <li class="nav-item"><a href="<?= route('user.sign_up') ?>" class="nav-link link-border register">Register</a></li>
			                    <li class="nav-item"><a href="<?= route('user.login') ?>" class="nav-link link-border">Login</a></li>
			                </ul>
			            </div>
		            @endif
		            
		        </div>
		    </nav>
		</header>

		            

@endif