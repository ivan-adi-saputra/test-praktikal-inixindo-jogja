<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentDetail extends Model
{
    protected $table = 'enrollment_details';
    protected $guarded = ['id'];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function course_content(): BelongsTo
    {
        return $this->belongsTo(CourseContent::class);
    }
}
