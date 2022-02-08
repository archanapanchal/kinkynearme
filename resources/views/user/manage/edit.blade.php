@push('header')
<link rel="stylesheet" href="<?= __yesset('dist/css/vendorlibs-leaflet.css') ?>" />
<style>
	#staticMapId {
		height: 300px;
	}
</style>
@endpush
@push('footer')
<script src="<?= __yesset('dist/js/vendorlibs-leaflet.js') ?>"></script>
<link rel="stylesheet" href="<?= __yesset('dist/css/vendorlibs-leaflet.css') ?>" />
<link rel="stylesheet" href="<?= __yesset('dist/css/select2.min.css') ?>" />
@endpush
<?php $latitude = (__ifIsset($userProfileData['latitude'], $userProfileData['latitude'], '40.077375'));
	$longitude = (__ifIsset($userProfileData['longitude'], $userProfileData['longitude'], '-101.785386'));
?>

<!-- 40.07737522900633, -101.78538600586823 -->

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-200"><?= __tr('Update User') ?></h1>
	<!-- back button -->
	<a class="btn btn-light btn-sm" href="<?= route('manage.user.view_list', ['status' => 1]) ?>">
		<i class="fa fa-arrow-left" aria-hidden="true"></i> <?= __tr('Back to Users') ?>
	</a>
	<!-- /back button -->
</div>
<!-- Page Heading -->

<!-- Start of Page Wrapper -->
<div class="row">
	<div class="col-xl-12 mb-4">
		<!-- card -->
		<div class="card mb-4">
			<!-- card body -->
			<div class="card-body">

				<div class="d-flex">
					<a href="javascript:" class="nav-link" data-toggle="modal" data-target="#myLikesModal">
			            <i class="fas fa-check"></i>
			            <span><?= __tr('My Likes') ?></span>
			        </a>

					<a href="javascript:" class="nav-link" data-toggle="modal" data-target="#myWhoLikesMeModal">
			            <i class="fa fa-thumbs-up" aria-hidden="true"></i>
			            <span><?= __tr('Who Likes Me') ?></span>
			        </a>

					<a href="javascript:" class="nav-link" data-toggle="modal" data-target="#myMatchsModal">
			            <i class="fa fa-users"></i>
			            <span><?= __tr('My Matches') ?></span>
			        </a>
			        <a href="javascript:" class="nav-link" data-toggle="modal" data-target="#myFavouritesModal">
			            <i class="fas fa-fw fa-heart"></i>
			            <span><?= __tr('My Favourites') ?></span>
			        </a>
			        
			        <a href="javascript:" class="nav-link" data-toggle="modal" data-target="#myWhoFavouritesMeModal">
			            <i class="fa fa-thumbs-up"></i>
			            <span><?= __tr('Who Favourites Me') ?></span>
			        </a>
			        
			        <?php /*
					<a href="javascript:" class="nav-link" data-toggle="modal" data-target="#myBlockedUsersModal">
			            <i class="fas fa-ban"></i>
			            <span><?= __tr('Blocked Users') ?></span>
			        </a> */ ?>
			    </div>

				<!-- User Profile and Cover photo -->
				<div class="card mb-4 lw-profile-image-card-container">
					<div class="card-body">
						<div class="row" id="lwProfileAndCoverStaticBlock">
							<div class="col-md-6">
								<div class="card mb-3 lw-profile-image-card-container">
									<img class="lw-profile-thumbnail lw-photoswipe-gallery-img lw-lazy-img" id="lwProfilePictureStaticImage" data-src="<?= imageOrNoImageAvailable($userData['profilePicture']) ?>">
									
								</div>
							</div>
							
							<div class="col-md-6">
								<div  style="max-width: 160px;">
									<input type="file" name="filepond" class="filepond lw-file-uploader" id="lwFileUploader" data-remove-media="true" data-instant-upload="true" data-action="<?= route('user.upload_profile_image') ?>?id=<?= $userData['id'] ?>" data-label-idle="<?= __tr("Edit Profile", ['__browseAction__' => "<span class='filepond--label-action'>" . __tr('Browse') . "</span>"]) ?>" data-image-preview-height="170" data-image-crop-aspect-ratio="1:1" data-style-panel-layout="compact circle" data-style-load-indicator-position="center bottom" data-style-progress-indicator-position="right bottom" data-style-button-remove-item-position="left bottom" data-style-button-process-item-position="right bottom" data-callback="afterUploadedProfilePicture">
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /User Profile and Cover photo -->

				<!-- form start -->
				<form class="lw-form" method="post" method="post" action="<?= route('manage.user.write.update', ['userUid' => $userData['uid']]) ?>">
					<div class="form-group row">
						<!-- First Name -->
						<div class="col-sm-6 mb-3 mb-sm-0">
							<label for="lwFirstName"><?= __tr('First Name') ?></label>
							<input type="text" class="form-control form-control-user" name="first_name" id="lwFirstName" value="<?= $userData['first_name'] ?>" required minlength="3">
						</div>
						<!-- /First Name -->
						<!-- Last Name -->
						<div class="col-sm-6">
							<label for="lwLastName"><?= __tr('Last Name') ?></label>
							<input type="text" class="form-control form-control-user" name="last_name" id="lwLastName" value="<?= $userData['last_name'] ?>" minlength="3">
						</div>
						<!-- /Last Name -->
					</div>
					<div class="form-group row">
						<!-- Email -->
						<div class="col-sm-6 mb-3 mb-sm-0">
							<label for="lwEmail"><?= __tr('Email') ?></label>
							<input type="text" class="form-control form-control-user" name="email" id="lwEmail" value="<?= $userData['email'] ?>" required>
						</div>
						<!-- /Email -->
						<!-- Username -->
						<div class="col-sm-6">
							<label for="lwUsername"><?= __tr('Username') ?></label>
							<input type="text" class="form-control form-control-user" name="username" id="lwUsername" value="<?= $userData['username'] ?>" required minlength="5">
						</div>
						<!-- /Username -->
					</div>
					<div class="form-group row">

						<!-- Mobile Number -->
						<input type="hidden" class="form-control form-control-user" name="mobile_number" id="lwMobileNumber" value="<?= $userData['mobile_number'] ?>" maxlength="15">
						<!-- <div class="col-sm-6 mb-3 mb-sm-0">
							<label for="lwMobileNumber"><?= __tr('Mobile Numbers') ?></label>
							<input type="text" class="form-control form-control-user" name="mobile_number" id="lwMobileNumber" value="<?= $userData['mobile_number'] ?>" maxlength="15">
						</div> -->
						<!-- /Mobile Number -->
						<!-- Birthday -->
						
					</div>
					<div class="form-group row">
						<div class="col-sm-6">
							<label for="lwBirthday"><?= __tr('Birthday') ?></label>
							<input type="date" min="{{ \Carbon\Carbon::now()->subYears(configItem('age_restriction.maximum'))->format('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->subYears(configItem('age_restriction.minimum'))->format('Y-m-d') }}" class="form-control form-control-user" name="birthday" placeholder="<?= __tr('YYYY-MM-DD') ?>" value="<?= __ifIsset($userProfileData['dob'], $userProfileData['dob']) ?>" >
						</div>
						<!-- /Birthday -->
						<!-- Gender -->
						<div class="col-sm-6">
							<label for="select_gender"><?= __tr('Gender') ?></label>
							<select name="gender" class="form-control" id="select_gender">
								<option value="" selected disabled><?= __tr('Choose your gender') ?></option>
								@foreach($genders as $genderKey => $gender)
								<option value="<?= $genderKey ?>" <?= (__ifIsset($userProfileData['gender']) and $genderKey == $userProfileData['gender']) ? 'selected' : '' ?>><?= $gender ?></option>
								@endforeach
							</select>
						</div>
						<!-- /Gender -->
					</div>


					<!-- /User Basic Information -->
					<div class="card mb-3">
						<div class="card-header">
							<span class="float-right">
								<a class="lw-icon-btn" href role="button" id="lwEditUserLocation">
									<i class="fa fa-pencil-alt"></i>
								</a>
								<a class="lw-icon-btn" href role="button" id="lwCloseLocationBlock" style="display: none;">
									<i class="fa fa-times"></i>
								</a>
							</span>
							<h5><?= __tr('Location') ?></h5>
						</div>
						<div class="card-body">
							@if(getStoreSettings('allow_google_map') or getStoreSettings('use_static_city_data'))
							<div id="lwUserStaticLocation">
							@if(getStoreSettings('allow_google_map'))
								<div class="gmap_canvas"><iframe height="300" id="gmap_canvas" src="https://maps.google.com/maps/place?q=<?= $latitude ?>,<?= $longitude ?>&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
								</div>
							@else
							<div id="staticMapId"></div>
							@endif
							</div>
							<div id="lwUserEditableLocation" style="display: none;">
							@if(getStoreSettings('use_static_city_data'))
								<div class="form-group">
									<label for="selectLocationCity"><?= __tr('Location') ?></label>
									<input type="hidden" name="city_id" id="cityId">
									<input type="text" id="selectLocationCity" class="form-control" placeholder="<?= __tr('Enter a location') ?>">
								</div>
								@else
								<div class="form-group">
									<label for="address_address"><?= __tr('Location') ?></label>
									<input type="text" id="address-input" name="address_address" class="form-control map-input" placeholder="<?= __tr('Enter a location') ?>">

									<!-- show select location on map error -->
									<div class="alert alert-danger mt-2 alert-dismissible" style="display: none" id="lwShowLocationErrorMessage">
										<button type="button" class="close" data-dismiss="alert">&times;</button>
										<span data-model="locationErrorMessage"></span>
									</div>
									<!-- /show select location on map error -->

									<input type="hidden" name="address_latitude" data-model="profileData.latitude" id="address-latitude" value="<?= $latitude ?>" />
									<input type="hidden" name="address_longitude" data-model="profileData.longitude" id="address-longitude" value="<?= $longitude ?>" />
								</div>
								<div id="address-map-container" style="width:100%;height:400px; ">
									<div style="width: 100%; height: 100%" id="address-map"></div>
								</div>
							</div>
							@endif
							@else
							<!-- info message -->
							<div class="alert alert-info">
								<?= __tr('Something went wrong with Google Api Key, please contact to system administrator.') ?>
							</div>
							<!-- / info message -->
							@endif
						</div>
					</div>



					<!-- User Specifications -->
					@if(!__isEmpty($userSpecificationData))
					@foreach($userSpecificationData as $specificationKey => $specifications)
					<div class="card mb-3">
						<!-- User Specification Header -->
						<div class="card-header">
							<h5><?= $specifications['title'] ?></h5>
						</div>
						<!-- /User Specification Header -->
						<div class="card-body">
							@if($isOwnProfile)
							<!-- User Specification Form -->
								@foreach(collect($specifications['items'])->chunk(2) as $specification)
								<input type="hidden" name="users__id" value="<?= $userData['id'] ?>">
								<div class="form-group row">
									@foreach($specification as $itemKey => $item)
										@if($item['input_type'] != 'dynamic')
										<div class="col-sm-6 mb-3 mb-sm-0">
											@if($item['input_type'] == 'select')
											<?php  if($item['name'] == 'looking_for' || $item['name'] == 'kinks' ){ $selected_options = explode(',',$item['selected_options']); } ?>
											<label for="<?= $item['name'] ?>"><?= $item['label'] ?></label>
											<select name="<?php if($item['multiple'] == true){
												echo $item['name'] . '[]';
											} else if($item['name'] == 'looking_for' || $item['name'] == 'kinks'){
												echo $item['name'] . '[]';
												} else {
													echo $item['name'] ;
												}?>" class="form-control <?=$item['name'] == 'kinks'? 'multiple-kinks': ''?>" <?= $item['multiple'] == true ? 'multiple' : '' ?>
											<?php  if($item['name'] == 'looking_for' || $item['name'] == 'kinks' ){ echo 'multiple'; } ?>
											id="<?=$item['name'] == 'looking_for'? 'multiple-looking_for' : ''?>">
												
												<?php if($item['multiple'] == true){ } else if($item['name'] == 'looking_for' || $item['name'] == 'kinks'){ } else {?>
													<option value="" selected disabled><?= __tr('Choose __label__', [
																						'__label__' => $item['label']
																					]) ?></option>
												<?php } ?>
												@foreach($item['options'] as $optionKey => $option)
												<option value="<?= $optionKey ?>" 
													<?php  if($item['name'] == 'looking_for' || $item['name'] == 'kinks' ){ ?>
														<?= in_array($optionKey, $selected_options) ? 'selected' : '' ?>
													<?php  } ?>
													<?= $item['selected_options'] == $optionKey ? 'selected' : '' ?>>
													<?= $option ?>
												</option>
												@endforeach
											</select>
											@elseif($item['input_type'] == 'textbox')
											<label for="<?= $item['name'] ?>"><?= $item['label'] ?></label>
											<input type="text" id="<?= $item['name'] ?>" name="<?= $item['name'] ?>" class="form-control" value="<?= $item['selected_options'] ?>">
											@endif
										</div>
										@endif
									@endforeach
								</div>
								@endforeach
							<!-- /User Specification Form -->
							@endif
						</div>
					</div>
					@endforeach
					@endif
					<!-- /User Specifications -->




					<!-- status field -->
					<div class="form-group row">
						<div class="col-sm-6 mb-3 mb-sm-0">
							<div class="custom-control custom-checkbox custom-control-inline">
								<input type="checkbox" class="custom-control-input" id="activeCheck" name="status" <?= $userData['status'] == 1 ? 'checked' : '' ?> value="1">
								<label class="custom-control-label" for="activeCheck"><?= __tr('Active')  ?></label>
							</div>
						</div>
					</div>
					<!-- / status field -->

					<!-- Update Button -->
					<button type="button" class="btn btn-primary lw-btn-block-mobile lw-ajax-form-submit-action"><?= __tr('Update') ?></button>
					<!-- /Update Button -->
				</form>
				<!-- /form end -->
			</div>
			<!-- /card body -->
		</div>
		<!-- /card -->

	</div>
</div>
<!-- End of Page Wrapper -->


@include('user.manage.partial-templates.users', ['modalId' => 'myLikesModal', 'modalHeading' => '<i class="fas fa-check"></i><span>&nbsp;My Likes', 'totalUsers' => $totalUserLike, 'usersList' => $peopleILikes])

@include('user.manage.partial-templates.users', ['modalId' => 'myWhoLikesMeModal', 'modalHeading' => '<i class="fa fa-thumbs-up"></i><span>&nbsp;Who Likes Me', 'totalUsers' => $totalUserLikesMe, 'usersList' => $peopleWhoLikesMe])

@include('user.manage.partial-templates.users', ['modalId' => 'myMatchsModal', 'modalHeading' => '<i class="fa fa-users"></i><span>&nbsp;My Matches</h5>', 'totalUsers' => $totalMutualLikes, 'usersList' => $mutualLikes])

@include('user.manage.partial-templates.users', ['modalId' => 'myFavouritesModal', 'modalHeading' => '<i class="fas fa-fw fa-heart"></i><span>&nbsp;My Favourites', 'totalUsers' => $totalUserFavourite, 'usersList' => $peopleIFavourites])

@include('user.manage.partial-templates.users', ['modalId' => 'myWhoFavouritesMeModal', 'modalHeading' => '<i class="fa fa-thumbs-up"></i><span>&nbsp;Who Favourites Me', 'totalUsers' => $totalUserFavouritesMe, 'usersList' => $peopleWhoFavouritesMe])

@include('user.manage.partial-templates.users', ['modalId' => 'myBlockedUsersModal', 'modalHeading' => '<i class="fas fa-ban"></i><span>&nbsp;Blocked Users</span>', 'totalUsers' => $totalBlockedUsers, 'usersList' => $blockedUsers])


@push('appScripts')
<script>
	$(function() {
        $('#lwUsername').on('keypress', function(e) {
            if (e.which == 32){
               return false;
            }
        });
    });
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

	//block user confirmation
	$("#lwBlockUserBtn").on('click', function(e) {
		var confirmText = $('#lwBlockUserConfirmationText');
		//show confirmation 
		showConfirmation(confirmText, function() {
			var requestUrl = '<?= route('user.write.block_user') ?>',
				formData = {
					'block_user_id': '<?= $userData['uid'] ?>',
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
<script>
	$(document).ready(function() {
     $('.filepond--drop-label , .filepond--list-scroller, .filepond--panel, .filepond--assistant, .filepond--data, .filepond--drip').addClass('hideanimation ');
      $('.multiple-kinks').select2();
      $('#multiple-looking_for').select2();

      ///////////
   });
	// Click on edit / close button 
	$('#lwEditUserLocation, #lwCloseLocationBlock').click(function(e) {
		e.preventDefault();
		showHideLocationContainer();
	});
	// Show hide location container
	function showHideLocationContainer() {
		$('#lwUserStaticLocation').toggle();
		$('#lwUserEditableLocation').toggle();
		$('#lwEditUserLocation').toggle();
		$('#lwCloseLocationBlock').toggle();
	}

	function initialize() {
		@if(getStoreSettings('allow_google_map'))
		$('form').on('keyup keypress', function(e) {
			var keyCode = e.keyCode || e.which;
			if (keyCode === 13) {
				e.preventDefault();
				return false;
			}
		});
		const locationInputs = document.getElementsByClassName("map-input");

		const autocompletes = [];
		const geocoder = new google.maps.Geocoder;
		for (let i = 0; i < locationInputs.length; i++) {

			const input = locationInputs[i];
			const fieldKey = input.id.replace("-input", "");
			const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';

			const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || -33.8688;
			const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 151.2195;

			const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
				center: {
					lat: latitude,
					lng: longitude
				},
				zoom: 4
			});
			const marker = new google.maps.Marker({
				map: map,
				position: {
					lat: latitude,
					lng: longitude
				},
			});

			marker.setVisible(isEdit);

			const autocomplete = new google.maps.places.Autocomplete(input);
			autocomplete.key = fieldKey;
			autocompletes.push({
				input: input,
				map: map,
				marker: marker,
				autocomplete: autocomplete
			});
		}

		for (let i = 0; i < autocompletes.length; i++) {
			const input = autocompletes[i].input;
			const autocomplete = autocompletes[i].autocomplete;
			const map = autocompletes[i].map;
			const marker = autocompletes[i].marker;

			google.maps.event.addListener(autocomplete, 'place_changed', function() {
				marker.setVisible(false);
				const place = autocomplete.getPlace();

				geocoder.geocode({
					'placeId': place.place_id
				}, function(results, status) {
					if (status === google.maps.GeocoderStatus.OK) {
						const lat = results[0].geometry.location.lat();
						const lng = results[0].geometry.location.lng();
						setLocationCoordinates(autocomplete.key, lat, lng, place);
					}
				});

				if (!place.geometry) {
					window.alert("No details available for input: '" + place.name + "'");
					input.value = "";
					return;
				}

				if (place.geometry.viewport) {
					map.fitBounds(place.geometry.viewport);
				} else {
					map.setCenter(place.geometry.location);
					map.setZoom(4);
				}
				marker.setPosition(place.geometry.location);
				marker.setVisible(true);

			});
		}
		@endif
	}

	function setLocationCoordinates(key, lat, lng, placeData) {
		__DataRequest.post("<?= route('user.write.location_data') ?>", {
			'latitude': lat,
			'longitude': lng,
			'placeData': placeData.address_components
		}, function(responseData) {
			var requestData = responseData.data;
			//check reaction code is not 1
			if (responseData.reaction != 1) {
				$("#lwShowLocationErrorMessage").show();
				__DataRequest.updateModels({
					'locationErrorMessage': requestData.message
				});
				return false;
			}
			//check reaction code is 1
			if (responseData.reaction == 1) {
				$("#lwShowLocationErrorMessage").hide();
				showHideLocationContainer();
				__DataRequest.updateModels('profileData', {
					city: requestData.city,
					country_name: requestData.country_name,
					latitude: lat,
					longitude: lng
				});
			}

			var mapSrc = "https://maps.google.com/maps/place?q=" + lat + "," + lng + "&output=embed";
			$('#gmap_canvas').attr('src', mapSrc)
		});
	};
	@if(!getStoreSettings('allow_google_map') and getStoreSettings('use_static_city_data'))
	var $selectLocationCity = $('#selectLocationCity').selectize({
		// plugins: ['restore_on_backspace'],
		valueField: 'id',
		labelField: 'cities_full_name',
		searchField: [
			'cities_full_name'
		],
		// options: [],
		create: false,
		// loadThrottle: 2000,
		maxItems: 1,
		render: {
			option: function(item, escape) {
				return '<div><span class="title"><span class="name">' + escape(item.cities_full_name) + '</span></span></div>';
			}
		},
		load: function(query, callback) {
			if (!query.length || (query.length < 2)) {
				return callback([]);
			} else {
				__DataRequest.post("<?= route('user.read.search_static_cities') ?>", {
					'search_query': query
				}, function(responseData) {
					callback(responseData.data.search_result);
				});
			}
		},
		onChange: function(value) {
			if (!value.length) {
				return;
			};

			$('#cityId').val(value);
			/*__DataRequest.post("<?= route('user.write.store_city') ?>", {
				'selected_city_id': value
			}, function(responseData) {
				if (responseData.reaction == 1) {
					__Utils.viewReload();
				}
			});*/
		}
	});

	var selectLocationCityControl = $selectLocationCity[0].selectize;
	selectLocationCityControl.clear(true);
	selectLocationCityControl.clearOptions(true);

	// leaflet map
	var leafletMap = L.map('staticMapId').setView(["<?= $latitude ?>", "<?= $longitude ?>"], 13);
	L.tileLayer(
		'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, '
		}
	).addTo(leafletMap);
	// add marker
	L.marker(["<?= $latitude ?>", "<?= $longitude ?>"]).addTo(leafletMap);
	@endif
</script>
<script src="<?= __yesset('dist/js/select2.min.js') ?>"></script>

@endpush