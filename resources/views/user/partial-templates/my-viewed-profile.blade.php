@foreach($usersData as $user)
    <div class="col-lg-3 mb-5 load-matches" id="user-<?= $user['userId'] ?>" style="display: block;">
        <div class="profile-people">
            <a href="javascript:void(0)">
                <img data-src="<?= imageOrNoImageAvailable($user['userImageUrl']) ?>" class="lw-user-thumbnail lw-lazy-img" />
               
            <div class="profile-desc">
                <div class="title-icon d-flex">
                    <!-- #37ef00 -->
                    <?php
                    if ($user['userOnlineStatus']  ==  3) { ?>
                    <span class="dots-thik">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                            <g id="Path_284" data-name="Path 284" transform="translate(2 2)" fill="#ffffff">
                                <path d="M4,0A4,4,0,1,1,0,4,4,4,0,0,1,4,0Z" stroke="none"/>
                                <path d="M 4 0 C 1.790860176086426 0 0 1.790860176086426 0 4 C 0 6.209139823913574 1.790860176086426 8 4 8 C 6.209139823913574 8 8 6.209139823913574 8 4 C 8 1.790860176086426 6.209139823913574 0 4 0 M 4 -2 C 7.308410167694092 -2 10 0.6915898323059082 10 4 C 10 7.308410167694092 7.308410167694092 10 4 10 C 0.6915898323059082 10 -2 7.308410167694092 -2 4 C -2 0.6915898323059082 0.6915898323059082 -2 4 -2 Z" stroke="none" fill="rgba(0,0,0,0.4)"/>
                            </g>
                        </svg>
                    </span>
                    <?php } ?>

                    <?php
                        if ($user['userOnlineStatus']  ==  1) { ?>
                            <span class="dots-thik">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                                    <g id="Path_284" data-name="Path 284" transform="translate(2 2)" fill="#37ef00">
                                        <path d="M4,0A4,4,0,1,1,0,4,4,4,0,0,1,4,0Z" stroke="none"/>
                                        <path d="M 4 0 C 1.790860176086426 0 0 1.790860176086426 0 4 C 0 6.209139823913574 1.790860176086426 8 4 8 C 6.209139823913574 8 8 6.209139823913574 8 4 C 8 1.790860176086426 6.209139823913574 0 4 0 M 4 -2 C 7.308410167694092 -2 10 0.6915898323059082 10 4 C 10 7.308410167694092 7.308410167694092 10 4 10 C 0.6915898323059082 10 -2 7.308410167694092 -2 4 C -2 0.6915898323059082 0.6915898323059082 -2 4 -2 Z" stroke="none" fill="rgba(0,0,0,0.4)"/>
                                    </g>
                                </svg>
                            </span>
                    <?php } ?>

                    <!-- <h4>$user['userFullName']</h4> -->
                    <h4 id="display_user_detail"  class="display_user_detail" value="<?= $user['username']?>" ><?= $user['username'] ?></h4>
                    @if ((isset($user['isPremiumUser']) and $user['isPremiumUser'] == true))
                    <span class="yellow-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25">
                            <g id="Group_157" data-name="Group 157" transform="translate(-454 -843)">
                                <path id="Polygon_11" data-name="Polygon 11" d="M14.986,0l1.436,3.03L19.581,1.9l.167,3.349,3.349.167L21.97,8.577,25,10.014,22.75,12.5,25,14.986l-3.03,1.436L23.1,19.581l-3.349.167L19.581,23.1,16.423,21.97,14.986,25,12.5,22.75,10.014,25,8.577,21.97,5.419,23.1l-.167-3.349L1.9,19.581,3.03,16.423,0,14.986,2.25,12.5,0,10.014,3.03,8.577,1.9,5.419l3.349-.167L5.419,1.9,8.578,3.03,10.014,0,12.5,2.25Z" transform="translate(454 843)" fill="#ffd123"/>
                                <path id="Union_21" data-name="Union 21" d="M3.535-7272.223l-2.828-2.828a1,1,0,0,1,0-1.414,1,1,0,0,1,1.414,0l2.121,2.121,4.951-4.949a1,1,0,0,1,1.414,0,1,1,0,0,1,0,1.414l-6.365,6.363Z" transform="translate(460.843 8131.258)"/>
                            </g>
                        </svg>
                    </span>
                    @endif
                </div>

                <p><?= $user['detailString'] ?> <br>
                    @if($user['countryName'])
                    <?= $user['countryName'] ?>
                    @endif
                </p>
                @if(!empty($user['our_sexual_orientation']))
                    <h5>Sexual Orientation is <?= $user['our_sexual_orientation'] ?></h5>
                @else 
                    <h5>Open for relationships</h5>
                @endif
                @if(!empty($user['kinks']))
                    @php $kink = str_limit($user['kinks'], $limit = 25, $end = '...')  @endphp
                    <p>{{ucwords(str_replace(array(",", "-"), array(", ", " "),$kink))}}</p>
                @else 
                    <p>BDSM, Blindfold, Piercing</p>
                @endif

                <?php 

                   // echo "<pre>";print_r($user['userId']);exit();

                ?>


                <div class="profile-icon d-flex">
                    <div class="profile-icon-left-side d-flex">
                        <div class="main-heart">
                            <a href data-action="<?= route('user.write.like_dislike', ['toUserUid' => $user['userId'], 'like' => 1]) ?>" data-method="post" data-callback="onLikeCallback" title="Like" class="icon lw-ajax-link-action lw-like-action-btn" id="lwLikeBtn">


                                <?php 

                                        if ($user['like_sts'] == 1) { 
                                            
                                            ?>
                                            <span class="heart-icon" style="display:block;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22.002" height="19.503" viewBox="0 0 22.002 19.503">
                                                    <g id="Component_2_19" data-name="Component 2 – 19" transform="translate(1.001 1.003)">
                                                        <path id="Path_280" data-name="Path 280" d="M18.059-15.055a5.342,5.342,0,0,0-7.289.531L10-13.73l-.77-.793a5.341,5.341,0,0,0-7.289-.531,5.609,5.609,0,0,0-.387,8.121L9.113.871a1.225,1.225,0,0,0,1.77,0l7.559-7.8A5.606,5.606,0,0,0,18.059-15.055Z" transform="translate(0 16.251)" fill="#f06a6a" stroke="#f06a6a" stroke-width="2"/>
                                                    </g>
                                                </svg>
                                            </span>
                                       <?php } else { 
                                         
                                        ?>
                                            <span class="heart-icon" style="display:block;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22.002" height="19.503" viewBox="0 0 22.002 19.503">
                                                    <g id="Component_2_19" data-name="Component 2 – 19" transform="translate(1.001 1.003)">
                                                        <path id="Path_280" data-name="Path 280" d="M18.059-15.055a5.342,5.342,0,0,0-7.289.531L10-13.73l-.77-.793a5.341,5.341,0,0,0-7.289-.531,5.609,5.609,0,0,0-.387,8.121L9.113.871a1.225,1.225,0,0,0,1.77,0l7.559-7.8A5.606,5.606,0,0,0,18.059-15.055Z" transform="translate(0 16.251)" fill="rgba(240,106,106,0)" stroke="#f06a6a" stroke-width="2"/>
                                                    </g>
                                                </svg>
                                            </span>
                                       <?php } ?>


                                
                            </a>
                        </div>
                        <div class="main-star">
                            <a href data-action="<?= route('user.write.favourite', ['toUserUid' => $user['userId'], 'favourite' => 1]) ?>" data-method="post" data-callback="onFavouriteCallback" title="Favorite" class="icon lw-ajax-link-action lw-favourite-action-btn" id="lwFavouriteBtn">

                                <?php

                                        if ($user['favourite_sts'] == 1) { 
                                            
                                            ?>

                                            <span class="hover-show" style="display:block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22.936" height="22.029" viewBox="0 0 22.936 22.029">
                                                    <g id="Component_2_20" data-name="Component 2 – 20" transform="translate(1.019 1)">
                                                        <path id="Path_282" data-name="Path 282" d="M10.129-16.8,7.578-11.633,1.871-10.8A1.251,1.251,0,0,0,1.18-8.668L5.309-4.645,4.332,1.039A1.249,1.249,0,0,0,6.145,2.355L11.25-.328l5.105,2.684a1.25,1.25,0,0,0,1.812-1.316l-.977-5.684L21.32-8.668a1.251,1.251,0,0,0-.691-2.133l-5.707-.832L12.371-16.8A1.251,1.251,0,0,0,10.129-16.8Z" transform="translate(-0.801 17.5)" fill="#ebe054" stroke="rgba(235,224,84,0)" stroke-width="2"/>
                                                    </g>
                                                </svg>
                                            </span>
                                           
                                        <?php } else { 
                                            
                                            ?>

                                            <span class="star-icon" style="display:block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22.936" height="22.029" viewBox="0 0 22.936 22.029">
                                                    <g id="Component_2_20" data-name="Component 2 – 20" transform="translate(1.019 1)">
                                                        <path id="Path_282" data-name="Path 282" d="M10.129-16.8,7.578-11.633,1.871-10.8A1.251,1.251,0,0,0,1.18-8.668L5.309-4.645,4.332,1.039A1.249,1.249,0,0,0,6.145,2.355L11.25-.328l5.105,2.684a1.25,1.25,0,0,0,1.812-1.316l-.977-5.684L21.32-8.668a1.251,1.251,0,0,0-.691-2.133l-5.707-.832L12.371-16.8A1.251,1.251,0,0,0,10.129-16.8Z" transform="translate(-0.801 17.5)" fill="rgba(235,224,84,0)" stroke="#ebab54" stroke-width="2"/>
                                                    </g>
                                                </svg>
                                            </span>

                                           
                                       <?php } ?>
                            </a>                        
                        </div>
                    </div>
                 <!--    <div class="profile-icon-right-side">
                        <a href="#"><img src=" //url('dist/images/share-icon.png') "></a>
                    </div> -->
                </div>
                    
            </div>
        </a>
        </div>
    </div>
@endforeach


