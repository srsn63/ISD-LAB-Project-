<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required','string','max:120'],
            'email'   => ['required','email','max:180'],
            'subject' => ['nullable','string','max:180'],
            'message' => ['required','string','max:5000'],
        ]);

        ContactMessage::create($data);

        return redirect()->route('contact')->with('success', 'Message sent successfully. We will get back to you soon.');
    }
}
