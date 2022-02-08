<!-- Page Heading -->
<h3><?= __tr('General Settings') ?></h3>
<!-- Page Heading -->
<hr>
<!-- General setting form -->
<form class="lw-ajax-form lw-form" method="post" action="<?= route('manage.configuration.write', ['pageType' => request()->pageType]) ?>">
<?php /* ## CUSTOM-CHANGE ##
<div class="row">
    <!-- Color Theme -->
    <div class="form-group col-lg-6">
        <label for="lwColorTheme"><?= __tr('Default Color Theme') ?></label>
        <select id="lwColorTheme" class="form-control form-control-user" name="color_theme" required>
            <option value="dark" <?= $configurationData['color_theme'] == 'dark' ? 'selected' : '' ?>><?= __tr('Dark') ?></option>
            <option value="light" <?= $configurationData['color_theme'] == 'light' ? 'selected' : '' ?>><?= __tr('Light') ?></option>
        </select>
    </div>
    <!-- /Color Theme -->
    <!-- Allow User to Change Theme -->
    <div class="form-group col-lg-6">
        <label for="lwAllowUserToChangeTheme"><?= __tr('Allow User to Change Color Theme') ?></label>
        <select id="lwAllowUserToChangeTheme" class="form-control form-control-user" name="allow_user_to_change_theme" required>
            <option value="1" <?= $configurationData['allow_user_to_change_theme'] == 1 ? 'selected' : '' ?>><?= __tr('Yes') ?></option>
            <option value="0" <?= $configurationData['allow_user_to_change_theme'] == 0 ? 'selected' : '' ?>><?= __tr('No') ?></option>
        </select>
    </div>
    <!-- /Allow User to Change Theme -->
</div> */ ?>
<hr class="mb-3">
    <div class="row">
        <div class="col-lg-6">
            <label for="lwUploadLogo"><?= __tr('Upload Logo') ?></label>
            <input type="file" class="lw-file-uploader" data-instant-upload="true" data-action="<?= route('media.upload_logo') ?>" id="lwUploadLogo" data-callback="afterUploadedFile" data-default-image-url="<?= getStoreSettings('logo_image_url') ?>">
        </div>
        <div class="col-lg-6">
            <label for="lwUploadSmallLogo"><?= __tr('Upload Small Logo') ?></label>
            <input type="file" class="lw-file-uploader" data-instant-upload="true" data-action="<?= route('media.upload_small_logo') ?>" id="lwUploadSmallLogo" data-callback="afterUploadedFile" data-default-image-url="<?= getStoreSettings('small_logo_image_url') ?>">
        </div>
        <div class="col-lg-6">
            <label for="lwUploadAdminLogo"><?= __tr('Upload Admin Logo') ?></label>
            <input type="file" class="lw-file-uploader" data-instant-upload="true" data-action="<?= route('media.upload_admin_logo') ?>" id="lwUploadAdminLogo" data-callback="afterUploadedFile" data-default-image-url="<?= getStoreSettings('admin_logo_image_url') ?>">
        </div>
        <div class="col-lg-2">
            <label for="lwUploadFavicon"><?= __tr('Upload Favicon') ?></label>
            <input type="file" class="lw-file-uploader" data-instant-upload="true" data-action="<?= route('media.upload_favicon') ?>" data-callback="afterUploadedFile" id="lwUploadFavicon" data-default-image-url="<?= getStoreSettings('favicon_image_url') ?>">
        </div>
        <div class="col-lg-6">
            <label for="lwUploadFooterLogo"><?= __tr('Upload Footer Logo') ?></label>
            <input type="file" class="lw-file-uploader" data-instant-upload="true" data-action="<?= route('media.upload_footer_logo') ?>" id="lwUploadFooterLogo" data-callback="afterUploadedFile" data-default-image-url="<?= getStoreSettings('footer_logo_image_url') ?>">
        </div>
    </div>

    <!-- Website Name -->
    <div class="form-group">
        <label for="lwWebsiteName"><?= __tr('Your Website Name') ?></label>
        <input type="text" class="form-control form-control-user" name="name" id="lwWebsiteName" value="<?= $configurationData['name'] ?>" required>
    </div>
    <!-- /Website Name -->
    <!-- Business Email -->
    <div class="form-group">
        <label for="lwBusinessEmail"><?= __tr('Business Email') ?></label>
        <input type="email" class="form-control form-control-user" name="business_email" id="lwBusinessEmail" value="<?= $configurationData['business_email'] ?>" required>
    </div>
    <!-- /Business Email -->
    <!-- Contact Email -->
    <div class="form-group">
        <label for="lwContactEmail"><?= __tr('Contact Email') ?></label>
        <input type="email" class="form-control form-control-user" name="contact_email" id="lwContactEmail" value="<?= $configurationData['contact_email'] ?>">
    </div>
    <!-- /Contact Email -->

    <?php /* ## CUSTOM-CHANGE ##
    <!-- Select Timezone -->
    <div class="form-group">
        <label for="lwSelectTimezone"><?= __tr('Select Timezone') ?></label>
        <select id="lwSelectTimezone" class="form-control form-control-user" name="timezone" required>
            @foreach ($configurationData['timezone_list'] as $timezone)
            <option value="<?= $timezone['value'] ?>" <?= $configurationData['timezone'] == $timezone['value'] ? 'selected' : '' ?>><?= $timezone['text'] ?></option>
            @endforeach
        </select>
    </div>
    <!-- /Select Timezone -->

    <!-- Distance Measurement -->
    <div class="form-group">
        <label for="lwDistanceMeasurement"><?= __tr('Distance Measurement') ?></label>
        <select id="lwDistanceMeasurement" class="form-control form-control-user" name="distance_measurement" required>
            <option value="6371" <?= $configurationData['distance_measurement'] == '6371' ? 'selected' : '' ?>><?= __tr('KM') ?></option>
            <option value="3959" <?= $configurationData['distance_measurement'] == '3959' ? 'selected' : '' ?>><?= __tr('Miles') ?></option>
        </select>
    </div>
    <!-- /Distance Measurement -->

    <!-- Select Default language -->
    <div class="form-group mt-2">
        <label for="lwSelectDefaultLanguage"><?= __tr('Default Language') ?></label>
        <select id="lwSelectDefaultLanguage" placeholder="<?= __tr('Select default language...') ?>" name="default_language">
            @if (!__isEmpty($configurationData['languageList']))
            @foreach ($configurationData['languageList'] as $key => $language)
            <option value="<?= $language['id'] ?>" <?= $configurationData['default_language'] == $language['id'] ? 'selected' : '' ?> required><?= $language['name'] ?></option>
            @endforeach
            @endif
        </select>
    </div>
    <!-- /Select Default language -->
     */ ?>

    <!-- Update Button -->
    <a href class="lw-ajax-form-submit-action btn btn-primary btn-user lw-btn-block-mobile">
        <?= __tr('Update') ?>
    </a>
    <!-- /Update Button -->
</form>
<!-- /General setting form -->

@push('appScripts')
    <script>
        // After file successfully uploaded then this function is called
        function afterUploadedFile(responseData) {
            var requestData = responseData.data;
            $('#lwUploadedLogo').attr('src', requestData.path);
        }
        $(function() {
            $('#lwSelectTimezone').selectize();
        });

        //initialize selectize element
        $(function() {
            $('#lwSelectDefaultLanguage').selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['id', 'name']
            });
        });
    </script>
@endpush
