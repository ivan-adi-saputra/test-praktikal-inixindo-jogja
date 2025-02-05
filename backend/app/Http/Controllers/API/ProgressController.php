<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\Enrollment;
use App\Models\EnrollmentDetail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        try {
            $course_content_id = $request->course_content_id;
            $enrollment_id = $request->enrollment_id;
            if (!CourseContent::where('id', $course_content_id)->exists()) {
                return $this->handleResponse('Course content not found', 404);
            }

            if (!Enrollment::where('id', $enrollment_id)->exists()) {
                return $this->handleResponse('Enrollment not found', 404);
            }

            EnrollmentDetail::create([
                'enrollment_id' => $enrollment_id,
                'course_content_id' => $course_content_id,
                'completed_status' => true,
            ]);

            return $this->handleResponse('Add progress complete', 200, [
                'completed_status' => true,
            ]);
        } catch (\Exception $e) {
            return $this->handleResponse('Add progress complete failed', 500, null, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $user_id)
    {
        try {
            if (!User::find($user_id)) {
                return $this->handleResponse('User not found', 404);
            }

            $enrollment = Enrollment::with(['course', 'enrollment_details'])
                ->where('user_id', $user_id)
                ->get()
                ->map(function ($item) use ($user_id) {
                    return [
                        'course' => $item->course->id,
                        'course_title' => $item->course->title,
                        'progress' => $this->getProgress($item->course, $user_id),
                    ];
                });

            return $this->handleResponse('Get progress succcessfully', 200, $enrollment);
        } catch (\Exception $e) {
            return $this->handleResponse('Get progress failed', 500, null, $e->getMessage());
        }
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function getProgress(Course $course, $user_id)
    {
        $completed_course_contents = EnrollmentDetail::with('enrollment')
            ->whereHas('enrollment', function ($query) use ($course, $user_id) {
                $query->where('course_id', $course->id)
                    ->where('user_id', $user_id);
            })
            ->where('completed_status', true)
            ->get();

        $total_course_contents = CourseContent::where('course_id', $course->id)->count();

        if ($total_course_contents === 0) {
            return 0;
        }

        $progress = round(($completed_course_contents->count() / $total_course_contents) * 100, 2);

        return $progress;
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
