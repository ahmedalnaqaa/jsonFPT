<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ContactService;
use App\Models\Resource;
use Validator;

class ResourceController extends Controller
{
    /**
     * @var ContactService
     */
    private $contactService;

    /**
     * ResourceController constructor.
     * @param ContactService $contactService
     */
    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * Get resources
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Resource::paginate(), 200);
    }

    /**
     * Get resource
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $poll = Resource::findOrFail($id);
        return response()->json($poll, 200);
    }

    /**
     * Upload new resource
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadSource(Request $request)
    {
        $rules = [
            'file' => 'required|mimes:json,txt',
            'language' => 'required|regex:/^[a-zA-Z]+$/u|size:2'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $contactSource = $this->contactService->storeContactsResource(
            $request->file('file')->getClientOriginalName(),
            $request->file('file')->getPathname(),
            $request->file('file')->getSize(),
            $request->get('language'));

        return response()->json($contactSource, 201);
    }

    /**
     * Get resource contacts
     *
     * @param Request $request
     * @param Resource $resource
     * @return JsonResponse
     */
    public function resourceContacts(Request$request, Resource $resource)
    {
        return response()->json([
            'resource' => $resource,
            'contacts' => Contact::where('resource_id', $resource->id)->paginate()
        ], 200);
    }
}
