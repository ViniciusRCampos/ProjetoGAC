<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;
    public function __construct(UserService $userService){
        $this->userService = $userService;
    }
    public function index()
    {
        if(auth()->check()){
            return redirect()->route('home');
        }
        return view('login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(["username","password"]);
        
        if (auth()->attempt($credentials)) {
            return response()->json(['status' => 'success'], 200);
        }
    
        return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        return redirect()->route('login');
    }

    public function home(Request $request){
        if(auth()->user()){
            return view('home');
        }
        return redirect()->route('login');
    }

    public function register(RegisterRequest $request){
        $newUser = $this->userService->register($request);
        return $newUser;
    }

}
