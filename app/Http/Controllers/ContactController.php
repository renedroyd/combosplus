<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function showForm()
    {
        return view('store.contact');
    }

    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Enviar correo o guardar en BD
        // Mail::to('admin@combosplus.com')->send(new ContactMailable($request->all()));

        return back()->with('success', 'Mensaje enviado correctamente. Te contactaremos a la brevedad.');
    }
}
