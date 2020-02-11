<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Services\GoogleTranslatorService;
use App\Models\Resource;
use Exception;
use Illuminate\Console\Command;
use Mavinoo\LaravelBatch\LaravelBatchFacade as Batch;

class TranslateContactsCommand extends Command
{
    /**
     * @var
     */
    private $gTranslateService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contacts:translate {--limit= : Number of records to be update in one command (max=1000)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate list of contacts';

    /**
     * Create a new command instance.
     *
     * @param GoogleTranslatorService $gTranslateService
     * @return void
     */
    public function __construct(GoogleTranslatorService $gTranslateService)
    {
        $this->gTranslateService = $gTranslateService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {
        $limit = $this->option('limit')?:500;
        if ($limit>1000) {
            $this->error($limit.' is out of limit. maximum is 1000');die();
        }
        $resource = Resource::where('translated', false)->first();
        if (!$resource){
            $this->error('Nothing to translate');
            return true;
        };
        $contacts = Contact::where('resource_id', $resource->id)
            ->where('translated', false)
            ->whereNotNull('name')->limit($limit)->get();
        if (count($contacts) < $limit) {
            $resource->translated = true;
            $resource->save();
        }
        if (count($contacts) == 0){
            $this->error('Nothing to translate');
            return true;
        };
        $this->alert('Translating '.$limit.' contact');
        $contactInstance = new Contact();
        $arr = array_values(array_filter($contacts->pluck('name')->toArray()));
        $this->info('Sending translation');
        $translations = $this->gTranslateService->translate($arr, $resource->language);
        $this->info('Received translation');

        foreach ($contacts as $key => $contact) {
            $this->line('Translating ' .$contact->name);
            $contact->hits = $contact->hits + 1;
            $contact->translated = true;
            if (array_key_exists($key, $translations)) {
                $contact->name = $translations[$key];
                $contact->translated_name = !in_array($key, $this->contactsDuplicates($arr)) ? $translations[$key] : null;
            }
            $this->info('Translated!');
            $this->line('________________________________________________________________________');
        }
        $this->info('storing translations');
        Batch::update($contactInstance, $contacts->toArray(), 'id');
        $this->alert('translation is done successfully!');
        return true;
    }

    /**
     * Extract duplicated contacts
     *
     * @param array $contactsArr
     * @return array
     */
    private function contactsDuplicates($contactsArr = [])
    {
        $uniqueValues = array_unique($contactsArr);
        $duplicates = array_diff_assoc($contactsArr, $uniqueValues);
        return array_keys($duplicates);
    }
}
