<?php

namespace App\Http\Controllers;

use App\Models\Profiles;
use Illuminate\Http\Request;

class UserSettingsController extends Controller
{
    //

    public function addDisplayName(Request $request) {
        $userId = $request->uid;
        $displayName = $request->displayName;

        Profiles::where('user_id', $userId)->update(['display_name' => $displayName]);

        return response()->json(['success' => true, 'message' => 'Display name updated successfully.', 'display_name' => $displayName], 200);
    }
}
