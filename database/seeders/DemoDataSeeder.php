<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@tutorhub.test'],
            [
                'name' => 'Platform Admin',
                'password' => 'Password123!',
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'city' => 'Mumbai',
                'country' => 'India',
            ]
        );

        $tutors = [
            ['name' => 'Alice Nakamura', 'email' => 'alice.tutor@tutorhub.test', 'headline' => 'Math & Physics tutor, PhD candidate', 'subjects' => ['Algebra', 'Calculus', 'Physics'], 'rate' => 800, 'city' => 'Bengaluru', 'trial' => true, 'trial_price' => 199],
            ['name' => 'Brian Okafor', 'email' => 'brian.tutor@tutorhub.test', 'headline' => 'Full-stack developer teaching Python & Web Dev', 'subjects' => ['Python Programming', 'Web Development', 'Data Structures'], 'rate' => 1200, 'city' => 'Pune', 'trial' => true, 'trial_price' => 299],
            ['name' => 'Carla Mendes', 'email' => 'carla.tutor@tutorhub.test', 'headline' => 'Native Spanish & French speaker, 8 yrs experience', 'subjects' => ['Spanish', 'French'], 'rate' => 650, 'city' => 'Delhi', 'trial' => true, 'trial_price' => 0],
            ['name' => 'David Kim', 'email' => 'david.tutor@tutorhub.test', 'headline' => 'SAT/ACT prep specialist', 'subjects' => ['SAT Prep', 'ACT Prep', 'Algebra'], 'rate' => 1000, 'city' => 'Hyderabad', 'trial' => false, 'trial_price' => 0],
            ['name' => 'Priya Sharma', 'email' => 'priya.tutor@tutorhub.test', 'headline' => 'Biology & Chemistry teacher, 10th/12th board specialist', 'subjects' => ['Biology', 'Chemistry', 'Environmental Science'], 'rate' => 700, 'city' => 'Chennai', 'trial' => true, 'trial_price' => 149],
            ['name' => 'Rohan Mehta', 'email' => 'rohan.tutor@tutorhub.test', 'headline' => 'ML engineer teaching Data Structures & Machine Learning', 'subjects' => ['Data Structures', 'Machine Learning', 'Web Development'], 'rate' => 1500, 'city' => 'Bengaluru', 'trial' => true, 'trial_price' => 399],
            ['name' => 'Ananya Iyer', 'email' => 'ananya.tutor@tutorhub.test', 'headline' => 'Classically trained pianist & music theory teacher', 'subjects' => ['Piano', 'Music Theory'], 'rate' => 600, 'city' => 'Mumbai', 'trial' => true, 'trial_price' => 0],
            ['name' => 'Wei Zhang', 'email' => 'wei.tutor@tutorhub.test', 'headline' => 'Mandarin Chinese & English fluency coach', 'subjects' => ['Mandarin Chinese', 'English'], 'rate' => 750, 'city' => 'Delhi', 'trial' => true, 'trial_price' => 199],
            ['name' => 'Fatima Al-Rashid', 'email' => 'fatima.tutor@tutorhub.test', 'headline' => 'IELTS & GRE prep coach, ex-examiner', 'subjects' => ['IELTS Prep', 'GRE Prep', 'English'], 'rate' => 900, 'city' => 'Pune', 'trial' => false, 'trial_price' => 0],
            ['name' => 'Marcus Johnson', 'email' => 'marcus.tutor@tutorhub.test', 'headline' => 'Statistics & advanced calculus specialist', 'subjects' => ['Statistics', 'Calculus', 'Trigonometry'], 'rate' => 850, 'city' => 'Hyderabad', 'trial' => true, 'trial_price' => 249],
        ];

        foreach ($tutors as $i => $t) {
            $user = User::firstOrCreate(
                ['email' => $t['email']],
                [
                    'name' => $t['name'],
                    'password' => 'Password123!',
                    'role' => User::ROLE_TUTOR,
                    'status' => User::STATUS_ACTIVE,
                    'email_verified_at' => now(),
                    'city' => $t['city'],
                    'country' => 'India',
                    'timezone' => 'Asia/Kolkata',
                ]
            );

            $profile = TutorProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'headline' => $t['headline'],
                    'bio' => "Hi, I'm {$t['name']}. ".$t['headline'].'. I love helping students build confidence and master new concepts through patient, personalized lessons.',
                    'hourly_rate' => $t['rate'],
                    'currency' => 'INR',
                    'offers_trial' => $t['trial'],
                    'trial_price' => $t['trial_price'],
                    'experience_years' => 3 + $i,
                    'education' => "M.Sc. in {$t['subjects'][0]}",
                    'languages' => ['English', 'Hindi'],
                    'status' => TutorProfile::STATUS_APPROVED,
                    'approved_at' => now()->subDays(30 - $i),
                    'approved_by' => $admin->id,
                    'rating_avg' => 0,
                    'rating_count' => 0,
                    'total_sessions' => 0,
                    'total_students' => 0,
                ]
            );

            foreach ($t['subjects'] as $j => $subjectName) {
                $subject = Subject::where('name', $subjectName)->first();
                if ($subject) {
                    $profile->subjects()->syncWithoutDetaching([$subject->id => ['level' => $j === 0 ? 'expert' : 'advanced']]);
                }
            }

            foreach (range(1, 5) as $day) {
                $profile->availabilities()->firstOrCreate([
                    'day_of_week' => $day,
                    'start_time' => '15:00:00',
                    'end_time' => '20:00:00',
                ], ['is_active' => true]);
            }

            $certPath = "certificates/seed-tutor-{$user->id}.txt";
            if (! Storage::disk('local')->exists($certPath)) {
                Storage::disk('local')->put($certPath, "Certificate of Completion\n\nAwarded to {$t['name']} for teaching excellence in {$t['subjects'][0]}.\n(Demo placeholder document.)");
            }

            $profile->certificates()->firstOrCreate(
                ['title' => "Certified {$t['subjects'][0]} Educator"],
                [
                    'issuer' => 'National Board of Education',
                    'file_path' => $certPath,
                    'status' => $i % 3 === 0 ? \App\Models\TutorCertificate::STATUS_PENDING : \App\Models\TutorCertificate::STATUS_VERIFIED,
                    'verified_by' => $i % 3 === 0 ? null : $admin->id,
                    'verified_at' => $i % 3 === 0 ? null : now()->subDays(20),
                ]
            );
        }

        $students = [
            ['name' => 'Ethan Ross', 'email' => 'ethan.student@tutorhub.test', 'grade' => 'Grade 10', 'city' => 'Mumbai', 'subjects' => ['Algebra', 'Physics']],
            ['name' => 'Fatima Al-Sayed', 'email' => 'fatima.student@tutorhub.test', 'grade' => 'Grade 12', 'city' => 'Delhi', 'subjects' => ['Chemistry', 'Biology']],
            ['name' => 'George Papadopoulos', 'email' => 'george.student@tutorhub.test', 'grade' => 'Undergraduate', 'city' => 'Bengaluru', 'subjects' => ['Python Programming', 'Data Structures']],
            ['name' => 'Ananya Gupta', 'email' => 'ananya.student@tutorhub.test', 'grade' => 'Grade 9', 'city' => 'Pune', 'subjects' => ['Algebra', 'Geometry']],
            ['name' => 'Rohan Kapoor', 'email' => 'rohan.student@tutorhub.test', 'grade' => 'Grade 11', 'city' => 'Chennai', 'subjects' => ['SAT Prep', 'English']],
            ['name' => 'Zara Khan', 'email' => 'zara.student@tutorhub.test', 'grade' => 'Adult learner', 'city' => 'Hyderabad', 'subjects' => ['Spanish', 'French']],
            ['name' => 'Liam O\'Brien', 'email' => 'liam.student@tutorhub.test', 'grade' => 'Grade 8', 'city' => 'Mumbai', 'subjects' => ['Piano', 'Music Theory']],
            ['name' => 'Mei Lin', 'email' => 'mei.student@tutorhub.test', 'grade' => 'Undergraduate', 'city' => 'Delhi', 'subjects' => ['Machine Learning', 'Web Development']],
        ];

        foreach ($students as $i => $s) {
            $user = User::firstOrCreate(
                ['email' => $s['email']],
                [
                    'name' => $s['name'],
                    'password' => 'Password123!',
                    'role' => User::ROLE_STUDENT,
                    'status' => User::STATUS_ACTIVE,
                    'email_verified_at' => now(),
                    'city' => $s['city'],
                    'country' => 'India',
                    'timezone' => 'Asia/Kolkata',
                ]
            );

            $profile = $user->studentProfile()->firstOrCreate([], [
                'grade_level' => $s['grade'],
                'learning_goals' => 'Improve grades and prepare for upcoming exams.',
                'guardian_name' => $i % 2 === 0 ? 'Parent of '.explode(' ', $s['name'])[0] : null,
                'guardian_email' => $i % 2 === 0 ? 'guardian.'.strtolower(explode(' ', $s['name'])[0]).'@example.com' : null,
            ]);

            foreach ($s['subjects'] as $subjectName) {
                $subject = Subject::where('name', $subjectName)->first();
                if ($subject) {
                    $profile->subjects()->syncWithoutDetaching([$subject->id]);
                }
            }
        }

        $this->command?->info('Demo accounts (password: Password123!):');
        $this->command?->info('  Admin:   admin@tutorhub.test');
        $this->command?->info('  Tutor:   alice.tutor@tutorhub.test (+ 9 more, see seeder)');
        $this->command?->info('  Student: ethan.student@tutorhub.test (+ 7 more, see seeder)');
    }
}
