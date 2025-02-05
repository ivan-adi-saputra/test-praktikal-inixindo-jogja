<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Course\CreateRequest;
use App\Http\Requests\API\Course\UpdateRequest;
use App\Models\Course;
use App\Models\CourseContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Course::with(['course_contents', 'user', 'trainer'])
                ->get()
                ->map(function (Course $course) {
                    return $this->baseArrayResponse($course);
                });

            return $this->handleResponse('Get all courses successfully', 200, $data);
        } catch (\Exception $e) {
            return $this->handleResponse('Get all courses failed', 500, null, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        // return response()->json($request->all());
        DB::beginTransaction();
        try {
            // validation 
            if (Course::where('title', $request->title)->exists()) {
                return $this->handleResponse('Course already exists', 400);
            }

            $user = $request->attributes->get('user');

            $course = Course::create([
                'user_id' => $user->id,
                'trainer_id' => 1,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'title' => $request->title,
                'category' => $request->category,
                'price' => $request->price,
                'status' => $request->status ?? 'active',
                'description' => $request->description,
            ]);

            foreach ($request->course_contents as $item) {
                CourseContent::create([
                    'course_id' => $course->id,
                    'description' => $item['description'],
                ]);
            }

            $courseQuery = Course::with(['user', 'trainer', 'course_contents'])->findOrFail($course->id);

            DB::commit();
            return $this->handleResponse('Create course successfully', 201, $this->baseArrayResponse($courseQuery));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleResponse('Create course failed', 500, null, $$e->getMessage());
        }
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            if (!$course = Course::with(['course_contents', 'user', 'trainer'])->find($id)) {
                return $this->handleResponse('Course not found', 404);
            }

            if (Course::where('title', $request->title)->where('id', '!=', $id)->exists()) {
                return $this->handleResponse('Course already exists', 400);
            }

            $course->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'title' => $request->title,
                'category' => $request->category,
                'price' => $request->price,
                'description' => $request->description,
            ]);
            if ($request->status) {
                $course->update([
                    'status' => $request->status,
                ]);
            }

            foreach ($request->course_contents as $item) {
                CourseContent::where('id', $item['id'])->update([
                    'description' => $item['description'],
                ]);
            }

            DB::commit();
            return $this->handleResponse('Update course successfully', 200, $this->baseArrayResponse($course));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleResponse('Update course failed', 500, null, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            if (!$course = Course::with(['course_contents', 'user', 'trainer'])->find($id)) {
                return $this->handleResponse('Course not found', 404);
            }

            $course->delete();
            CourseContent::where('course_id', $id)->delete();

            DB::commit();
            return $this->handleResponse('Delete course successfully', 200, $this->baseArrayResponse($course));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleResponse('Delete Course failed', 500, null, $e->getMessage());
        }
    }

    private function baseArrayResponse(Course $course)
    {
        return [
            'id' => $course->id,
            'start_date' => $course->start_date,
            'end_date' => $course->end_date,
            'title' => $course->title,
            'category' => $course->category,
            'price' => $course->price,
            'status' => $course->status,
            'description' => $course->description,
            'user' => $course->user->name,
            'trainer' => $course->trainer->name,
            'course_contents' => $course->course_contents->map(function (CourseContent $content) {
                return [
                    'description' => $content->description,
                ];
            })
        ];
    }

    private function handleResponse(string $message, int $code, $data = null, $error = null): JsonResponse
    {
        $response = array_filter([
            'message' => $message,
            'code' => $code,
            'data' => $data,
            'error' => $error,
        ], function ($resp) {
            return $resp != null;
        });

        return response()->json($response);
    }
}
