<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ContactService;
use App\Models\Resource;
use Validator;

class ContactController extends Controller
{
    /**
     * Get contacts
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Contact::paginate(), 200);
    }

    /**
     * Get contact
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return response()->json($contact, 200);
    }
}
