<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;

class SessionsController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    public function create()
    {
        return view('sessions.create');
    }
    public function store(Request $request)
    {
        $credentials = $this->validate($request,[
          'email' => 'required|email|max:255',
           'password' => 'required'
        ]);
        if(Auth::attempt($credentials,$request->has('remember'))){
          if(Auth::user()->activated)
          {
            session()->flash('success','welcome back~');
            return redirect()->intended(route('users.show',[Auth::user()]));
          }
          else
          {
            Auth::logout();
            session()->flash('warning','your account is not activated');
            return redirect('/');
          }

        }
        else {
          session()->flash('danger','sorry wrong email or password~');
          return redirect()->back();
        }
        return ;
    }
    public function destroy()
    {
        Auth::logout();
        session()->flash('success','logout good');
        return redirect('login');
    }
}
