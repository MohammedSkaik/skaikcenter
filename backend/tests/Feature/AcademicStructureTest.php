<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Room;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_academic_hierarchy()
    {
        // 1. Create Year
        $year = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'status' => 'active'
        ]);

        $this->assertDatabaseHas('academic_years', ['name' => '2025/2026']);

        // 2. Create Semester linked to Year
        $semester = Semester::create([
            'academic_year_id' => $year->id,
            'name' => 'First Semester',
            'status' => 'active'
        ]);

        $this->assertEquals($year->id, $semester->academic_year_id);
    }

    public function test_can_create_grade_and_subject()
    {
        $grade = Grade::create([
            'name' => 'Grade 10',
            'level_order' => 10
        ]);

        $subject = Subject::create([
            'grade_id' => $grade->id,
            'name' => 'Math',
            'price_package' => 50,
            'price_single' => 150
        ]);

        $this->assertEquals($grade->id, $subject->grade_id);
        $this->assertDatabaseHas('subjects', ['name' => 'Math', 'price_single' => 150]);
    }

    public function test_can_create_room()
    {
        $room = Room::create([
            'name' => 'Blue Hall',
            'capacity' => 30,
            'type' => 'classroom'
        ]);

        $this->assertDatabaseHas('rooms', ['name' => 'Blue Hall']);
    }
    public function test_can_create_study_class_with_types()
    {
        // Setup deps
        $year = AcademicYear::create(['name' => '2025/2026', 'start_date' => '2025-01-01', 'end_date' => '2026-01-01']);
        $semester = Semester::create(['academic_year_id' => $year->id, 'name' => 'Sem 1']);
        $grade = Grade::create(['name' => 'G10']);
        $subject = Subject::create(['grade_id' => $grade->id, 'name' => 'Math']);

        // 1. Package Class
        $packageClass = \App\Models\StudyClass::create([
            'semester_id' => $semester->id,
            'grade_id' => $grade->id,
            'name' => 'Group A (Package)',
            'type' => 'package'
        ]);

        $this->assertEquals('package', $packageClass->type);
        $this->assertNull($packageClass->subject_id);

        // 2. Single Class
        $singleClass = \App\Models\StudyClass::create([
            'semester_id' => $semester->id,
            'grade_id' => $grade->id,
            'name' => 'Math Special',
            'type' => 'single',
            'subject_id' => $subject->id
        ]);

        $this->assertEquals('single', $singleClass->type);
        $this->assertEquals($subject->id, $singleClass->subject_id);
    }
}
