<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Enrollment\CreateRequest;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
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
    public function store(CreateRequest $request)
    {
        try {
            $enrollment = Enrollment::create([
                'user_id' => $request->participant_id,
                'enrollment_date' => now(),
                'course_id' => $request->course_id,
            ]);

            $enrollmentQuery = Enrollment::with(['user', 'course', 'enrollment_details'])->findOrFail($enrollment->id);

            return $this->handleResponse('Create enrollment successfully', 201, $this->baseArrayResponse($enrollmentQuery));
        } catch (\Exception $e) {
            return $this->handleResponse('Create enrollment failed', 500, null, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $course_id)
    {
        try {
            if (!Course::find($course_id)) {
                return $this->handleResponse('Course not found', 404);
            }

            $enrollment = Enrollment::with(['user', 'course'])->where('course_id', $course_id)
                ->get()
                ->map(function ($item) {
                    return [
                        'participant_id' => $item->user->id,
                        'participant_name' => $item->user->name,
                    ];
                });

            return $this->handleResponse('Get enrollment successfully', 200, $enrollment);
        } catch (\Throwable $th) {
            return $this->handleResponse('Get enrollment failed', 500, null, $th->getMessage());
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
        try {
            if (!$enrollment = Enrollment::find($id)) {
                return $this->handleResponse('Enrollment not found', 404);
            }

            $enrollment->delete();

            return $this->handleResponse('Delete enrollment successfully', 204);
        } catch (\Exception $e) {
            return $this->handleResponse('Delete enrollment failed', 500, null, $e->getMessage());
        }
    }

    private function baseArrayResponse(Enrollment $enrollment)
    {
        return [
            'id' => $enrollment->id,
            'participat' => [
                'id' => $enrollment->user->id,
                'name' => $enrollment->user->name,
                'email' => $enrollment->user->email,
            ],
            'enrollment_date' => $enrollment->enrollment_date,
            'course' => [
                'id' => $enrollment->course->id,
                'title' => $enrollment->course->title,
            ],
            'enrollment_details' => $enrollment->enrollment_details->map(function ($detail) {
                return [
                    'course_content_id' => $detail->course_content_id,
                    'completed_status' => $detail->completed_status,
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
