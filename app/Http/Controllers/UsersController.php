<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',[
          'except' => ['show','create','store','index','confirmEmail']
        ]);
        $this->middleware('guest', [
            'only' => ['create']
        ]);

    }
    public function create()
    {
        return view('users.create');
    }
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
              'name' => $request->name,
              'email' => $request->email,
              'password' => bcrypt($request->password),
          ]);
          //Auth::login($user);
          //session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
          $this->sendEmailConfirmationTo($user);
          ession()->flash('success', 'an email has sent to your mailbox');
          //return redirect()->route('users.show', [$user]);
          return redirect('/');
    }
    public function edit(User $user)
    {
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }
    public function update(User $user,Request $request)
    {
        $this->validate($request,[
          'name' => 'required|max:50',
            'password' => 'required|confirmed|min:6'
        ]);
        $this->authorize('update',$user);
        $data = [];
        $data['name'] = $request->name;
        if($request->password)
        {
          $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success','update ok');
        return redirect()->route('users.show',$user->id);
    }
    public function index()
    {
      $users = User::paginate(10);
      return view('users.index',compact('users'));
    }
      public function destroy(User $user)
      {
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','delete ok');
        return back();
      }

      protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'aufree@yousails.com';
        $name = 'Aufree';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }



}
