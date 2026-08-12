<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\SubjectCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Mathematics' => ['Algebra', 'Geometry', 'Calculus', 'Statistics', 'Trigonometry'],
            'Science' => ['Physics', 'Chemistry', 'Biology', 'Environmental Science'],
            'Languages' => ['English', 'Spanish', 'French', 'Mandarin Chinese', 'German'],
            'Computer Science' => ['Python Programming', 'Web Development', 'Data Structures', 'Machine Learning'],
            'Test Prep' => ['SAT Prep', 'ACT Prep', 'GRE Prep', 'IELTS Prep'],
            'Arts & Music' => ['Piano', 'Guitar', 'Music Theory', 'Drawing & Painting'],
        ];

        foreach ($data as $categoryName => $subjects) {
            $category = SubjectCategory::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            foreach ($subjects as $subjectName) {
                Subject::firstOrCreate(
                    ['slug' => Str::slug($subjectName)],
                    [
                        'subject_category_id' => $category->id,
                        'name' => $subjectName,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
