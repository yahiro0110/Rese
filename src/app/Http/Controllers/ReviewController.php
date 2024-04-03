<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
    public function update(Request $request, Review $review)
    {
        $reviewImages = $review->reviewImages;
        $file_name = $reviewImages->first()->image_path ?? null;

        // レビュー画像に関する更新処理
        if ($request->file('file')) {
            // 古いファイルが存在するか確認し、存在する場合は削除
            if (Storage::exists('public/images/review/' . $file_name)) {
                Storage::delete('public/images/review/' . $file_name);
            }

            // 新しいファイル名を生成し、ファイルを保存
            $file_name = date('Ymd') . Str::random(15) . '_' . $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('public/images/review/', $file_name);

            // Eloquentのsaveメソッドは、モデルのインスタンスに対して使用できるものであり、コレクションに対して直接使用することはできない
            // reviewImagesはコレクションオブジェクトのため、データベースに保存するためには、firstメソッドを使用して、最初のモデルインスタンスを取得する
            $firstReviewImage = $reviewImages->first();
            if ($firstReviewImage) {
                // レビュー画像が存在する場合、画像パスを更新
                $firstReviewImage->image_path = $file_name;
                $firstReviewImage->save();
            } else {
                // レビュー画像が存在しない場合、新しいレビュー画像を作成
                $review->reviewImages()->create([
                    'review_id' => $review->id,
                    'image_path' => $file_name,
                ]);
            }
        }

        // レビュー情報に関する更新処理
        $review->update($request->only(['rating', 'title', 'comment']));

        return to_route('home')->with([
            'message' => '口コミを更新しました。',
            'status' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {
        try {
            $review->delete();

            return to_route('home')->with([
                'message' => '口コミを削除しました。',
                'status' => 'danger',
            ]);
        } catch (\Exception $e) {
            return to_route('home')->with([
                'message' => '口コミの削除に失敗しました。',
                'status' => 'warning',
            ]);
        }
    }
}
