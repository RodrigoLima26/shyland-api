<?php


namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class ContactController
 * @package App\Http\Controllers
 */
class ContactController extends Controller {

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function addContact(Request $request) {

        $contact = new Contact();

        $contact->store($request->all());

        return response($contact, 200);

    }

    /**
     * @param Request $request
     */
    public function listContacts(Request $request) {

        $type_read = @$request->type_read;

        $contacts = Contact::
        when($type_read == 'READ', function($query) {
            return $query->where('read', 1);
        })->
        when($type_read == 'NOT_READ', function($query) {
            return $query->where('read', 0);
        })->
        orderBy('created_at', 'desc')->paginate(20);

        return response($contacts, 200);
    }

    /**
     * @param Contact $contact
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function readContact(Contact $contact, Request $request) {

        $contact->read();

        return response($contact, 200);

    }
}
