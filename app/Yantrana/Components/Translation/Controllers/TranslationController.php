<?php
/**
* TranslationController.php - Controller file
*
* This file is part of the Translation component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Translation\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\Translation\{
    TranslationEngine,
    Requests\LanguageAddRequest,
    Requests\LanguageUpdateRequest
};
use App\Yantrana\Support\CommonUnsecuredPostRequest;
use App\Yantrana\Components\Translation\Requests\TranslationUpdateRequest;

class TranslationController extends BaseController
{
    /**
     * @var  TranslationEngine $translationEngine - Translation Engine
     */
    protected $translationEngine;

    /**
     * Constructor
     *
     * @param  TranslationEngine $translationEngine - Translation Engine
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(TranslationEngine $translationEngine)
    {
        $this->translationEngine = $translationEngine;
    }

    /**
     * lists Translate
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function languages()
    {
        $processReaction = $this->translationEngine->languages();

        return $this->loadManageView(
            'translation.languages_list',
            $processReaction['data']
        );
    }

    /**
     * Store Language
     *
     * @param object LanguageAddRequest $request
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function storeLanguage(LanguageAddRequest $request)
    {
        $processReaction = $this->translationEngine->processStoreLanguage($request->all());

        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Store Language
     *
     * @param object LanguageUpdateRequest $request
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function updateLanguage(LanguageUpdateRequest $request)
    {
        $processReaction = $this->translationEngine->processUpdateLanguage($request->all());

        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Store Language
     *
     * @param object LanguageAddRequest $request
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function deleteLanguage(CommonUnsecuredPostRequest $request, $languageId)
    {
        $processReaction = $this->translationEngine->processDeleteLanguage($languageId);

        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * lists Translate
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function lists($languageId)
    {
        $processReaction = $this->translationEngine->lists($languageId);
        return $this->loadManageView(
            'translation.list',
            [
                'translations' => $processReaction['data']['translations'],
                'languageId' => $languageId,
                'languageInfo' => $processReaction['data']['languageInfo']
            ]
        );
    }

    /**
     * Scan for strings
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function scan($languageId, $preventReload = false)
    {
        $processReaction = $this->translationEngine->scan($languageId);

        if ($preventReload) {
            return $processReaction;
        }

        //check reaction code equal to 1
        if ($processReaction['reaction_code'] === 1) {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true),
                $this->redirectTo('manage.translations.lists', [
                    'languageId' => $languageId
                ])
            );
        } else {
            return $this->responseAction(
                $this->processResponse($processReaction, [], [], true)
            );
        }
    }

    /**
     * Update Translate
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function update(TranslationUpdateRequest $request)
    {
        return $this->processResponse(
            $this->translationEngine->update($request->all()),
            [],
            [],
            true
        );
    }

    /**
     * export
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function export($languageId)
    {
        return $this->translationEngine->exportToExcel($languageId);
    }

    /**
     * export
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function import(CommonUnsecuredPostRequest $request, $languageId)
    {
        $processReaction = $this->translationEngine->importExcel($request->all(), $languageId);

        return $this->processResponse($processReaction, [], [], true);
    }
}
