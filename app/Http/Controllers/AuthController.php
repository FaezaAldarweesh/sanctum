<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\loginRequest;
use App\Http\Requests\signupRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\signupResource;


class AuthController extends Controller
{

    //لحتى اقدر استخدم توابع ال trait
    use ApiResponseTrait;
    
    /*
    هلئ هوت هي طريقة تانية مثلا لحتى خلي تابع معين ما ينطلب إذا مافي تسجيل دخول أول
    أو بحطو عند الراوت هنيك للميدلويير عند الراوتات
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    */
//======================================================================================================================================
    public function sign_up(signupRequest $request){

        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        //The under code will create a token that will be sent along with every request to a protected route.
        $token = $user->createToken('apiToken')->plainTextToken;

        return $this->apiResponse(new signupResource($user),$token,"create account successfully - registered successfully",201);
        

    }
//======================================================================================================================================
    public function login(loginRequest $request)
    {
        
        $data = $request->validated();

        // Define a $user variable that contains the user with the given email.
        // Check if the $user is registered and return 'msg' => 'incorrect username or password' with a 401 status code if it isn't.

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response([
                'msg' => 'incorrect username or password'
            ], 401);
        }

        //The under code generates a token that will be used to log in.
        $token = $user->createToken('apiToken')->plainTextToken;
        
        return $this->apiResponse(null,$token,"login successfully",200);

    }

//======================================================================================================================================
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return ['message' => 'user logged out'];
    }


}
