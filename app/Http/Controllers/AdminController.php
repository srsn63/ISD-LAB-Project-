<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Simple pagination of users; if none, pass empty collection
        $users = User::query()
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin', compact('users'));
    }
}
