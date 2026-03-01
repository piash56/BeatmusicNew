<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;

class AdminNewsletterController extends Controller
{
    public function index()
    {
        $subscribers = NewsletterSubscriber::orderByDesc('created_at')->paginate(25);
        return view('admin.newsletter.index', compact('subscribers'));
    }
}

