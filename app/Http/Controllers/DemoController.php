<?php

namespace App\Http\Controllers;

use App\Mail\DemoRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DemoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'demo_name'     => 'required|string|max:255',
            'demo_business' => 'required|string|max:255',
            'demo_phone'    => 'required|string|max:50',
            'demo_email'    => 'required|email|max:255',
            'demo_terms'    => 'accepted',
        ], [
            'demo_terms.accepted' => 'You must accept the Terms & Conditions to proceed.',
        ]);

        Mail::to('information@victorninn.com')->send(new DemoRequestMail(
            demoName:     $data['demo_name'],
            demoBusiness: $data['demo_business'],
            demoPhone:    $data['demo_phone'],
            demoEmail:    $data['demo_email'],
        ));

        return redirect()->route('login')
            ->with('demo_success', "Thanks {$data['demo_name']}! We'll reach out to you soon.");
    }
}
