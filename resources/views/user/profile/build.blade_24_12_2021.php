@section('page-title', __tr('Build your profile'))
@section('head-title', __tr('Build your profile'))
@section('keywordName', strip_tags(__tr('Build your profile')))
@section('keyword', strip_tags(__tr('Build your profile')))
@section('description', strip_tags(__tr('Build your profile')))
@section('keywordDescription', strip_tags(__tr('Build your profile')))
@section('page-image', getStoreSettings('logo_image_url'))
@section('twitter-card-image', getStoreSettings('logo_image_url'))
@section('page-url', url()->current())

<!-- include header -->
@include('includes.header')
<!-- /include header -->

<section class="complete-form-section">
    <div class="com-inner">
        <div class="com-header">
            <h2><?= __tr('LET US HELP YOU TO BUILD UP YOUR PROFILE') ?></h2>
            <p class="com-text"><?= __tr('Complete profiles have more chances to get recognized') ?></p>
        </div>
        <form id="msform" class="com-form lw-ajax-form lw-form" method="post" action="<?= route('user.profile.process') ?>" data-show-processing="true" data-secured="false" data-unsecured-fields="">
            <div class="formprogres-bar">
                <a href="javascript:void(0);" class="back-link"><span><img src="<?= url('dist/images/back-aerow.svg') ?>"></span><?= __tr('Back') ?></a>
                <ul id="progressbar" class="form-progress">
                    @php
                    $i = 1
                    @endphp
                    @foreach($specificationData as $specificationKey => $specifications)
                        <li class="pro-step step-<?= $i ?> @if ($i == 1) active @endif"><strong><?= $i ?></strong></li>
                    @php
                    $i++
                    @endphp
                    @endforeach
                </ul>
                <a href="javascript:void(0);" class="skip-link">Skip</a>
            </div>
            <div class="com-form-white">
                @php
                $i = 0
                @endphp

                @if(!__isEmpty($specificationData))
                @foreach($specificationData as $specificationKey => $specifications)
                <fieldset  class="set-active @if ($i == 0) active @endif">
                    <div class="form-card">
                        @foreach(collect($specifications['items'])->chunk(2) as $specification)                        
                            @foreach($specification as $itemKey => $item)
                            <div class="form-field">
                                @if($item['input_type'] == 'dynamic')
                                <label for="<?= $item['name'] ?>"><?= $item['label'] ?></label>
                                <div class="select-input">
                                    @if($item['name'] == 'gender')
                                        <select name="gender" class="form-control-user lw-user-gender-select-box" id="select_gender" required>
                                            @foreach($genders as $genderKey => $gender)
                                            <option value="<?= $genderKey ?>" <?= $item['selected_options'] == $genderKey ? 'selected' : '' ?>><?= $gender ?></option>
                                            @endforeach
                                        </select>
                                    @elseif($item['name'] == 'dob')
                                        <input type="date" min="{{ \Carbon\Carbon::now()->subYears(configItem('age_restriction.maximum'))->format('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->subYears(configItem('age_restriction.minimum'))->format('Y-m-d') }}" class="form-control form-control-user" name="birthday" placeholder="<?= __tr('DD/MM/YYYY') ?>" value="<?= $item['selected_options'] ?>" required="true">
                                    @elseif($item['name'] == 'city')
                                        <input type="hidden" name="city_id" id="cityId">
                                        <input type="text" id="selectLocationCity" class="form-control" placeholder="<?= __tr('Enter a location') ?>">
                                    @endif
                                </div>
                                @elseif($item['input_type'] == 'select')
                                <label for="<?= $item['name'] ?>"><?= $item['label'] ?></label>
                                @if($item['name'] == 'body_piercing' || $item['name'] == 'tattoo')
                                    <div class="radio-group">
                                        @php
                                        $j = 0
                                        @endphp
                                        @foreach($item['options'] as $optionKey => $option)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="<?= $item['name'] ?>" id="radio-<?= $item['name'] ?>-<?= $optionKey ?>" value="<?= $optionKey ?>" @if ($j != 0) checked @endif>
                                                <label class="form-check-label" for="radio-<?= $item['name'] ?>-<?= $optionKey ?>">
                                                    <?= $option ?>
                                                </label>
                                            </div>
                                            @php
                                            $j++
                                            @endphp
                                        @endforeach
                                    </div>
                                @else
                                    <div class="select-input">
                                        <select name="<?= $item['multiple'] == true ? $item['name'] . '[]' : $item['name'] ?>" class="" <?= $item['multiple'] == true ? 'multiple' : '' ?>>
                                            @if($item['multiple'] == false)
                                            <option value="" selected disabled><?= __tr('Choose __label__', [
                                                                                    '__label__' => $item['label']
                                                                                ]) ?></option>
                                            @endif
                                            @foreach($item['options'] as $optionKey => $option)
                                            <option value="<?= $optionKey ?>" <?= $item['selected_options'] == $optionKey ? 'selected' : '' ?>>
                                                <?= $option ?>
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                @elseif($item['input_type'] == 'textbox')
                                <label for="<?= $item['name'] ?>"><?= $item['label'] ?></label>
                                <div class="field-input">
                                    <input type="text" id="<?= $item['name'] ?>" name="<?= $item['name'] ?>" class="form-control" value="<?= $item['selected_options'] ?>">
                                </div>
                                @endif
                            </div>
                            @endforeach                        
                        @endforeach
                    </div>
                </fieldset>
                @php
                $i++
                @endphp
                @endforeach
                @endif
                <!-- /Specifications -->

                <div class="next-btn">
                    <a href="javascript:void(0);" class="action-button skip-link"><?= __tr('Next') ?></a>
                    <a href class="lw-ajax-form-submit-action action-submit btn btn-primary btn-user btn-block" style="display: none;">
                        <?= __tr('Next') ?>
                    </a>
                </div>
            </div>
        </form>
    </div>
    <div class="step-images">
        <div class="step-1 step-img active"><img src="<?= url('dist/images/form-bg.png') ?>"></div>
        <div class="step-2 step-img"><img src="<?= url('dist/images/form-bg-2.png') ?>"></div>
        <div class="step-3 step-img"><img src="<?= url('dist/images/form-bg-3.png') ?>"></div>
        <div class="step-4 step-img"><img src="<?= url('dist/images/form-bg-4.png') ?>"></div>
        <div class="step-5 step-img"><img src="<?= url('dist/images/form-bg-5.png') ?>"></div>
    </div>
</section>





@push('appScripts')
    <script>
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
                __DataRequest.post("<?= route('user.read.search_static_cities_without_login') ?>", {
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
@endpush


<!-- include footer -->
@include('includes.footer')
<!-- /include footer -->