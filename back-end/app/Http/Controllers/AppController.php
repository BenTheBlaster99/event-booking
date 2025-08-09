<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Review;;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Str;

class AppController extends Controller

{

    public function createEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            unset($validatedData['image']);
        }

        $event = Event::create(array_merge(
            $validatedData,
            [
                'user_id' => Auth::id(),
                'image_url' => $imagePath,
            ]
        ));

        return response()->json($event, 201);
    }
    public function getEvents(Request $request)
    {

        $query = Event::with('category', 'user')->orderBy('start_time', 'asc');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('start_date')) {
            $query->where('start_time', '>=', $request->start_date);
        }
        $events = $query->paginate(10);
        return response()->json([
            'message' => 'Events retrieved successfully',
            'data' => $events
        ], 200);
    }
    public function getEventId($id)
    {
        $event = Event::with('category', 'user')->findOrFail($id);
        return response()->json([
            'message' => 'Event retrieved successfully',
            'data' => $event
        ], 200);
    }
    public function getUpcomingEvents()
    {
        $events = Event::with('category', 'user')
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(4)
            ->get();

        return response()->json([
            'message' => 'Upcoming events retrieved',
            'data' => [
                'data' => $events // Maintains your data.data structure
            ]
        ], 200); // Return 200 even if empty
    }
    public function updateEvent(Request $request, Event $event)
    {

        if ($event->user_id !== Auth::id()) {
            abort(403, 'You can only update your own events.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'location' => 'sometimes|string',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'category_id' => 'sometimes|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();


        if ($request->hasFile('image')) {

            if ($event->image_url) {
                Storage::disk('public')->delete($event->image_url);
            }

            $imagePath = $request->file('image')->store('events', 'public');
            $validatedData['image_url'] = $imagePath;
            unset($validatedData['image']);
        } else {

            if ($event->image_url) {
                $validatedData['image_url'] = $event->image_url;
            }
        }


        $event->update($validatedData);

        return response()->json($event);
    }
    public function deleteEvent($id)
    {
        $event = Event::where('user_id', Auth::id())->findOrFail($id);
        $event->delete();
        return response()->json([
            'message' => 'Even deleted successfully'
        ]);
    }
    public function createCategory(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);
    }

    public function getCategories()
    {
        $categories = Category::all();
        return response()->json([
            'message' => 'Categories retieved successfully',
            'data' => $categories
        ]);
    }

    public function toggleBookmark(Event $event)
    {

        $user = Auth::user();

        if ($user->bookmarks()->where('event_id', $event->id)->exists()) {
            DB::table('bookmarks')->insert(['user_id' => $user->id, 'event_id' => $event->id]);

            $user->bookmarks()->detach($event->id);
            return response()->json(['message' => ' Bookmark removed']);
        }
        $user->bookmarks()->attach($event->id);
        return response()->json(['message' => 'Bookmark added']);
    }

    public function getBookmarks()
    {
        $bookmarks = Auth::user()->bookmarks()->with('category')->paginate(10);
        return response()->json(['data' => $bookmarks]);
    }

    public function getProfile()
    {
        $user = Auth::user();
        return response()->json([
            'data' => $user->only('name', 'email', 'bio', 'interests', 'avatar')
        ]);
    }
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'bio' => 'nullable|string|max:500',
            'interests' => 'nullable|array',
        ]);
        Auth::user()->update($validated);
        return response()->json(['message' => 'Profile updated']);
    }

    public function updateAvatar(Request $request)
    {

        // dd($request->all(), $request->file('avatar'));

        $request->validate(['avatar' => 'required|image|max:2048']);
        $path = $request->file('avatar')->store('avatars', 'public');


        Auth::user()->update(['avatar' => $path]);
        return response()->json([
            'message' => 'Avatar uploaded',
            'avatar_url' => asset("storage/$path")
        ]);
    }

    public function addReview(Request $request, Event $event)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500'
        ]);
        if ($event->reviews()->where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => 'You already reviewed this event'], 422);
        }

        $review = $event->reviews()->create([
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);
        return response()->json($review, 201);
    }

    public function getUserReview(Event $event)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            // Get reviews written BY this user (not reviews FOR their events)
            $reviews = Review::where('user_id', $user->id)
                ->with(['event:id,title,image,location,start_time']) // Include event details
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at,
                        'event' => [
                            'id' => $review->event->id,
                            'title' => $review->event->title,
                            'image' => $review->event->image,
                            'location' => $review->event->location,
                            'start_time' => $review->event->start_time,
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $reviews,
                'message' => 'User reviews retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteReview(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403, 'You can only delete your own reviews');
        }
        $review->delete();
        return response()->json(['message' => 'Review deleted']);
    }


    public function searchEvents(Request $request)
    {
        $query = $request->query('query');
        if (!$query) {
            return response()->json([
                'message' => 'No search query provided',
                'data' => []
            ], 200);
        };
        $events = Event::with('category', 'user')->where('title', 'like', '%', $query . '%')->orWhere('description', 'like', '%' . $query . '%')->get();
        return response()->json(['message' => 'search result retrieved', 'data' => $events]);
    }

    public function getRandomEvents()
    {
        $events = Event::with('category', 'user')
            ->inRandomOrder()
            ->take(5)
            ->get();

        return response()->json([
            'message' => 'Random events retrieved',
            'data' => $events
        ], 200);
    }


    public function getUserEvents()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }


            $createdEvents = Event::where('user_id', $user->id)
                ->with(['category', 'user'])
                ->orderBy('start_time', 'asc')
                ->get();


            return response()->json([
                'success' => true,
                'data' => $createdEvents,
                'message' => 'User events retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user events',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $user->settings = $request->all();
        $user->save();
        return response()->json(['message' => 'Settings updated']);
    }

    public function getEventsWithLocation()
    {
        try {
            $events = Event::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', '')
                ->where('longitude', '!=', '')
                ->with(['category', 'user'])
                ->orderBy('start_time', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $events,
                'message' => 'Events with location retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch events with location',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getAllEvents()
    {
        try {
            $events = Event::with(['category', 'user'])
                ->orderBy('start_time', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $events,
                'message' => 'All events retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch all events',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
