<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * getting user profile
     * 
     * @return Responsable
     */
    public function get()
    {
        return $this->success(Auth::user());
    }

    /**
     * updating user profile
     * 
     * @return Responsable
     */
    public function update(UserRequest $request)
    {
        $params = $request->safe()->except('file');
        $user = Auth::user();
        $path = false;

        if ($request->hasFile('file')) {
            $oldFile = $user->avatar;
            $result = $request->file('file')->store('avatars', 'public');
            $path = $result ? $request->file('file')->hashName() : false;
            $params['avatar'] = $path;
        }

        $user->update($params);

        if ($path and $oldFile) {
            Storage::disk('public')->delete($oldFile);
        }
        return $this->success($user->makeVisible('email'));
    }
}
