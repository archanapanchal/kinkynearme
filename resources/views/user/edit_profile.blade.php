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
                       <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                       <li class="breadcrumb-item active"><a href="{{url('/user/@'.$userData['userName'])}}">My Profile</a><span></span></li>
                       <li class="breadcrumb-item active"><span>Edit Profile</span></li>
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
                
                 <img src="<?= $userData['profilePicture'] ?>" id="lwProfilePictureStaticImage">
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
                           <textarea name="about_me" cols="58">{{ trim($userProfileData['aboutMe']) ?? ''}}</textarea>
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
                       <label>First Name (not visible to other members)</label>
                       <div class="field-input">
                          <input type="text" name="fname" placeholder="Sheridan" value="<?= $userData['first_name'] ?>">
                          @error('fname')
                             <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                             </span>
                         @enderror
                       </div>
                    </div>
                    <div class="form-field">
                       <label>Last Name (not visible to other members)</label>
                       <div class="field-input">
                          <input type="text" name="lname" placeholder="Kuhn" value="<?= $userData['last_name'] ?>">
                          @error('lname')
                             <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                             </span>
                         @enderror
                       </div>
                    </div>
                    <div class="form-field">
                       <label>Username</label>
                       <div class="field-input">
                          <input type="text" name="username" placeholder="sheridankuhn" value="<?= $userData['userName'] ?>" id="username">
                          @error('username')
                             <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                             </span>
                         @enderror
                       </div>
                    </div>
                    <div class="form-field">
                       <label>Email Address (not visible to other members)</label>
                       <div class="field-input">
                          <input type="text" name="email" placeholder="johnsmith@gmail.com" value="<?= $userData['email'] ?>">
                          @error('email')
                             <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                             </span>
                         @enderror
                       </div>
                    </div>
                    <?php //echo "<pre>"; print_r($userProfileData); exit; ?>
                    <div class="form-field">
                       <label>Date of Birth</label>
                       <div class="date-input">
                          <input type="date" onkeydown="return false" max="{{ date('Y-m-d',strtotime('18 years ago')) }}" min="{{ date('Y-m-d',strtotime('100 years ago')) }}"  name="dob" placeholder="Jan 3 1985" value="<?php echo date("Y-m-d", strtotime($userProfileData['dob'])); ?>">
                          @error('dob')
                             <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                             </span>
                         @enderror
                       </div>
                    </div>

                    <div class="form-field">
                        <label>I am a</label>
                        <div class="select-input">
                    @foreach(collect($userSpecificationData['step-1']['items'])->chunk(2) as $specification)                        
                        @foreach($specification as $itemKey => $item)
                            @if($item['input_type'] == 'dynamic')
                                @if($item['name'] == 'gender')
                                    <select name="gender" class="form-control-user lw-user-gender-select-box" id="select_gender">
                                        @foreach($genders as $genderKey => $gender)
                                        <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $gender ?></option>
                                        @endforeach
                                    </select>
                                    @error('gender')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                @endif
                             @endif
                            @endforeach
                           @endforeach
                        </div>
                    </div>

                    <?php //echo "<pre>"; print_r($userSpecifications['city_id']); exit;?>
               

                   

                    <div class="form-field">
                       <label>Ethnicity</label>
                       <div class="select-input">
                       @foreach(collect($userSpecificationData['step-1']['items'])->chunk(2) as $specification) 
                           @foreach($specification as $itemKey => $item)
                                   @if($item['name'] == 'ethnicity')
                                       <select name="ethnicity" class="form-control-user lw-user-gender-select-box" id="select_gender" >
                                           @foreach($item['options'] as $genderKey => $options)
                                           <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                           @endforeach
                                       </select>
                                       @error('ethnicity')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                   @endif
                                
                               @endforeach
                              @endforeach
                        </div>
                    </div>
                 </div>
              </div>
              <div class="edit-pic">
                 <h4 class="font-25">Lifestyle</h4>
                 <div class="select-bar">
                    <div class="form-field">
                       <label>Do you Smoke?</label>

                       <div class="select-input">
                       @foreach(collect($userSpecificationData['step-3']['items'])->chunk(2) as $specification) 
                           @foreach($specification as $itemKey => $item)
                                   @if($item['name'] == 'smoke')
                                       <select name="smoke" class="form-control-user lw-user-gender-select-box" id="select_gender" >
                                           <option value="" selected="">Choose Do you Smoke?</option>
                                           @foreach($item['options'] as $genderKey => $options)
                                           <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                           @endforeach
                                       </select>
                                       @error('smoke')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                   @endif
                                
                               @endforeach
                              @endforeach
                        </div>
                    </div>


                    <div class="form-field">
                       <label>Do you Drink?</label>
                       <div class="select-input">

                           @foreach(collect($userSpecificationData['step-3']['items'])->chunk(2) as $specification) 
                           @foreach($specification as $itemKey => $item)
                                   @if($item['name'] == 'drink')
                                       <select name="drink" class="form-control-user lw-user-gender-select-box" id="select_gender" >
                                            <option value="" selected="">Choose Do you drink?</option>
                                           @foreach($item['options'] as $genderKey => $options)
                                           <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                           @endforeach
                                       </select>
                                       @error('drink')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror

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
                                       <select name="married" class="form-control-user lw-user-gender-select-box" id="select_gender" >
                                           <option value="" selected="">Choose Are you married?</option>
                                           @foreach($item['options'] as $genderKey => $options)
                                           <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                           @endforeach
                                       </select>
                                        @error('married')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
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
                       <label>Do you have Children?</label>
                       <div class="select-input">

                        @foreach(collect($userSpecificationData['step-4']['items'])->chunk(2) as $specification) 
                           @foreach($specification as $itemKey => $item)
                                   @if($item['name'] == 'children')
                                       <select name="children" class="form-control-user lw-user-gender-select-box" id="select_gender" >
                                            <option value="" selected="">Choose Do you have children?</option>
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
                                        <select name="no_of_children" class="form-control-user lw-user-gender-select-box" id="select_gender" >
                                            <option value="" selected="">Choose No. of children?</option>
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
                                       <select name="relocate" class="form-control-user lw-user-gender-select-box" id="select_gender" >
                                            <option value="" selected="">Choose Are you willing to relocate?</option>
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
                 <!-- <h4 class="font-25">Relationships Status</h4>
                 <div class="select-bar">
                    <div class="form-field">
                       <label>Relationships</label>
                       <div class="select-input">
                            <select name="relationships">
                                 <option value="open">Open</option>
                                 <option value="close">Close</option>
                          </select>
                          @error('relationships')
                             <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                             </span>
                         @enderror
                       </div>
                    </div> -->
                    <h4 class="font-25">My Sexual Orientation</h4>
                    <div class="select-bar">
                        <div class="form-field">
                           <label>My Sexual Orientation</label>
                           <div class="select-input">
                                @foreach(collect($userSpecificationData['step-1']['items'])->chunk(2) as $specification) 
                                    @foreach($specification as $itemKey => $item)
                                        @if($item['name'] == 'our_sexual_orientation')
                                           <select name="our_sexual_orientation" class="form-control-user lw-user-gender-select-box" id="select_gender">
                                                <option value="" selected="">Choose Our sexual orientation</option>
                                               @foreach($item['options'] as $genderKey => $options)
                                               <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                               @endforeach
                                           </select>
                                            @error('our_sexual_orientation')
                                                 <span class="invalid-feedback" role="alert">
                                                     <strong>{{ $message }}</strong>
                                                 </span>
                                             @enderror
                                       @endif
                                        
                                    @endforeach
                              @endforeach
                             
                           </div>
                        </div>
                    <div class="form-field edit_profile_kinks">
                       <label>Looking for</label>
                       <div class="select-input">
                        @foreach(collect($userSpecificationData['step-1']['items'])->chunk(2) as $specification) 
                           @foreach($specification as $itemKey => $item)
                                   @if($item['name'] == 'looking_for')
                                        <?php 
                                           $selected_options = explode(',',$item['selected_options']);
                                        ?>
                                       <select name="looking_for[]" class="form-control-user lw-user-gender-select-box " id="multiple-looking_for" multiple="multiple">
                                            @foreach($item['options'] as $genderKey => $options)
                                           <option value="<?= $genderKey ?>" <?= in_array($genderKey, $selected_options) ? 'selected' : '' ?>><?= $options ?></option>
                                           @endforeach
                                       </select>
                                       @error('looking_for')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
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
                                       $selected_options = explode(',',$item['selected_options']);
                                    ?>
                                    <select name="kinks[]" class="form-control-user lw-user-gender-select-box multiple-kinks" id="select_gender" multiple="multiple">
                                       @foreach($item['options'] as $genderKey => $options)
                                            
                                          @if(!empty($selected_options))
                                          <option value="<?= $genderKey ?>" <?= in_array($genderKey, $selected_options) ? 'selected' : '' ?>><?= $options ?></option>
                                          @else
                                             <option value="<?= $genderKey ?>"><?= $options ?></option>
                                          @endif
                                       @endforeach
                                    </select>
                                 @endif   
                              @endforeach
                           @endforeach
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
                                          <select name="body_type" class="form-control-user lw-user-gender-select-box" id="select_gender" >
                                                <option value="" selected="">Choose Body Type</option>
                                              @foreach($item['options'] as $genderKey => $options)
                                              <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                              @endforeach
                                          </select>
                                          @error('body_type')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
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
                                          <select name="height" class="form-control-user lw-user-gender-select-box" id="select_gender" >
                                                <option value="" selected="">Choose Height (in ft )</option>
                                              @foreach($item['options'] as $genderKey => $options)
                                              <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                              @endforeach
                                          </select>
                                          @error('height')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

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
                                          <select name="hair_color" class="form-control-user lw-user-gender-select-box" id="select_gender" >
                                                <option value="" selected="">Choose Hair Color</option>
                                              @foreach($item['options'] as $genderKey => $options)
                                              <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                              @endforeach
                                          </select>
                                          @error('hair_color')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
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
                                          <select name="eye_color" class="form-control-user lw-user-gender-select-box" id="select_gender" >
                                              <option value="" selected="" >Choose Eye Color</option>
                                              @foreach($item['options'] as $genderKey => $options)
                                              <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $options ?></option>
                                              @endforeach
                                          </select>
                                          @error('eye_color')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
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
                        <?php if($config == 1) { ?><h4 class="font-25">Portfolio-Images</h4> <?php }?>
                    </div>
                    <?php if($config == 1) { ?>
                        <div class="portfolio-images col-md-12">
                            <div class="row" >
                                <?php 
                                // echo "<pre>";
                                // print_r($photosData);
                                  if (!empty($photosData)) {
                                   foreach ($photosData as $key => $photo) {   
                                     $explode_photo = explode('users/',$photo['image_url']);
                                     $explode_photo1 = explode('/',$explode_photo[1]);
                                     $img_url = $explode_photo[0].'users/'.getUserID().'/'.$explode_photo1[1].'/'.$explode_photo1[2]; 

                                         $image_name = $explode_photo1[2];
                                         //echo "<pre>";print_r($explode_photo1[2]);exit();

                                     ?>
                                        <div class="col-md-3 image-{{$key}}">
                                         <img src="<?php echo $img_url; ?>">
                                         <div class="edit-delete-btn">

                                            <a style="cursor: pointer;" class="delete-portfolio-image" image_name="{{$image_name}}" username="{{$userData['userName']}}" key="{{$key}}"><i class="far fa-trash-alt"></i></a>

                                           <!-- <a href="#" class="edit"><i class="far fa-edit"></i></a> -->
                                          <!--  <a href="<?= route('user.delete.portfolio', ['username' => $userData['userName'],'image_name' => $image_name]) ?>" class="delete" ><i class="far fa-trash-alt"></i></a> -->
                                         </div>
                                       </div>
                                      
                                 <?php  }
                                  }
                                  ?>
                            </div>

                           <div class="col-md-12 text-center">
                                <div class="upload-more-img upload-custom-btn">

                                   <input type="file" class="lw-file-uploader" data-instant-upload="true" data-action="<?= route('user.upload_photos.front', ['userid' => getUserID()]) ?>" data-default-image-url="" data-allowed-media='<?= getMediaRestriction('photos') ?>' multiple data-remove-all-media="true" value="UPLOAD MORE IMAGES" data-callback="afterFileUpload" accept="image/*" >UPLOAD MORE IMAGES
                                  <!--   data-callback="afterFileUpload"  -->
                                  <!-- <button type="button" class="btn btn-outline-secondary bg-color">UPLOAD MORE IMAGES</button> -->
                                </div>
                                <p>Allowed format - jpg, png & Max file size 2 MB</p>
                           </div>
                        </div>
                    <?php }?>
                    <!-- upload video -->



                    <?php if ($userData['user_videos_limits'] != 0) { ?>
                        
                  
                    <div class="col-md-12">
                        <h4 class="font-25">Portfolio-Videos</h4>
                    </div>
                    <div class="row">
                        <?php

                          if (!empty($userData['user_videos'])) {
                           foreach ($userData['user_videos'] as $key => $video) {
                             ?>
                                <div class="col-md-6">
                                  <iframe width="420" height="315" src="<?= $video['url'] ?>" autoplay="0"></iframe> 
                                 <div class="edit-delete-btn">
                                   <!-- <a href="#" class="edit"><i class="far fa-edit"></i></a> -->
                                   <a href="<?= route('user.delete_video', ['video_id' => $video['video_id']]) ?>" class="delete"><i class="far fa-trash-alt"></i></a>
                                 </div>
                               </div>
                              
                         <?php  }
                          }
                          ?>
                    </div>
                    <?php if ($plan_deatail['title'] != "7 Days Trial") { ?>
                    <div class="col-md-12 text-center">
                        <div id="loading" style="display:none;"></div>
                        <div class="upload-more-img upload-custom-btn">
                          <div class="form-group">
                                <input type='file' id="file" name='file' class="submitvidios" accept="video/*">
                                UPLOAD MORE VIDEO
                          </div>
                        </div>
                          <p>Allowed format - mp4, mov, wmv, avi, mkv & Max file size 30 MB</p>
                          
                        <div class="alert displaynone" id="responseMsg"></div>
                    </div>
                    <?php } }?>
                    <!-- upload video End -->
                    <div class="col-md-12 text-center">
                     <div class="upload-more-img edit-pic-action">
                       <a type="button" class="btn btn-outline-secondary bg-color bg-color-red" href="{{route('user.profile.build')}}">CANCEL</a>
                       <input type="submit" value="SAVE" class="btn btn-outline-secondary bg-color">
                     </div>
                    </div>  
               </div>
            </form> 
          </div>                 
        </div><!-- container -->
  </section>
  <style type="text/css">
     .hideanimation {
    display:none !important;
}
#loading {
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 100;
  width: 100vw;
  height: 100vh;
  background-color: rgba(192, 192, 192, 0.5);
  background-image: url("https://i.stack.imgur.com/MnyxU.gif");
  background-repeat: no-repeat;
  background-position: center;
}
  </style>
@push('appScripts')
<script>
 new WOW().init();
</script>
<script>

    $(document).on('click','.delete-portfolio-image',function(){
        var username = $(this).attr('username');
        var imagename = $(this).attr('image_name');
        var key = $(this).attr('key');
        var token = $("meta[name='csrf-token']").attr("content");
        var url = "{{url('/')}}/user/settings/@"+username+'/delete-portfolio-image';
        
        $.ajax(
        {
            url: url,
            type: 'get',
            data: {
                "username": username,
                "image_name": imagename,
                "_token": token,
            },
            success: function (response){
                $('.image-'+key).css('display','none');
                alert('Portfolio picture deleted successfully.');
            }
        });

    })

  
    $(function() {
        $('#username').on('keypress', function(e) {
            if (e.which == 32){
               return false;
            }
        });
    });
   $('select[name="children"]').change(function() {
         var select_children = $(this).val();
         if (select_children == "no") {

            $('label[for="no_of_children"]').css("pointer-events","none");
            $('select[name="no_of_children"]').css("pointer-events","none");
         }else{
            $('label[for="no_of_children"]').css("pointer-events","painted");
            $('select[name="no_of_children"]').css("pointer-events","painted");
         }  
    });


   $(document).ready(function() {
     $('.filepond--drop-label , .filepond--list-scroller, .filepond--panel, .filepond--assistant, .filepond--data, .filepond--drip').addClass('hideanimation ');
      $('.multiple-kinks').select2();
      $('#multiple-looking_for').select2();

      ///////////
   });
   /*after upload reload*/
    function afterFileUpload(responseData) {
        
        if (!_.isUndefined(responseData.data.stored_photo)) {
           location.reload(); 
        }
    }

   /*single upload image */
   function afterUploadedProfilePicture(responseData) {
      $('#lwProfilePictureStaticImage').attr('src', responseData.data.image_url);
      $('#lwProfilePictureStaticImage, .lw-profile-thumbnail').attr('src', responseData.data.image_url);
   }
   function afterUploadedCoverPhoto(responseData) {
      $('#lwCoverPhotoStaticImage').attr('src', responseData.data.image_url);
   }
    //$('#selectLocationCity').selectize({cities_full_name});
    $(document).ready(function(){
      $("#selectLocationCity").keypress(function(){
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

            $('#cityIdS').val(value);
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
      });
    });
   
    /*User image delete functioanlity*/
   /* $(".deleteRecord").click(function(){
        var username = $(this).attr('data-username');
        
        var image_name = $(this).attr('data-imgname');
        var token = $("meta[name='csrf-token']").attr("content");
        var url = $(this).data('remote');
        $.ajax(
        {
            url: url,
            type: 'get',
            data: {
                "username": username,
                "image_name": image_name,
                "_token": token,
            },
            success: function (){
                console.log("it Works");
            }
        });
    });*/
</script>

<script src="<?= __yesset('dist/js/select2.min.js') ?>"></script>
@endpush