<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Models\Genre;
use App\Models\Prefecture;
use App\Models\Restaurant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RestaurantController extends Controller
{
    /**
     * Display the home page listing of restaurants.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        $userId = Auth::id();
        // 全ての店舗情報を取得し、
        // 各店舗に対して、現在のユーザーと紐づいているかどうかの情報も取得
        $restaurants = Restaurant::with('genre', 'prefecture', 'reviews.reviewImages', 'reviews.user')
            ->withUserAttached($userId)
            ->get();
        return Inertia::render(
            'Home',
            [
                'restaurants' => $restaurants,
                'genres' => Genre::select('id', 'name')->get(),
                'prefectures' => Prefecture::select('id', 'name')->get(),
            ]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 現在ログインしているユーザーのIDを取得
        $userId = Auth::id();

        return Inertia::render(
            'Restaurants/Index',
            [
                'restaurants' => Restaurant::with('schedules.user')->ofUser($userId)->get(),
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render(
            'Restaurants/Create',
            [
                'genres' => Genre::select('id', 'name')->get(),
                'prefectures' => Prefecture::select('id', 'name')->get(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRestaurantRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRestaurantRequest $request)
    {
        if ($request->file('file')) {
            // 新しいファイル名を生成し、ファイルを保存
            $file_name = date('Ymd') . Str::random(15) . '_' . $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('public/images', $file_name);
        } else {
            $file_name = null;
        }

        Restaurant::create([
            'name' => $request->name,
            'tel' => $request->tel,
            'email' => $request->email,
            'postal' => $request->postal,
            'address' => $request->address,
            'description' => $request->description,
            'restaurant_image' => $file_name,
            'prefecture_id' => $request->prefecture_id,
            'genre_id' => $request->genre_id,
            'user_id' => Auth::id(),
        ]);

        return to_route('restaurants.index')->with([
            'message' => '店舗情報を登録しました。',
            'status' => 'success',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function show(Restaurant $restaurant)
    {
        return Inertia::render(
            'Restaurants/Show',
            [
                'restaurant' => $restaurant,
                'genres' => Genre::select('id', 'name')->get(),
                'prefectures' => Prefecture::select('id', 'name')->get(),
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function edit(Restaurant $restaurant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRestaurantRequest  $request
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
    {
        $file_name = $restaurant->restaurant_image;
        if ($request->file('file')) {
            // 古いファイルが存在するか確認し、存在する場合は削除
            if (Storage::exists('public/images/' . $file_name)) {
                Storage::delete('public/images/' . $file_name);
            }
            // 新しいファイル名を生成し、ファイルを保存
            $file_name = date('Ymd') . Str::random(15) . '_' . $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('public/images', $file_name);
        }
        $restaurant->update([
            'name' => $request->name,
            'tel' => $request->tel,
            'email' => $request->email,
            'postal' => $request->postal,
            'address' => $request->address,
            'description' => $request->description,
            'restaurant_image' => $file_name,
            'prefecture_id' => $request->prefecture_id,
            'genre_id' => $request->genre_id,
        ]);

        return to_route('restaurants.index', compact('restaurant'))->with([
            'message' => '店舗情報を更新しました。',
            'status' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restaurant $restaurant)
    {
        try {
            $restaurant->delete();
            // 古いファイルが存在するか確認し、存在する場合は削除
            if ($restaurant->restaurant_image && Storage::exists('public/images/' . $restaurant->restaurant_image)) {
                Storage::delete('public/images/' . $restaurant->restaurant_image);
            }

            return to_route('restaurants.index')->with([
                'message' => '店舗を削除しました。',
                'status' => 'danger',
            ]);
        } catch (\Exception $e) {
            return to_route('restaurants.index')->with([
                'message' => '店舗の削除に失敗しました。',
                'status' => 'warning',
            ]);
        }
    }

    /**
     * 指定された店舗を現在認証されているユーザーのお気に入りに登録する。
     *
     * 登録処理中に例外が発生した場合は、ホームページへリダイレクトし、
     * エラーメッセージを含む警告通知を表示する。
     *
     * @param Restaurant $restaurant お気に入りに登録する店舗のインスタンス
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector 登録に失敗した場合のリダイレクトレスポンス
     */
    public function attachFavorite(Restaurant $restaurant)
    {
        try {
            $restaurant->users()->attach(Auth::id());
            return to_route('home');
        } catch (\Exception $e) {
            return to_route('home')->with([
                'message' => 'お気に入り登録に失敗しました。',
                'status' => 'warning',
            ]);
        }
    }

    /**
     * 指定された店舗を現在認証されているユーザーのお気に入りリストから削除する。
     *
     * 削除処理中に例外が発生した場合は、ホームページへリダイレクトし、
     * エラーメッセージを含む警告通知を表示する。
     *
     * @param Restaurant $restaurant お気に入りから削除する店舗のインスタンス
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector 削除に失敗した場合のリダイレクトレスポンス
     */
    public function detachFavorite(Restaurant $restaurant)
    {
        try {
            $restaurant->users()->detach(Auth::id());
            return to_route('home');
        } catch (\Exception $e) {
            return to_route('home')->with([
                'message' => 'お気に入りからの削除に失敗しました。',
                'status' => 'warning',
            ]);
        }
    }
}
