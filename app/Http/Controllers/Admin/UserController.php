<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request) {

        $keyword = $request->input('keyword');

        $query = User::query();

        if(!empty($keyword)) {
            $query->where('name','LIKE',"%{$keyword}%")
            ->orwhere('kana','LIKE',"%{$keyword}%");
        }

        $users = User::paginate(15);

        $total = $users->total();

        return view('users.index',compact('users','total','keyword'));
    }

    public function show(User $user) {
        return view('admin.users.show',compact('user'));
    }
}
