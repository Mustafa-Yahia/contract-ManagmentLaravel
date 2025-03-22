<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => [
                'required',
                'string',
                'min:8', // كلمة المرور لا تقل عن 8 أحرف
                'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/', // تحتوي على حرف ورقم على الأقل
                'confirmed',
            ],
            'phone' => 'required|digits:10', // رقم الهاتف يجب أن يكون 10 أرقام فقط
            'address' => 'required|string|max:255',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);


        // معالجة الصورة إذا وُجدت
        if ($request->hasFile('img')) {
            $imagePath = $request->file('img')->store('profile_images', 'public');
        } else {
            $imagePath = null; // أو ضع صورة افتراضية
        }

        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'img' => $imagePath,
            'role' => 'user',
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    // تأكد من أن الـ role يتم إرساله في الاستجابة
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,  
        ]
    ]);
}


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
