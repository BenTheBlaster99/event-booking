<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Load Sanctum routes
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set'])->cookie('XSRF-TOKEN', csrf_token());
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test-route', function () {
    return response()->json(['message' => 'API is working :D']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::post('/categories', [AppController::class, 'createCategory']);
Route::get('/categories', [AppController::class, 'getCategories']);
Route::get('/events/random', [AppController::class, 'getRandomEvents']);
Route::get('/events/search', [AppController::class, 'searchEvents']);
Route::get('/events/with-location', [AppController::class, 'getEventsWithLocation']);
Route::get('/allEvents', [AppController::class, 'getAllEvents']); //new one
Route::get('/events/upcoming', [AppController::class, 'getUpcomingEvents']);
Route::get('/events/{id}', [AppController::class, 'getEventId']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/events', [AppController::class, 'createEvent']);
    Route::put('/events/{id}', [AppController::class, 'updateEvent']);
    Route::delete('/events/{id}', [AppController::class, 'deleteEvent']);
    Route::post('/events/{event}/bookmark', [AppController::class, 'toggleBookmark']);
    Route::get('/bookmarks', [AppController::class, 'getBookmarks']);

    Route::get('/user/events', [AppController::class, 'getUserEvents']);
    Route::get('/user/reviews', [AppController::class, 'getUserReview']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile/settings', [AppController::class, 'updateSettings']);
    Route::get('/profile', [AppController::class, 'getProfile']);
    Route::put('/profile', [AppController::class, 'updateProfile']);
    //update avatar image
    Route::post('/profile/avatar', [AppController::class, 'updateAvatar']);

    Route::post('/events/{event}/reviews', [AppController::class, 'addReview']);
    Route::delete('/reviews/{review}', [AppController::class, 'deleteReview']);
});
Route::get('/ping', function () {
    return response()->json(['message' => 'API is working']);
});

// Debug route for events (no auth)
Route::get('/debug/events', function () {
    return response()->json([
        'total_events' => Event::count(),
        'upcoming_events' => Event::where('start_time', '>=', now())->get()
    ]);
});
