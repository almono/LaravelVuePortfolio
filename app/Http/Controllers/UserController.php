<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;
use JWTAuth;
use Auth;
use Mail;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserFollower;
use App\Models\Log;
use App\Models\LogCategory;
use App\Models\LogType;
use App\Models\Request as Req;

class UserController extends Controller
{

    public function loginUser(Request $request)
    {
        $data = $request->validate([
            'userEmail' => 'required|email',
            'userPassword' => 'required'
        ]);

        $user = User::where('email', $request->userEmail)->first();

        if (!$user || !Hash::check($request->userPassword, $user->password)) {
            $categoryId = LogCategory::getLogCategoryId('login_error');
            $typeId = LogType::getLogTypeId('error');

            Log::addLogEntry([
                'admin_log' => 0,
                'user_id' => $user['id'],
                'log_category' => $categoryId,
                'log_type' => $typeId,
                'log_info' => ''
            ]);

            return response([
                'status' => 'error',
                'message' => 'invalidUserCredentials'
            ]);
        }

        if($user['is_active'] == '0') {
            return response([
                'status' => 'error',
                'message' => 'accountIsDisabled'
            ]);
        }

        $token = JWTAuth::customClaims(['user' => $user])->attempt(['email' =>  $request->userEmail, 'password' => $request->userPassword]);
        $categoryId = LogCategory::getLogCategoryId('login_success');
        $typeId = LogType::getLogTypeId('success');
        $user['role_name'] = UserRole::where('id', $user['role'])->value('role_name');

        Log::addLogEntry([
            'admin_log' => 0,
            'user_id' => $user['id'],
            'log_category' => $categoryId,
            'log_type' => $typeId,
            'log_info' => ''
        ]);

        $response = [
            'userData' => ['user' => $user, 'token' => $token],
            'status' => 'success',
            'message' => 'userLoginSuccess'
        ];

        return response($response, 200);
    }

    public function logout()
    {
        $this->guard()->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'logoutSuccess'
        ], 200);
    }

    public function authenticateUser(Request $request)
    {
        $user = User::where('email', $request->email)
                    ->where('id', $request->id)->first();

        if(!isset($user) || is_null($user)) {
            return response([
                'status' => 'error'
            ]);
        }

        $user['role_name'] = UserRole::where('id', $user['role'])->value('role_name');

        try {
            $token = $this->guard()->refresh();
        } catch (\Exception $e) {
            $response = [
                'status' => 'error'
            ];
            return response($response, 200);
        }

        //$token = $this->guard()->check();
        if(!isset($token) || is_null($token)) {
            $response = [
                'status' => 'error'
            ];
            return response($response, 200);
        }

        $response = [
            'userData' => ['user' => $user, 'token' => $token],
            'status' => 'success'
        ];


        return response($response, 200);
    }

    public function validateToken(Request $request)
    {
        $validation = $this->guard()->check();
        if($validation) {
            $token = $this->guard()->refresh();
            return response(['tokenValid' => true, 'token' => $token]);
        } else {
            return response(['tokenValid' => false]);
        }
    }

    public function getUserDashboard(Request $request)
    {
        $data = $request->all();
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];

        $userActivities = $this->getUserActivities($userData, $data);
        $lastLogin = Log::getUserLastLogin($userData->id);
        $lastLogin = Carbon::createFromFormat('Y-m-d H:i:s', $lastLogin)->format('Y-m-d H:i:s');
        $comments = [];

        $logCategories = LogCategory::select('id', 'name')->get()->toArray();
        $categoryList = [];

        if(isset($logCategories) && !is_null($logCategories)) {
            $categoryList[] = ['value' => NULL, 'text' => 'selectCategory'];
            foreach($logCategories as $logCat) {
                $categoryList[] = ['value' => $logCat['id'], 'text' => ucfirst(str_replace('_', ' ', $logCat['name']))];
            }
        }

        $followers = UserFollower::getUserFollowers($userData->id);
        $following = UserFollower::getUserFollows($userData->id);

        unset($userData->password);

        return response([
            'userActivities' => $userActivities['activities'],
            'lastLogin' => $lastLogin,
            'userComments' => $comments,
            'activityCount' => $userActivities['activityCount'],
            'activityPages' => $userActivities['activityPages'],
            'logCategories' => $categoryList,
            'followers' => $followers,
            'following' => $following,
            'userData' => $userData
        ]);
    }

    public function getDashboardActivities(Request $request) {
        $data = $request->all();
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];

        $userActivities = $this->getUserActivities($userData, $data);
        return response($userActivities, 200);
    }

    public function getUserActivities($userData, $data) {
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];

        $userActivities = Log::where('user_id', $userData->id);

        if($userData->role != 1) {
            $userActivities->where('admin_log', 0);
        }

        $userActivities->join('log_categories', 'log_categories.id', '=', 'logs.log_category')
            ->leftJoin('log_types', 'log_types.id', '=', 'logs.log_type')
            ->orderBy('created_at', 'DESC')
            ->select('logs.id', 'logs.log_category', 'logs.log_info', 'logs.created_at', 'log_categories.name as log_category_name', 'log_types.name as log_type_name');

        if(isset($data['filters']) && !is_null($data['filters'])) {
            $filters = $data['filters'];
            if(isset($filters['type']) && !is_null($filters['type'])) {
                $typeId = LogType::getLogTypeId($filters['type']);
                $userActivities->where('logs.log_type', $typeId);
            }
            if(isset($filters['category']) && !is_null($filters['category'])) {
                $userActivities->where('logs.log_category', $filters['category']);
            }
            if(isset($filters['dateFrom']) && !is_null($filters['dateFrom'])) {

            }
            if(isset($filters['dateTo']) && !is_null($filters['dateTo'])) {

            }
        }

        $activityCount = $userActivities->get()->count();

        if(isset($data['page']) && isset($data['perPage']) && !is_null($data['page']) && !is_null($data['perPage']) && $data['page'] != 1) {
            $userActivities = $userActivities->skip(($data['page'] - 1) * $data['perPage'])->take($data['perPage'])->get()->toArray();
        } else {
            $pageLimit = isset($data['perPage']) && !is_null($data['perPage']) ? $data['perPage'] : 10;
            $userActivities = $userActivities->limit($pageLimit)->get()->toArray();
        }

        $activityPages = ceil($activityCount / isset($data['perPage']) ? $data['perPage'] : 10);

        return [
            'activities' => $userActivities,
            'activityCount' => $activityCount,
            'activityPages' => $activityPages
        ];
    }

    public function updateUserInfo(Request $request) {
        $userData = $request->all();

        if(!isset($userData) || is_null($userData)) {
            $result['status'] = 'error';
            $result['message'] = 'userDataNotFound';
            return response($result, 200);
        }

        $currentUser = User::where('id', $userData['userId'])->first();
        $updateInfo = false;

        if($userData['email'] != $currentUser['email']) {
            // check if available
            $existingEmail = User::where('email', $userData['email'])->first();
            if(!is_null($existingEmail)) {
                $result['status'] = 'error';
                $result['message'] = 'emailNotAvailable';
                return response($result, 200);
            }
            $updateInfo = true;
            $currentUser['email'] = $userData['email'];
        }

        if(isset($userData['newPassword'])) {
            if(!isset($userData['oldPassword']) || !is_null($userData['oldPassword'])) {
                $result['status'] = 'error';
                $result['message'] = 'oldPasswordMissing';
                return response($result, 200);
            }

            if (!$currentUser || !Hash::check($userData['newPassword'], $currentUser['password'])) {
                $result['status'] = 'error';
                $result['message'] = 'invalidPasswordProvided';
                return response($result, 200);
            }

            $updateInfo = true;
            $currentUser['password'] = $userData['newPassword'];
        }

        if(isset($userData['isPrivate'])) {
            if($userData['isPrivate'] != $currentUser['is_private']) {
                $currentUser['is_private'] = $userData['isPrivate'];
                $updateInfo = true;
            }
        }

        if($updateInfo) {
            try {
                $currentUser->save();
            } catch (\Exception $e) {
                dd($e->getMessage());
            }

            $categoryId = LogCategory::getLogCategoryId('profile_update');
            $typeId = LogType::getLogTypeId('info');
            Log::addLogEntry([
                'admin_log' => 0,
                'user_id' => $currentUser['id'],
                'log_category' => $categoryId,
                'log_type' => $typeId,
                'log_info' => ''
            ]);

            $newUserData = User::where('id', $currentUser['id'])->first();
            $newUserData['role_name'] = UserRole::where('id', $newUserData['role'])->value('role_name');
            unset($newUserData['password']);

            $result['status'] = 'success';
            $result['message'] = 'profileInfoUpdated';
            $result['updatedUser'] = $newUserData;
        } else {
            $result['status'] = 'info';
            $result['message'] = 'noProfileInfoUpdated';
        }

        return response($result);
    }

    public function getUserStats(Request $request) {
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];

        $stats = User::getUserSocials($userData['id']);
    }

    public function viewProfile(Request $request) {
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];

        $userId = $request->userId;

        if(isset($userId) && !is_null($userId)) {
            $userProfile = User::where('id', $userId)->get()->first();
            if(is_null($userProfile)) {
                $result['status'] = 'error';
                $result['message'] = 'profileNotFound';
            } else {
                $userProfile['role_name'] = UserRole::where('id', $userId)->value('role_name');
                $result['isFollowed'] = UserFollower::isUserFollowed($userData->id, $userId);
                $result['status'] = 'success';
                $result['user'] = $userProfile;
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = 'profileNotFound';
        }

        return response($result);
    }

    public function toggleAccountStatus(Request $request) {
        $data = $request->all();

        if(isset($data['userId'])) {
            $token = \JWTAuth::getToken();
            $tokenData = \JWTAuth::getPayload($token);
            $userData = $tokenData['user'];

            if($userData['id'] == $data['userId']) {
                $currentUser = User::where('id', $userData['userId'])->first();
                $currentUser['is_private'] = $currentUser['is_private'] == 1 ? 0 : 1;

                $categoryId = LogCategory::getLogCategoryId('profile_visibility_change');
                $typeId = LogType::getLogTypeId('success');
                Log::addLogEntry([
                    'admin_log' => 0,
                    'user_id' => $currentUser['id'],
                    'log_category' => $categoryId,
                    'log_type' => $typeId,
                    'log_info' => ''
                ]);

                try {
                    $currentUser->save();
                } catch (\Exception $e) {
                    dd($e->getMessage());
                }

                $result['status'] = 'success';
                $result['message'] = 'accountStatusChanged';
            } else {
                $result['status'] = 'error';
                $result['message'] = 'wrongUserProvided';
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = 'noUserProvided';
        }

        return response($result);
    }

    public function followUser(Request $request) {
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];
        $data = $request->all();
        $result['status'] = 'error';

        if(!isset($data['userId']) || is_null($data['userId'])) {
            $result['message'] = 'invalidUserProvided';
            return response($result);
        }

        try {

            $follow = new UserFollower();
            $follow->user_id = $data['userId'];
            $follow->follower_id = $userData->id;
            $follow->follow_date = date('Y-m-d H:i:s');
            $follow->save();

            $result['status'] = 'success';
            $result['message'] = 'userFollowedSuccessfully';
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return response($result);
    }

    public function unfollowUser(Request $request) {
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];
        $data = $request->all();
        $result['status'] = 'error';

        if(!isset($data['userId']) || is_null($data['userId'])) {
            $result['message'] = 'invalidUserProvided';
            return response($result);
        }

        try {
            UserFollower::where([
                ['follower_id', $userData->id],
                ['user_id', $data['userId']]
            ])->delete();

            $result['status'] = 'success';
            $result['message'] = 'userUnfollowedSuccessfully';
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return response($result);
    }

    public function followCryptoCoin(Request $request) {
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $data = $request->all();
        $result['status'] = 'error';

        if(!isset($data['userId']) || is_null($data['userId'])) {
            $result['message'] = 'invalidUserProvided';
            return response($result);
        }
    }

    public function forgotPassword(Request $request) {
        $data = $request->all();
        $result['status'] = 'error';

        if(!isset($data['userEmail']) || !filter_var($data['userEmail'], FILTER_VALIDATE_EMAIL)) {
            $result['message'] = 'invalidEmailFormat';
            return response($result);
        }

        $user = User::getUserByEmail($data['userEmail']);

        if(!isset($user) || is_null($user)) {
            $result['message'] = 'invalidUserEmail';
            return response($result);
        }

        $resetToken = Req::createRequest('forgot_password', $user['id']);

        if($resetToken) {
            try {
                $resetUrl = getenv('BASE_URL') . "/reset_password?user=" . $user['id'] . "&token=" . $resetToken->request_token;

                Mail::send('emails.forgotPassword', ['resetUrl' => $resetUrl], function($message) use ($user)
                {
                    $message->to($user['email']);
                    $message->subject('Password Reset Request for almono.info');
                });

                $result['status'] = 'success';
                $result['message'] = 'forgotPasswordMailSuccess';
            } catch (\Exception $e) {
                $result['message'] = 'forgotPasswordMailError';
            }
        } else {
            $result['message'] = 'forgotPasswordMailError';
        }

        return response($result);
    }

    public function resetPassword(Request $request) {
        $userId = $request->userId;
        $token = $request->token;
        $result['status'] = 'error';

        if(!isset($userId) || is_null($userId)) {
            $result['message'] = 'invalidUserProvidedError';
            return response($result);
        }

        if(!isset($token) || is_null($token)) {
            $result['message'] = 'invalidTokenError';
            return response($result);
        }

        $user = User::find($userId);
        if(is_null($user)) {
            $result['message'] = 'invalidUserError';
            return response($result);
        }

        $resetToken = Req::where([
            ['request_type', 'forgot_password'],
            ['request_user', $user->id],
            ['request_token', $token],
            ['is_active', '1']
        ])->get()->first();

        if(is_null($resetToken)) {
            $result['message'] = 'tokenNotFoundError';
            return response($result);
        } else {

            try {
                Req::where('id', $resetToken['id'])->delete();
            } catch (\Exception $e) {
                $result['message'] = 'failedToUseToken';
                return response($result);
            }

            $result['status'] = 'success';
            $result['message'] = '';
            return response($result);
        }
    }
    
    public function setNewPassword(Request $request) {
        $data = $request->all();
        $result['status'] = 'error';

        if(!isset($data['newPassword']) || is_null($data['newPassword'])) {
            $result['message'] = 'providedPasswordIsInvalid';
            return response($result);
        }

        if(!isset($data['userId']) || is_null($data['userId'])) {
            $result['message'] = 'providedUserIsInvalid';
            return response($result);
        }

        $user = User::find($data['userId']);

        if(is_null($user)) {
            $result['message'] = 'userInvalidOrNotFoundError';
            return response($result);
        }

        try {
            $user->password = Hash::make($data['newPassword']);
            $user->save();
            $result['status'] = 'success';
            $result['message'] = 'passwordChangeSuccess';

            Mail::send('emails.passwordResetConfirmation', ['ip' => $request->ip()], function($message) use ($user)
            {
                $message->to($user->email);
                $message->subject('Password for ' . $user->email . ' has been reset');
            });
        } catch (\Exception $e) {
            $result['message'] = 'passwordChangeError';
        }

        return response($result);
    }

    private function guard()
    {
        return Auth::guard();
    }
}
