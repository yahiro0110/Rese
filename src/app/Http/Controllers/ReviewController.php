<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // リクエストされたユーザーIDと店舗IDが既にレビューテーブルに存在するかをチェック
        $exists = Review::where('user_id', Auth::id())
            ->where('restaurant_id', $request->restaurant_id)
            ->exists();

        if ($exists) {
            // 既にレビューが存在する場合、エラーメッセージとともにリダイレクトする
            return to_route('home')->with([
                'message' => '既にこの店舗に対するレビューを投稿しています。',
                'status' => 'warning',
            ]);
        }

        if ($request->file('file')) {
            // 新しいファイル名を生成し、ファイルを保存
            $file_name = date('Ymd') . Str::random(15) . '_' . $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('public/images/review', $file_name);
        } else {
            $file_name = null;
        }

        try {
            $review = Review::create([
                'user_id' => Auth::id(),
                'restaurant_id' => $request->restaurant_id,
                'rating' => $request->rating,
                'title' => $request->title,
                'comment' => $request->comment,
            ]);

            if ($file_name) {
                $review->reviewImages()->create([
                    'image_path' => $file_name,
                ]);
            }

            return to_route('home')->with([
                'message' => '口コミを投稿しました。',
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'message' => '口コミの投稿に失敗しました。',
                'status' => 'error',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
