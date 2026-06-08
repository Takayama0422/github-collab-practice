<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // パスワードハッシュ化に必要

class UserController extends Controller
{
    /**
     * ユーザー一覧を表示
     */
    public function index()
    {
        // 大量データに備え、全件取得(all)からペジネーションに変更（任意ですが推奨）
        $users = User::paginate(15); 
        
        return view('users.index', compact('users'));
    }

    /**
     * 新しいユーザーを登録
     */
    public function store(Request $request)
    {
        // 1. バリデーションの実施
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'], // 実務では 'confirmed'（確認用入力のチェック）もよく使います
        ]);

        // 2. パスワードのハッシュ化
        $validated['password'] = Hash::make($validated['password']);

        // 3. データの安全な一括保存（※事前にUserモデル側で $fillable の設定が必要です）
        User::create($validated);

        // 4. 名前付きルートを使用したリダイレクト（フラッシュメッセージ付き）
        return redirect()->route('users.index')->with('success', 'ユーザーを登録しました。');
    }
}
