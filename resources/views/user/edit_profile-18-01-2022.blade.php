@section('page-title', strip_tags($userData['userName']))
@section('head-title', strip_tags($userData['userName']))
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

<link rel="stylesheet" href="<?= __yesset('dist/css/vendorlibs-leaflet.css') ?>" />
<link rel="stylesheet" href="<?= __yesset('dist/css/select2.min.css') ?>" />
<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> -->
  <section class="edit-profile">

        <div class="container">
           <div class="row">
              <div class="col-md-12">
                 <div class="breadcrumbs">
                    <ol>
                       <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                       <li class="breadcrumb-item active"><a href="index.html">my-profile</a><span></span></li>
                       <li class="breadcrumb-item active"><span>edit profile</span></li>
                    </ol>
                 </div>
              </div>
              
           </div>
           <form action="<?= route('user.edit.profile_update', ['userUid' => getUserID()]) ?>" method="post">
           <input type="hidden" name="_token" value="{{ csrf_token() }}" />
           <div class="row">
              <div class="col-md-12">
                 <h4 class="heading-title-h4">Edit Profile</h4>
              </div>
              <div class="edit-pic">
                 <img src="<?= $userData['profilePicture'] ?>">
                 <div class="edit-pic-action">
                    <!-- <a href="#" class="btn btn-outline-secondary bg-color">edit</a> -->
                     <a href="#" class="btn btn-outline-secondary bg-color-red">upload<input type="file" name="filepond" class="filepond lw-file-uploader" id="lwFileUploader" data-remove-media="true" data-instant-upload="true" data-action="<?= route('user.upload_profile_image') ?>?id=<?= $userData['userId'] ?>" data-label-idle="<?= __tr("Drag & Drop your picture or __browseAction__", ['__browseAction__' => "<span class='filepond--label-action'>" . __tr('Browse') . "</span>"]) ?>" data-image-preview-height="170" data-image-crop-aspect-ratio="1:1" data-style-panel-layout="compact circle" data-style-load-indicator-position="center bottom" data-style-progress-indicator-position="right bottom" data-style-button-remove-item-position="left bottom" data-style-button-process-item-position="right bottom" data-callback="afterUploadedProfilePicture"></a>
                    <a class="btn btn-outline-secondary bg-color" href="<?= route('user.delete.profile', ['username' => $userData['userName']]) ?>">delete</a>
                 </div>
              </div>

              <div class="edit-content">
                  <div class="form-field">
                    <h4 class="font-25">About me</h4>
                    <div class="border">
                        <div class="field-input">
                           <textarea name="about_me" cols="58"> 
                              <?php echo $userProfileData['aboutMe'];?>
                           </textarea>
                        </div>
                          
                    </div>
                 </div>
              </div>
           </div>
           <div class="row personal-det-life-sty phy-life-style">
              <div class="edit-pic">
                 <h4 class="font-25">Personal Details</h4>
                 <div class="select-bar">
                    <div class="form-field">
                       <label>Name</label>
                       <div class="field-input">
                          <input type="text" name="fname" placeholder="Sheridan Kuhn" value="<?= $userData['first_name'] ?>">
                       </div>
                    </div>
                    <div class="form-field">
                       <label>Email address</label>
                       <div class="field-input">
                          <input type="text" name="email" placeholder="johnsmith@gmail.com" value="<?= $userData['email'] ?>">
                       </div>
                    </div>

                    <div class="form-field">
                       <label>Date of Birth</label>
                       <div class="date-input">
                          <input type="date" name="dob" placeholder="Jan 3 1985" value="<?php echo $userProfileData['birthday']; ?>">
                       </div>
                    </div>
                    
                   

                    <div class="form-field">
                        <label>I am a</label>
                        <div class="select-input">
                    @foreach(collect($userSpecificationData['step-1']['items'])->chunk(2) as $specification)                        
                        @foreach($specification as $itemKey => $item)
                            @if($item['input_type'] == 'dynamic')
                                @if($item['name'] == 'gender')
                                    <select name="gender" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
                                        @foreach($genders as $genderKey => $gender)
                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $gender ?></option>
                                        @endforeach
                                    </select>
                                @endif
                             @endif
                            @endforeach
                           @endforeach
                        </div>
                    </div>

                    



                    <div class="form-field select-input">
                          <label>City/State</label>
                           <input type="hidden" name="city_id" id="cityId">
                           <input type="text" id="selectLocationCity" class="form-control"  value="<?= $userProfileData['city'] ?>">
                       </div>

                   

                    <div class="form-field">
                       <label>Ethnicity</label>
                       <div class="field-input">
                          <input type="text" name="ethnicity" value="<?php echo $userSpecificationTexts['ethnicity']; ?>">
                       </div>
                    </div>
                 </div>
              </div>
              <div class="edit-pic">
                 <h4 class="font-25">Lifestyle</h4>
                 <div class="select-bar">
                    <div class="form-field">
                       <label>Do they Smoke ?</label>

                       <div class="select-input">
	                    @foreach(collect($userSpecificationData['step-3']['items'])->chunk(2) as $specification) 
	                        @foreach($specification as $itemKey => $item)
	                                @if($item['name'] == 'smoke')
	                                    <select name="smoke" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
	                                        @foreach($item['options'] as $genderKey => $options)
	                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
	                                        @endforeach
	                                    </select>
	                                @endif
	                             
	                            @endforeach
	                           @endforeach
                        </div>
                    </div>


                    <div class="form-field">
                       <label>Do they Drink ?</label>
                       <div class="select-input">

                       		@foreach(collect($userSpecificationData['step-3']['items'])->chunk(2) as $specification) 
	                        @foreach($specification as $itemKey => $item)
	                                @if($item['name'] == 'drink')
	                                    <select name="drink" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
	                                        @foreach($item['options'] as $genderKey => $options)
	                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
	                                        @endforeach
	                                    </select>
	                                @endif
	                             
	                            @endforeach
	                           @endforeach

                          <!-- <select>
                             <option>Occasionally</option>
                             <option>No</option>
                          </select> -->
                       </div>
                    </div>
                    <div class="form-field">
                       <label>Are you married?</label>
                       <div class="select-input">

                       	@foreach(collect($userSpecificationData['step-4']['items'])->chunk(2) as $specification) 
	                        @foreach($specification as $itemKey => $item)
	                                @if($item['name'] == 'married')
	                                    <select name="married" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
	                                        @foreach($item['options'] as $genderKey => $options)
	                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
	                                        @endforeach
	                                    </select>
	                                @endif
	                             
	                            @endforeach
	                           @endforeach

                          <!-- <select>
                             <option>Yes</option>
                             <option>No</option>
                          </select> -->
                       </div>
                    </div>
                    <div class="form-field">
                       <label>Do they have Children?</label>
                       <div class="select-input">

                       	@foreach(collect($userSpecificationData['step-4']['items'])->chunk(2) as $specification) 
	                        @foreach($specification as $itemKey => $item)
	                                @if($item['name'] == 'children')
	                                    <select name="children" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
	                                        @foreach($item['options'] as $genderKey => $options)
	                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
	                                        @endforeach
	                                    </select>
	                                @endif
	                             
	                            @endforeach
	                           @endforeach

                          <!-- <select>
                             <option>Yes - Not living with me</option>
                             <option>No</option>
                          </select> -->
                       </div>
                    </div>
                    <div class="form-field">
                       <label>No. of Children</label>
                       <div class="select-input">

                       	@foreach(collect($userSpecificationData['step-4']['items'])->chunk(2) as $specification) 
	                        @foreach($specification as $itemKey => $item)
	                                @if($item['name'] == 'no_of_children')
	                                    <select name="no_of_children" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
	                                        @foreach($item['options'] as $genderKey => $options)
	                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
	                                        @endforeach
	                                    </select>
	                                @endif
	                             
	                            @endforeach
	                           @endforeach

                          <!-- <select>
                             <option>1</option>
                             <option>2</option>
                             <option>3</option>
                          </select> -->
                       </div>
                    </div>
                    <div class="form-field">
                       <label>Are you willing to relocate?</label>
                       <div class="select-input">

                       		@foreach(collect($userSpecificationData['step-4']['items'])->chunk(2) as $specification) 
	                        @foreach($specification as $itemKey => $item)
	                                @if($item['name'] == 'relocate')
	                                    <select name="relocate" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
	                                        @foreach($item['options'] as $genderKey => $options)
	                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
	                                        @endforeach
	                                    </select>
	                                @endif
	                             
	                            @endforeach
	                           @endforeach

                          <!-- <select>
                             <option>Yes</option>
                             <option>No</option>
                          </select> -->
                       </div>
                    </div>
                 </div>
              </div>
           </div>
           <?php 
              //echo "<pre>";print_r($userSpecificationData);exit();
           ?>
           <div class="row personal-det-life-sty phy-life-style rel-stut-phy">
              <div class="edit-pic">
                 <h4 class="font-25">Relationships Status</h4>
                 <div class="select-bar">
                    <div class="form-field">
                       <label>Relationships</label>
                       <div class="select-input">
                       	
                          <select name="relationships">
                             <option value="open">Open</option>
                             <option value="close">Close</option>
                          </select>
                       </div>
                    </div>
                    <div class="form-field">
                       <label>Looking for</label>
                       <div class="select-input">
                        @foreach(collect($userSpecificationData['step-1']['items'])->chunk(2) as $specification) 
                           @foreach($specification as $itemKey => $item)
                                   @if($item['name'] == 'looking_for')
                                       <select name="looking_for" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
                                           @foreach($item['options'] as $genderKey => $options)
                                           <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                           @endforeach
                                       </select>
                                   @endif
                                
                               @endforeach
                              @endforeach

                          <!-- <select>
                             <option>Female, Trans male to female</option>
                             <option>No</option>
                          </select> -->
                       </div>
                    </div>
                    <div class="form-field edit_profile_kinks">
                          <label>Interests/Kinks</label>
                          <div class="select-input">
                            @foreach(collect($userSpecificationData['step-1']['items'])->chunk(2) as $specification) 
                              @foreach($specification as $itemKey => $item)
                                      @if($item['name'] == 'kinks')
                                          <?php 
                                             $selected_options = json_decode($item['selected_options']);
                                          ?>
                                          <select name="kinks[]" class="form-control-user lw-user-gender-select-box multiple-kinks" id="select_gender" required multiple="multiple">

                                              @foreach($item['options'] as $genderKey => $options)
                                              <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                              @endforeach
                                          </select>
                                      @endif
                                   
                                  @endforeach
                                 @endforeach

                             <!-- <select>
                                <option>BDSM, Blindfold, Piercing</option>
                                <option>No</option>
                             </select> -->
                          </div>
                       </div>
                 </div>
              </div>
              <div class="edit-pic">
                 <h4 class="font-25">Physical Appearance</h4>
                    <div class="select-bar">
                       <div class="form-field">
                          <label>Body type</label>
                          <div class="select-input">
                           @foreach(collect($userSpecificationData['step-2']['items'])->chunk(2) as $specification) 
                              @foreach($specification as $itemKey => $item)
                                      @if($item['name'] == 'body_type')
                                          <select name="body_type" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
                                              @foreach($item['options'] as $genderKey => $options)
                                              <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                              @endforeach
                                          </select>
                                      @endif
                                   
                                  @endforeach
                                 @endforeach
                             <!-- <select>
                                <option>Slim</option>
                                <option>fit</option>
                             </select> -->
                          </div>
                       </div>
                       <div class="form-field">
                          <label>Height (Ft & In)</label>
                          <div class="select-input">
                           @foreach(collect($userSpecificationData['step-2']['items'])->chunk(2) as $specification) 
                              @foreach($specification as $itemKey => $item)
                                      @if($item['name'] == 'height')
                                          <select name="height" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
                                              @foreach($item['options'] as $genderKey => $options)
                                              <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                              @endforeach
                                          </select>
                                      @endif
                                   
                                  @endforeach
                                 @endforeach

                             <!-- <input type="text" name="" placeholder="5.3 Ft"> -->
                          </div>
                       </div>
                       <div class="form-field">
                          <label>Hair color</label>
                          <div class="select-input">

                           @foreach(collect($userSpecificationData['step-2']['items'])->chunk(2) as $specification) 
                              @foreach($specification as $itemKey => $item)
                                      @if($item['name'] == 'hair_color')
                                          <select name="hair_color" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
                                              @foreach($item['options'] as $genderKey => $options)
                                              <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                              @endforeach
                                          </select>
                                      @endif
                                   
                                  @endforeach
                                 @endforeach

                             <!-- <select>
                                <option>Brown</option>
                                <option>Black</option>
                             </select> -->
                          </div>
                       </div>
                       <div class="form-field">
                          <label>Eye color</label>
                          <div class="select-input">

                           @foreach(collect($userSpecificationData['step-2']['items'])->chunk(2) as $specification) 
                              @foreach($specification as $itemKey => $item)
                                      @if($item['name'] == 'eye_color')
                                          <select name="eye_color" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
                                              @foreach($item['options'] as $genderKey => $options)
                                              <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                              @endforeach
                                          </select>
                                      @endif
                                   
                                  @endforeach
                                 @endforeach

                             <!-- <select>
                                <option>Golden</option>
                                <option>Black</option>
                             </select> -->
                          </div>
                       </div>
                    </div>
              </div>
           </div>
         <div class="row">
                <div class="col-md-12">
                <h4 class="font-25">Portfolio-Images</h4>
                </div>
                <div class="portfolio-images col-md-12">
                    <div class="row">
                        <?php 
                          if (!empty($photosData)) {
                           foreach ($photosData as $key => $photo) {

                                
                             $explode_photo = explode('users/',$photo['image_url']);
                             $explode_photo1 = explode('/',$explode_photo[1]);
                             $img_url = $explode_photo[0].'users/'.getUserID().'/'.$explode_photo1[1].'/'.$explode_photo1[2]; 

                                 $image_name = $explode_photo1[2];
                                 //echo "<pre>";print_r($explode_photo1[2]);exit();

                             ?>
                                <div class="col-md-3">
                                 <img src="<?php echo $img_url; ?>">
                                 <div class="edit-delete-btn">
                                   <!-- <a href="#" class="edit"><i class="far fa-edit"></i></a> -->
                                   <a href="<?= route('user.delete.portfolio', ['username' => $userData['userName'],'image_name' => $image_name]) ?>" class="delete"><i class="far fa-trash-alt"></i></a>
                                 </div>
                               </div>
                              
                         <?php  }
                          }
                          ?>
                    </div>
                       
                   <div class="col-md-12 text-center">
                        <div class="upload-more-img upload-custom-btn">

                           <input type="file" class="lw-file-uploader" data-instant-upload="true" data-action="<?= route('user.upload_photos', ['userid' => getUserID()]) ?>" data-default-image-url="" data-allowed-media='<?= getMediaRestriction('photos') ?>' multiple data-remove-all-media="true" value="UPLOAD MORE IMAGES">UPLOAD MORE IMAGES

                          <!-- <button type="button" class="btn btn-outline-secondary bg-color">UPLOAD MORE IMAGES</button> -->
                        </div>
                   </div>
               </div>
              
                   <div class="col-md-12 text-center">
                     <div class="upload-more-img edit-pic-action">
                       <button type="button" class="btn btn-outline-secondary bg-color bg-color-red">CANCEL</button>
                       <input type="submit" value="SAVE" class="btn btn-outline-secondary bg-color">
                     </div>
                   </div>
              
               </div>
            </form> 
          </div>                 
        </div><!-- container -->
  </section>
@push('appScripts')
<script>
 new WOW().init();
</script>
<script>
   $(document).ready(function() {
       $('.multiple-kinks').select2();
   });

   function afterUploadedProfilePicture(responseData) {
      $('#lwProfilePictureStaticImage, .lw-profile-thumbnail').attr('src', responseData.data.image_url);
   }
   function afterUploadedCoverPhoto(responseData) {
      $('#lwCoverPhotoStaticImage').attr('src', responseData.data.image_url);
   }
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

</script>

<script src="<?= __yesset('dist/js/select2.min.js') ?>"></script>
@endpush