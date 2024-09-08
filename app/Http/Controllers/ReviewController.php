<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Restaurant $restaurant)
    {

        if (Auth::user()->subscribed('premium_plan')) {
            $reviews = $restaurant->reviews()->orderBy('created_at', 'desc')->paginate(5);
        } else {
            $reviews = $restaurant->reviews()->orderBy('created_at', 'desc')->paginate(5)->take(3);
        }
      return view('reviews.index',compact('restaurant','reviews'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant)
    {

        return view('reviews.create',compact('restaurant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Restaurant $restaurant)
    {
        $request->validate([
            'score' => ['required','numeric','digits_between:1,5'],
            'content' => ['required']
        ]);
 
        $review = new Review();
        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->restaurant_id = $restaurant->id;
        $review->user_id = Auth::user()->id;
        $review->save();

        return redirect()->route('restaurants.reviews.index',$restaurant)->with('flash_message','レビューを投稿しました。');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Restaurant $restaurant,Review $review)
    {
        // ログインユーザーがレビューの所有者であることを確認
        if($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index',$restaurant)->with('error_message','不正なアクセスです。');
        }
        
        return view('reviews.edit',compact('restaurant','review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Restaurant $restaurant,Review $review)
    {
        $request->validate([
            'score' => ['required','numeric','digits_between:1,5'],
            'content' => ['required']
        ]);

        if($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index',$restaurant)->with('error_message','不正なアクセスです。');
        } else {

        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->restaurant_id = $restaurant->id;
        $review->user_id = Auth::user()->id;
        $review->save();

        return redirect()->route('restaurants.reviews.index',$restaurant)->with('flash_message','レビューを編集しました。');
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant,Review $review)
    {
        if($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index',$restaurant)->with('error_message','不正なアクセスです。');
        }
        $review->delete();

        return redirect()->route('restaurants.reviews.index',$restaurant)->with('flash_message','レビューを削除しました。');


    }
}
