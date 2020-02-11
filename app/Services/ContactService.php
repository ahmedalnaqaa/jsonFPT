<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Resource;

class ContactService
{

    /**
     * @var
     */
    private $gTranslateService;

    public function __construct(GoogleTranslatorService $gTranslateService)
    {
        $this->gTranslateService = $gTranslateService;
    }

    /**
     * Store contacts resource
     *
     * @param $fileName
     * @param $filePath
     * @param $fileSize
     * @param $language
     * @return mixed
     */
    public function storeContactsResource($fileName, $filePath, $fileSize, $language)
    {
        $resource = Resource::create([
            'file' => $fileName,
            'language' => $language
        ]);
        $arr = [];
        $index = 0;
        $batchSize = $this->getBatchSize($fileSize);
        $contactsStream = \JsonMachine\JsonMachine::fromFile($filePath);
        foreach ($contactsStream as $name => $data) {
            $names = json_decode($data['names'], true);
            $hits = json_decode($data['hits'], true);
             foreach ($names as $key => $name) {
                 if ($name != null) {
                     $arr[] = [
                         'name' => $name,
                         'hits' => $hits[$key] ?: 0,
                         'resource_id' => $resource->id,
                         'translated' => false,
                         'translated_name' => null,
                         'created_at' => date('Y-m-d H:i:s'),
                         'updated_at' => date('Y-m-d H:i:s'),
                     ];
                 }
                 $index ++;
             }
            if (($index % $batchSize) === 0) {
                Contact::insert($arr);
                unset($arr);
            }
            ++$index;
        }
        return $resource;
    }

    /**
     * Set batch size for faster data insertion
     *
     * @param $fileSize
     * @return int
     */
    private function getBatchSize ($fileSize)
    {
        $batch = strlen((string)$fileSize);
        if ($batch <= 4){
            return 5;
        } else {
            return 5 * $batch;
        }
    }
}
