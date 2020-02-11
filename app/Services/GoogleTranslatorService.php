<?php

namespace App\Services;

use Exception;
use Google\Cloud\Translate\V2\TranslateClient;

class GoogleTranslatorService
{
    /**
     * Translate text
     *
     * @param array $textArr
     * @param string $targetLanguage
     * @return mixed
     * @throws Exception
     */
    public function translate($textArr = [], $targetLanguage = 'ar')
    {
        $translatorClient = new TranslateClient([
            'key' => env('GOOGLE_API_KEY', null)
        ]);
        $translations = [];
        $textArrChunk = array_chunk($textArr, 100);
        foreach ($textArrChunk as $text) {
            try{
                $result = $translatorClient->translateBatch($text, [
                    'target' => $targetLanguage,
                ]);
                $translations[] = array_column($result, 'text');
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
        return call_user_func_array('array_merge', $translations);
    }

    /**
     * Generate translation URL query
     *
     * @param array $parameters
     * @return string
     */
    private function generateTranslationURL($parameters = array())
    {
        $parameters = array_merge(
            ['key' => env('GOOGLE_API_KEY')]
            ,$parameters
        );
        return env('GOOGLE_TRANSLATE_API_URL') . http_build_query($parameters);
    }
}
