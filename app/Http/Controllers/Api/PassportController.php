<?php


namespace App\Http\Controllers\API;

use App\Models\Role;
use App\Models\UserMeta;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\UserInviteRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Exception;
use Cookie;

class PassportController extends Controller
{

    public $successStatus = 200;

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['user'] =  $user;
            return response()->json(['success' => $success], $this->successStatus);
        }
        else {
            return response()->json(['error'=>'Incorrect username or password'], 401);
        }
    }


    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'c_password' => 'required|same:password',
        ], ['c_password.same' => 'Password confirmation should match the password']);


        if ($validator->fails()) {
            return response()->json(['error'=>$validator->messages()], 401);
        }

        $input = $request->all();
        $input['name'] = $input['first_name'] . " " . $input['last_name'];
        $input['password'] = bcrypt($input['password']);
        $password =  $input['password'];

        $user = User::create($input);
        $userMeta = new UserMeta();
        $role = new Role();
        $input['user_id'] = $user->id;

        // create user meta data and assign roles
        $userService = new UserService($user, $userMeta, $role);
        $userService->createUserAssociatedData($input, $user, $password, 'cao', false);

        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user'] =  User::with('meta', 'roles', 'company')->find($user->id);

        return response()->json(['success'=>$success], $this->successStatus);
    }




    // this function sets the password for newly promoted users who didn't have set passwords previously
    public function setPassword(Request $request) {

        $validator = Validator::make($request->all(), [
            'activation_token' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->messages()], 401);
        }

        $activation_token = $request->get('activation_token');
        $password = $request->get('password');

        $userMeta = UserMeta::where('activation_token', $activation_token)->first();

        if(!empty($userMeta)) {
            $user = User::find($userMeta->user_id);
            $user->password = bcrypt($password);
            $user->save();

            // log user in automatically
            if(Auth::attempt(['email' => $user->email, 'password' => $password])){
                $user = Auth::user();
                $success['token'] =  $user->createToken('MyApp')->accessToken;
                return response()->json(['success' => $success], $this->successStatus);
            }
            else {
                return response()->json(['error'=>'Unauthorised'], 401);
            }

        } else {
            return response()->json(['error'=>'Unauthorised'], 401);
        }

    }

}