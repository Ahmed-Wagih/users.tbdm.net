<?php
namespace App\Http\Controllers\Api\v1;
use App\User;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class UserController extends Controller
{
    use ApiResponser;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    // public function index()
    // {
    //     $users = User::paginate(5);
    //     return $this->successResponse($users);
    //
    // }
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($number = 20)
    {
        $users = User::orderBy('id', 'desc')->paginate($number);
        return $this->successResponse($users);

    }
    public function checkPhoneNumber($phone)
    {
        $users = User::where('phone',$phone)->count();
        return $users;

    }
    
    public function checkOtp($otp, $userId)
    {

        $users = User::where('verification_code',$otp)->count();
        $user = User::findOrFail($userId);
        $user->phone_verified_at = Carbon::now();
        $user->save();
        return $users;

    }
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVendors()
    {
        $vendors = User::where('type', 'vendor')->limit('10')->get();
        return $this->successResponse($vendors);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDoctors()
    {
        $doctors = User::where('type', 'doctor')->limit('10')->get();
        return $this->successResponse($doctors);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // return $request->all();
        $this->validate($request, [
            // 'image' => 'required|image|file|size:512',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'phone' => 'required|unique:users',
            'password' => 'required|min:8',
            // 'api_token' => 'required|max:255',
            'type' => 'required|in:user,vip,vendor,doctor,technician,dirver,developer',
            'chanel' => 'required|in:web,agent,mobile',
        ]);

        $verification_code = rand(111111, 999999);
        $api_token = md5(bin2hex(random_bytes(99)));

        $last_id = User::latest()->first();
        // $id = $last_id['id'] + 1;
        $username = str_replace(" ","_",trim(strtolower($request->first_name)))." ".str_replace(" ", "_",trim(strtolower($request->last_name)));

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $username,
            'phone' => $request->phone,
            'password' => md5($request->password),
            'api_token' => $api_token,
            'type' => $request->type,
            'chanel' => $request->chanel,
            'verification_code' => $verification_code,
        ]);
        return $this->successResponse($user, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        return $this->successResponse($user);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'phone' => 'required|unique:users,phone,'.$id,
            'password' => 'min:8',
        ]);
        
        $userInfo = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone
            ];
        if($request->password){
             $userInfo = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'password' => md5($request->password)
            ];
        }
        
        // return $userInfo;
        $user = User::findOrFail($id);
        $user->fill($userInfo);

        // if($user->isClean()){
        //     return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        // }
        $user->save();
        return $this->successResponse($user, Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required',
            'password' => 'required'
        ]);

        $password = md5($request->password);
        $phone = $request->phone;
        $user = User::where([
          ['phone', '=', $phone],
          ['password', '=', $password]
        ])->first();
        if($user != null){
          $user->api_token = md5(bin2hex(random_bytes(99)));
          $user->save();
          // return response()->json(['status' => 'success','data' => $user]);
          return $this->successResponse($user);
        }else{
          return response()->json(['status' => 'fail'],401);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
//        $product->forceDelete();
        return $this->successResponse($user);
    }
}
