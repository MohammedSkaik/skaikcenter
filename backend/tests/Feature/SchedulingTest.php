<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Room;
use App\Models\Semester;
use App\Models\StudyClass;
use App\Models\Subject;
use App\Models\TeachingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchedulingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_schedule_session_with_lesson()
    {
        // 1. Setup Data
        $year = AcademicYear::create(['name' => '2025/2026', 'start_date' => '2025-01-01', 'end_date' => '2026-06-01']);
        $semester = Semester::create(['academic_year_id' => $year->id, 'name' => 'S1']);
        $grade = Grade::create(['name' => 'G10']);
        $subject = Subject::create(['grade_id' => $grade->id, 'name' => 'Physics']);
        $room = Room::create(['name' => 'Lab 1']);
        $teacher = User::factory()->create();

        $studyClass = StudyClass::create([
            'semester_id' => $semester->id,
            'grade_id' => $grade->id,
            'name' => 'Physics Group',
            'type' => 'single',
            'subject_id' => $subject->id
        ]);

        // 2. Create Teaching Session
        $session = TeachingSession::create([
            'study_class_id' => $studyClass->id,
            'room_id' => $room->id,
            'date' => '2025-02-01',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'status' => 'scheduled'
        ]);

        $this->assertNotNull($session->fresh());
        $this->assertEquals('2025-02-01', $session->fresh()->date->format('Y-m-d'));

        // 3. Create Lesson inside Session
        $lesson = Lesson::create([
            'teaching_session_id' => $session->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'status' => 'pending'
        ]);

        $this->assertEquals($session->id, $lesson->teaching_session_id);
        $this->assertEquals($teacher->id, $lesson->teacher_id);
    }
}
