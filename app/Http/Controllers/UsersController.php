<?php

namespace App\Http\Controllers;

require_once app_path('Common/Constants.php');

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|Unique:users,email|max:255',
            'mobile' => 'required|string|Unique:users,mobile|max:10',
            'password' => USER_DEFAULT_PASSWORD,
            'role' => 'in:admin,user',
            'status' => 'in:-1,0,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 402]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'status' => USER_DEFAULT_STATUS,
            'role' => $request->role,
            'password' => Hash::make(USER_DEFAULT_PASSWORD)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User Created Successfully'
        ], 201);
    }

    public function changeUserRole(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'role' => 'required|in:admin,user'
        ]);

        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully'
        ], 200);
    }

    public function deleteUser($id)
    {
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required'
            ], 400);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($user->status == USER_STATUS_VALUES['Deleted']) {
            return response()->json([
                'success' => false,
                'message' => 'User is Already Deleted!'
            ], 400);
        }

        $user->status = USER_STATUS_VALUES['Deleted'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User Deleted Successfully'
        ], 200);
    }

    public function disableUser($id)
    {
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required'
            ], 400);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($user->status == USER_STATUS_VALUES['Disabled']) {
            return response()->json([
                'success' => false,
                'message' => 'User is Already Disabled!'
            ], 400);
        }

        $user->status = USER_STATUS_VALUES['Disabled'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User Disabled Successfully'
        ], 200);
    }

    public function enableUSer($id)
    {
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required'
            ], 400);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($user->status == USER_STATUS_VALUES['Active']) {
            return response()->json([
                'success' => false,
                'message' => 'User is Already Active!'
            ], 400);
        }

        $user->status = USER_STATUS_VALUES['Active'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User Enabled Successfully'
        ], 200);
    }

    public function updateUserDetails(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|string|max:15',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User Details Updated'
        ], 200);
    }


    public function getUserDetails($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function getUsers(Request $request)
    {
        $request->validate([
            'status' => 'in:-1,0,1',
            'role' => 'in:admin,user',
        ]);

        $query = User::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => 'No users found with the given filters.'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'count' => $users->count(),
            'users' => $users
        ], 200);
    }

    public function getUsersStatusCounts(Request $request)
    {
        if (!$request->filled('status')) {
            $statusMap = [
                getStatusValue('user', 'Active')   => 'active',
                getStatusValue('user', 'Disabled') => 'disabled',
                getStatusValue('user', 'Deleted')  => 'deleted',
            ];

            // Get counts for each status
            $statusCounts = \App\Models\User::selectRaw('status, COUNT(*) as count')
                ->whereIn('status', array_keys($statusMap))
                ->groupBy('status')
                ->pluck('count', 'status');

            // Fill missing statuses with 0
            $counts = [];
            foreach ($statusMap as $statusValue => $label) {
                $counts[$label] = $statusCounts->get($statusValue, 0);
            }

            // Add 'all' key as total users matching these statuses
            $counts['all'] = array_sum($counts);

            return response()->json([
                'success' => true,
                'status_counts' => $counts,
            ]);
        }

        $count = \App\Models\User::where('status', $request->status)->count();
        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function userDashboard(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid Token'], 401);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token Expired'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Welcome ' . $user->name . '.' . PHP_EOL .
                ' This is your dashboard'
        ], 200);
    }

    public function adminDashboard(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            if ($user->role != 'admin') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid Token'], 401);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token Expired'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Welcome ' . $user->name . '.' . PHP_EOL .
                ' This is your dashboard'
        ], 200);
    }
}
