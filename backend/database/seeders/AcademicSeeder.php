<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Room;
use App\Models\StudyClass;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AcademicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Academic Year
        $year = AcademicYear::firstOrCreate(
            ['name' => '2025/2026'],
            ['start_date' => '2025-08-20', 'end_date' => '2026-06-20', 'status' => 'active']
        );

        // 2. Semesters
        $sem1 = $year->semesters()->firstOrCreate(
            ['name' => 'الفصل الأول'],
            ['status' => 'active']
        );
        $year->semesters()->firstOrCreate(
            ['name' => 'الفصل الثاني'],
            ['status' => 'inactive']
        );

        // 3. Grades (Marahil)
        $grades = [
            ['name' => 'الصف العاشر', 'order' => 10],
            ['name' => 'الصف الحادي عشر', 'order' => 11],
            ['name' => 'الصف الثاني عشر', 'order' => 12],
        ];

        foreach ($grades as $g) {
            $grade = Grade::firstOrCreate(
                ['name' => $g['name']],
                ['level_order' => $g['order']]
            );

            // 4. Subjects (Mawad) for each grade
            $subjects = [
                ['name' => 'اللغة العربية', 'pkg' => 50, 'sng' => 150],
                ['name' => 'اللغة الإنجليزية', 'pkg' => 60, 'sng' => 180],
                ['name' => 'الرياضيات', 'pkg' => 80, 'sng' => 250],
                ['name' => 'الفيزياء', 'pkg' => 70, 'sng' => 220],
            ];

            foreach ($subjects as $s) {
                Subject::firstOrCreate(
                    ['grade_id' => $grade->id, 'name' => $s['name']],
                    [
                        'price_package' => $s['pkg'],
                        'price_single' => $s['sng']
                    ]
                );
            }

            // 5. Classes (Sho'ab)
            // Package Class
            StudyClass::firstOrCreate(
                ['name' => "شعبة عامة - {$g['name']}"],
                [
                    'semester_id' => $sem1->id,
                    'grade_id' => $grade->id,
                    'type' => 'package',
                    'max_students' => 25
                ]
            );
        }

        // Single Class Example (Remedial Math for Grade 12)
        $g12 = Grade::where('name', 'الصف الثاني عشر')->first();
        $math12 = Subject::where('grade_id', $g12->id)->where('name', 'الرياضيات')->first();

        StudyClass::firstOrCreate(
            ['name' => 'شعبة تقوية رياضيات - توجيهي'],
            [
                'semester_id' => $sem1->id,
                'grade_id' => $g12->id,
                'type' => 'single',
                'subject_id' => $math12->id,
                'max_students' => 15
            ]
        );

        // 6. Rooms
        $rooms = [
            ['name' => 'القاعة الذهبية', 'cap' => 40, 'type' => 'hall'],
            ['name' => 'مختبر الحاسوب', 'cap' => 20, 'type' => 'lab'],
            ['name' => 'فصل 101', 'cap' => 25, 'type' => 'classroom'],
            ['name' => 'فصل 102', 'cap' => 25, 'type' => 'classroom'],
        ];

        foreach ($rooms as $r) {
            Room::firstOrCreate(
                ['name' => $r['name']],
                ['capacity' => $r['cap'], 'type' => $r['type']]
            );
        }
    }
}
