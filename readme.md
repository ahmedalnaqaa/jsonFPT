## Requirements
- php 7, apache2, mysql 5.7
- Composer
- Git

## Installation
- Clone the project.
- Go to project directory and run `composer install`
- Create mysql database (ex:`json_fpt`) and configure it in the `.env` file.
- Make sure you have the right `.env` parameters configuration especially `GOOGLE_TRANSLATE_API_URL` and `GOOGLE_API_KEY`
- Run `php artisan migrate` to migrate the schema.
- Run `php artisan serve` to start the server.

## Endpoints
- Get resources

  GET `api/resources` | Return resources (paginated)
- Post resources

  POST `api/resources` | create and store resource | request body = ['file' => 'filepath', 'language' => 'languageToTranslate']
  
- Get resource

  GET `api/resource/{id}` | get resource | `id` = the resource id

- Get resource contacts

  GET `api/resource/{resource}/contacts` | get resource contacts | `resource` = the resource id

- Get contacts

  GET `api/contacts` | get contacts 

- Get contact

  GET `api/contacts/{id}` | get contact | `id` = the contact id
  
## How it works
1- Go and create resource through choosing contacts json file to upload and the language you want to translate.
2- In Console run command `php artisan contacts:translate --limit={limit}` to loop on bulk of uploaded contacts and translate them.
```ssh
NOTE: 
- We can sechdule this command to run every second for example.
```

##Why that solution
* Storing and translating file at the same time is a very bad choice especially when you upload large file.
* Translating all contacts at the same time will let `Goole API` to block the request because it has a limitation in number of requests per second and the size of the text you want translate.
* This solution will provide a good performance for most of the large files.

##Postman collection for a demo
You can import this collection [https://www.getpostman.com/collections/9648a355413ebb8ed8b4] into your postman account and run a demo for the project.
