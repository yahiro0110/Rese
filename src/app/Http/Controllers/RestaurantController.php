<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
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
        return Inertia::render(
            'Home',
            [
                'restaurants' => Restaurant::with('genre', 'prefecture')->get(),
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
                'restaurants' => Restaurant::ofUser($userId)->get(),
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRestaurantRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRestaurantRequest $request)
    {
        //
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
        $restaurant->update([
            'name' => $request->name,
            'tel' => $request->tel,
            'email' => $request->email,
            'postal' => $request->postal,
            'address' => $request->address,
            'description' => $request->description,
            'restaurant_image' => $request->restaurant_image,
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
        //
    }
}
