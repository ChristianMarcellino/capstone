<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Event::with('eventItems.product')->get(),
            'message' => 'Events retrieved successfully',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventStoreRequest $request)
    {
        try {
            $event = DB::transaction(function () use ($request) {
                return Event::create($request->validated());
            });

            return response()->json([
                'data' => $event,
                'message' => 'Event created successfully',
            ], 201);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to create event.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventUpdateRequest $request, Event $event)
    {
        try {
            $updatedEvent = DB::transaction(function () use ($request, $event) {
                $event->update($request->validated());
                return $event->refresh();
            });

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to update event.',
            ], 500);
        }

        return response()->json([
            'data' => $updatedEvent,
            'message' => 'Event updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        try {
            DB::transaction(function () use ($event) {
                $event->delete();
            });

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to delete event.',
            ], 500);
        }

        return response()->json([
            'message' => 'Event deleted successfully',
        ], 200);
    }
}
