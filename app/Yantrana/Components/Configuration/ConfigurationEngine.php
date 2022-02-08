<?php
/**
* ConfigurationEngine.php - Main component file
*
* This file is part of the Configuration component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Configuration;

use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\Media\MediaEngine;
use App\Yantrana\Components\Configuration\Repositories\ConfigurationRepository;
use App\Yantrana\Components\Configuration\Interfaces\ConfigurationEngineInterface;
use App\Yantrana\Support\CommonTrait;

class ConfigurationEngine extends BaseEngine implements ConfigurationEngineInterface
{
    /**
     * @var CommonTrait - Common Trait
     */
    use CommonTrait;

    /**
     * @var  ConfigurationRepository $configurationRepository - Configuration Repository
     */
    protected $configurationRepository;

    /**
     * @var  MediaEngine $mediaEngine - Media Engine
     */
    protected $mediaEngine;


    /**
     * Constructor
     *
     * @param  ConfigurationRepository $configurationRepository - Configuration Repository
     * @param  MediaEngine $mediaEngine - Media Engine
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(ConfigurationRepository $configurationRepository, MediaEngine $mediaEngine)
    {
        $this->configurationRepository = $configurationRepository;
        $this->mediaEngine              = $mediaEngine;
    }

    /**
     * Prepare Configuration.
     *
     * @param string $pageType
     *
     * @return array
     *---------------------------------------------------------------- */
    public function prepareConfigurations($pageType)
    {
        // Get settings from config
        $defaultSettings = $this->getDefaultSettings(config('__settings.items.' . $pageType));

        // check if default settings exists
        if (__isEmpty($defaultSettings)) {
            return $this->engineReaction(18, null, __tr('Invalid page type.'));
        }
        $configurationSettings = $dbConfigurationSettings = [];
        // Check if default settings exists
        if (!__isEmpty($defaultSettings)) {
            // Get selected default settings
            $configurationCollection = $this->configurationRepository->fetchByNames(array_keys($defaultSettings));
            // check if configuration collection exists
            if (!__isEmpty($configurationCollection)) {
                foreach ($configurationCollection as $configuration) {
                    $dbConfigurationSettings[$configuration->name] = $this->castValue($configuration->data_type, $configuration->value);
                }
            }
            // Loop over the default settings
            foreach ($defaultSettings as $defaultSetting) {
                $configurationSettings[$defaultSetting['key']] = $this->prepareDataForConfiguration($dbConfigurationSettings, $defaultSetting);
            }
        }
        //check page type is currency
        if ($pageType == 'general') {
            $configurationSettings['timezone_list'] = $this->getTimeZone();
            $languages = getStoreSettings('translation_languages');
            //set default language
            $languageList[] = [
                'id'    => 'en_US',
                'name'  => 'Default Language (English)',
                'status'  => true
            ];

            //check is not empty
            if (!__isEmpty($languages)) {
                foreach ($languages as $key => $language) {
                    if ($language['status']) {
                        $languageList[] = [
                            'id'         => $language['id'],
                            'name'         => $language['name'],
                            'status'     => $language['status']
                        ];
                    }
                }
            }
            $configurationSettings['languageList'] = $languageList;
        } else if ($pageType == 'currency') {
            $configurationSettings['currencies'] = config('__currencies.currencies');
            $configurationSettings['currency_options'] = $this->generateCurrenciesArray($configurationSettings['currencies']['details']);
        } else if ($pageType == 'premium-plans') {
            $defaultPlanDuration = $defaultSettings['plan_duration']['default'];
            $dbPlanDuration = $configurationSettings['plan_duration'];
            $configurationSettings['plan_duration'] = combineArray($defaultPlanDuration, $dbPlanDuration);
        } else if ($pageType == 'premium-feature') {
            $defaultFeaturePlans = $defaultSettings['feature_plans']['default'];
            $dbFeaturePlans = $configurationSettings['feature_plans'];
            $configurationSettings['feature_plans'] = combineArray($defaultFeaturePlans, $dbFeaturePlans);
        } else if ($pageType == 'email') {
            $configurationSettings['mail_drivers'] = configItem('mail_drivers');
            $configurationSettings['mail_encryption_types'] = configItem('mail_encryption_types');
        } else if ($pageType == 'user') {
            $configurationSettings['admin_choice_display_mobile_number'] = configItem('admin_choice_display_mobile_number');
        }

        return $this->engineReaction(1, [
            'configurationData' => $configurationSettings
        ]);
    }

    /**
     * Generate currency array.
     *
     * @param string $pageType
     * @param array $inputData
     *
     * @return array
     *---------------------------------------------------------------- */
    protected function generateCurrenciesArray($currencies)
    {
        $currenciesArray = [];
        foreach ($currencies as $key => $currency) {
            $currenciesArray[] = [
                'currency_code'    => $key,
                'currency_name'     => $currency['name']
            ];
        }

        $currenciesArray[] = [
            'currency_code'    => 'other',
            'currency_name'    => 'other'
        ];

        return $currenciesArray;
    }

    /**
     * Process Configuration Store.
     *
     * @param string $pageType
     * @param array $inputData
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processConfigurationsStore($pageType, $inputData)
    {
        $dataForStoreOrUpdate = $configurationKeysForDelete = [];
        $isDataAddedOrUpdated = false;

        // Get settings from config
        $defaultSettings = $this->getDefaultSettings(config('__settings.items.' . $pageType));

        // check if default settings exists
        if (__isEmpty($defaultSettings)) {
            return $this->engineReaction(18, ['show_message' => true], __tr('Invalid page type.'));
        }

        //manage create premium plan duration array
        if ($pageType == 'premium-plans') {
            if (!__isEmpty($inputData['plan_duration'])) {
                foreach ($inputData['plan_duration'] as $key => $plan) {
                    $inputData['plan_duration'][$key] = [
                        'enable'     => (isset($plan['enable']) and $plan['enable'] == 'true') ? true : false,
                        'price'     => $plan['price']
                    ];
                }
            }
        }

        $isExtendedLicence = (getStoreSettings('product_registration', 'licence') === 'dee257a8c3a2656b7d7fbe9a91dd8c7c41d90dc9');

        // Check if input data exists
        if (!__isEmpty($inputData)) {
            // Get selected default settings
            $configurationCollection = $this->configurationRepository->fetchByNames(array_keys($defaultSettings))->pluck('value', 'name')->toArray();
            foreach ($inputData as $inputKey => $inputValue) {
                // Check if default text and form text not same
                if (array_key_exists($inputKey, $defaultSettings) and ($inputValue != $defaultSettings[$inputKey]['default'] or !__isEmpty($defaultSettings[$inputKey]['default']))) {
                    $castValues = $this->castValue(
                        ($defaultSettings[$inputKey]['data_type'] == 4)
                            ? 5 : $defaultSettings[$inputKey]['data_type'], // for Encode purpose only
                        $inputValue
                    );
                    if (array_get($defaultSettings[$inputKey], 'hide_value') and $defaultSettings[$inputKey]['hide_value'] and !__isEmpty($inputValue)) {
                        $dataForStoreOrUpdate[] = [
                            'name'      => $inputKey,
                            'value'     => $castValues,
                            'data_type' => $defaultSettings[$inputKey]['data_type']
                        ];
                    } else if (!array_get($defaultSettings[$inputKey], 'hide_value')) {
                        $dataForStoreOrUpdate[] = [
                            'name'      => $inputKey,
                            'value'     => $castValues,
                            'data_type' => $defaultSettings[$inputKey]['data_type']
                        ];
                    }
                }

                if (!$isExtendedLicence and in_array($inputKey, [
                    'use_test_razorpay',
                    'use_test_stripe',
                    'use_test_paypal_checkout'
                ]) and !$inputValue) {
                    return $this->engineReaction(2, [
                        'show_message' => true
                    ], __tr('You need to purchase extended licence to use live keys.'));
                }
                // Check if default value and input value same and it is exists
                if ((array_key_exists($inputKey, $defaultSettings))
                    and ($inputValue == $defaultSettings[$inputKey]['default'])
                    and (!isset($defaultSettings[$inputKey]['hide_value']))
                ) {
                    if (array_key_exists($inputKey, $configurationCollection)) {
                        $configurationKeysForDelete[] = $inputKey;
                    }
                }
                continue;
            }
            // Send data for store or update
            if (
                !__isEmpty($dataForStoreOrUpdate)
                and $this->configurationRepository->storeOrUpdate($dataForStoreOrUpdate)
            ) {
                activityLog('Site configuration settings stored / updated.');
                $isDataAddedOrUpdated = true;
            }

            // Check if deleted keys deleted successfully
            if (
                !__isEmpty($configurationKeysForDelete)
                and $this->configurationRepository->deleteConfiguration($configurationKeysForDelete)
            ) {
                $isDataAddedOrUpdated = true;
            }

            // Check if data added / updated or deleted
            if ($isDataAddedOrUpdated) {
                return $this->engineReaction(1, ['show_message' => true], __tr('Configuration updated successfully.'));
            }

            return $this->engineReaction(14, ['show_message' => true], __tr('Nothing updated.'));
        }

        return $this->engineReaction(2, ['show_message' => true], __tr('Something went wrong on server.'));
    }

    /**
     * Get Timezone list
     *
     * @return array
     *---------------------------------------------------------------- */
    protected function getTimeZone()
    {
        $timezoneCollection = [];
        $timezoneList = timezone_identifiers_list();
        foreach ($timezoneList as $timezone) {
            $timezoneCollection[] = [
                'value' => $timezone,
                'text' => $timezone,
            ];
        }

        return $timezoneCollection;
    }

    /**
     * Process product registration
     *
     * @param array $inputData
     *
     * @return void
     *---------------------------------------------------------------- */
    public function processProductRegistration($inputData = [])
    {
        return $this->processConfigurationsStore('product_registration', [
            'product_registration' => [
                'registration_id' =>  array_get($inputData, 'registration_id', ''),
                'email' =>  array_get($inputData, 'your_email', ''),
                'licence' => array_get($inputData, 'licence_type', ''),
                'registered_at' => now(),
                'signature' => sha1(
                    array_get($_SERVER, 'HTTP_HOST', '') .
                        array_get($inputData, 'registration_id', '')
                )
            ]
        ]);
    }

    /**
     * Process product registration removal
     *
     * @return void
     *---------------------------------------------------------------- */
    public function processProductRegistrationRemoval()
    {
        return $this->processConfigurationsStore('product_registration', [
            'product_registration' => [
                'registration_id' =>  '',
                'email' =>  '',
                'licence' => '',
                'registered_at' => now(),
                'signature' => ''
            ]
        ]);
    }
}
