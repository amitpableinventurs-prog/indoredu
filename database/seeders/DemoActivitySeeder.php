<?php

namespace Database\Seeders;

use App\Models\AdminActionLog;
use App\Models\Attendance;
use App\Models\Booking;
use App\Models\ContentReport;
use App\Models\Conversation;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Review;
use App\Models\Subject;
use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoActivitySeeder extends Seeder
{
    protected User $admin;

    public function run(): void
    {
        $this->admin = User::where('role', User::ROLE_ADMIN)->firstOrFail();

        $bookingScenarios = [
            ['tutor' => 'alice.tutor@tutorhub.test', 'student' => 'ethan.student@tutorhub.test', 'subject' => 'Algebra', 'when' => -14, 'status' => 'completed', 'rating' => 5, 'comment' => 'Alice explained quadratic equations so clearly. My grades are already improving!', 'response' => 'Thank you Ethan, you worked really hard this session!'],
            ['tutor' => 'alice.tutor@tutorhub.test', 'student' => 'ethan.student@tutorhub.test', 'subject' => 'Algebra', 'when' => -7, 'status' => 'completed', 'rating' => 5, 'comment' => 'Another great session, very patient with my questions.'],
            ['tutor' => 'alice.tutor@tutorhub.test', 'student' => 'ethan.student@tutorhub.test', 'subject' => 'Physics', 'when' => -3, 'status' => 'completed', 'rating' => 4, 'comment' => 'Good session on Newton\'s laws, would have liked more practice problems.'],
            ['tutor' => 'alice.tutor@tutorhub.test', 'student' => 'ethan.student@tutorhub.test', 'subject' => 'Algebra', 'when' => 3, 'status' => 'confirmed'],
            ['tutor' => 'alice.tutor@tutorhub.test', 'student' => 'ananya.student@tutorhub.test', 'subject' => 'Algebra', 'when' => -5, 'status' => 'completed', 'rating' => 5, 'comment' => 'Alice made algebra fun for my daughter. Highly recommend!'],
            ['tutor' => 'alice.tutor@tutorhub.test', 'student' => 'ananya.student@tutorhub.test', 'subject' => 'Geometry', 'when' => 5, 'status' => 'pending'],

            ['tutor' => 'priya.tutor@tutorhub.test', 'student' => 'fatima.student@tutorhub.test', 'subject' => 'Chemistry', 'when' => -12, 'status' => 'completed', 'rating' => 5, 'comment' => 'Priya ma\'am is amazing at breaking down organic chemistry reactions.'],
            ['tutor' => 'priya.tutor@tutorhub.test', 'student' => 'fatima.student@tutorhub.test', 'subject' => 'Chemistry', 'when' => -6, 'status' => 'completed', 'rating' => 4, 'comment' => 'Helpful session, covered a lot of ground quickly.', 'response' => 'Glad it helped! Let\'s slow down a bit more next time.'],
            ['tutor' => 'priya.tutor@tutorhub.test', 'student' => 'fatima.student@tutorhub.test', 'subject' => 'Biology', 'when' => -2, 'status' => 'completed', 'rating' => 5, 'comment' => 'Best biology tutor I\'ve had. Explains diagrams so well.'],
            ['tutor' => 'priya.tutor@tutorhub.test', 'student' => 'fatima.student@tutorhub.test', 'subject' => 'Chemistry', 'when' => 4, 'status' => 'confirmed'],

            ['tutor' => 'brian.tutor@tutorhub.test', 'student' => 'george.student@tutorhub.test', 'subject' => 'Python Programming', 'when' => -20, 'status' => 'completed', 'rating' => 5, 'comment' => 'Brian is a fantastic Python mentor, real-world examples were great.'],
            ['tutor' => 'brian.tutor@tutorhub.test', 'student' => 'george.student@tutorhub.test', 'subject' => 'Python Programming', 'when' => -9, 'status' => 'completed', 'rating' => 5, 'comment' => 'Helped me debug my final project, saved me hours.', 'response' => 'Anytime! You\'re getting really good at this.'],
            ['tutor' => 'brian.tutor@tutorhub.test', 'student' => 'george.student@tutorhub.test', 'subject' => 'Web Development', 'when' => 2, 'status' => 'confirmed'],

            ['tutor' => 'rohan.tutor@tutorhub.test', 'student' => 'george.student@tutorhub.test', 'subject' => 'Data Structures', 'when' => -8, 'status' => 'completed', 'rating' => 3, 'comment' => 'Decent session but moved a bit too fast for me.'],
            ['tutor' => 'rohan.tutor@tutorhub.test', 'student' => 'mei.student@tutorhub.test', 'subject' => 'Machine Learning', 'when' => -4, 'status' => 'completed', 'rating' => 5, 'comment' => 'Rohan\'s explanation of gradient descent finally made it click for me.'],
            ['tutor' => 'rohan.tutor@tutorhub.test', 'student' => 'mei.student@tutorhub.test', 'subject' => 'Machine Learning', 'when' => 6, 'status' => 'confirmed'],

            ['tutor' => 'david.tutor@tutorhub.test', 'student' => 'rohan.student@tutorhub.test', 'subject' => 'SAT Prep', 'when' => -15, 'status' => 'completed', 'rating' => 5, 'comment' => 'My practice test score jumped 120 points after working with David.'],
            ['tutor' => 'david.tutor@tutorhub.test', 'student' => 'rohan.student@tutorhub.test', 'subject' => 'SAT Prep', 'when' => -1, 'status' => 'completed', 'rating' => 4, 'comment' => 'Solid strategies for the reading section.'],

            ['tutor' => 'carla.tutor@tutorhub.test', 'student' => 'zara.student@tutorhub.test', 'subject' => 'Spanish', 'when' => -6, 'status' => 'completed', 'rating' => 5, 'comment' => 'Carla is so encouraging, my confidence speaking Spanish has grown so much.'],
            ['tutor' => 'carla.tutor@tutorhub.test', 'student' => 'zara.student@tutorhub.test', 'subject' => 'French', 'when' => -1, 'status' => 'completed', 'rating' => 5, 'comment' => 'Loved the free trial, signing up for more sessions!', 'is_trial' => true],
            ['tutor' => 'carla.tutor@tutorhub.test', 'student' => 'zara.student@tutorhub.test', 'subject' => 'Spanish', 'when' => 7, 'status' => 'confirmed'],

            ['tutor' => 'ananya.tutor@tutorhub.test', 'student' => 'liam.student@tutorhub.test', 'subject' => 'Piano', 'when' => -10, 'status' => 'completed', 'rating' => 5, 'comment' => 'My son loves his piano lessons with Ananya, she\'s so patient with kids.'],
            ['tutor' => 'ananya.tutor@tutorhub.test', 'student' => 'liam.student@tutorhub.test', 'subject' => 'Piano', 'when' => -3, 'status' => 'completed', 'rating' => 4, 'comment' => 'Good progress on scales this week.'],

            ['tutor' => 'wei.tutor@tutorhub.test', 'student' => 'mei.student@tutorhub.test', 'subject' => 'English', 'when' => -2, 'status' => 'cancelled', 'cancel_reason' => 'Student had a scheduling conflict.'],
            ['tutor' => 'fatima.tutor@tutorhub.test', 'student' => 'rohan.student@tutorhub.test', 'subject' => 'IELTS Prep', 'when' => -18, 'status' => 'completed', 'rating' => 2, 'comment' => 'Tutor was often late to sessions, needs improvement.', 'flag' => true],
            ['tutor' => 'marcus.tutor@tutorhub.test', 'student' => 'ananya.student@tutorhub.test', 'subject' => 'Statistics', 'when' => -5, 'status' => 'completed', 'rating' => 5, 'comment' => 'Marcus made statistics finally make sense before my exam.', 'hide' => true],
        ];

        $tutorEarnings = [];

        foreach ($bookingScenarios as $s) {
            $tutor = User::where('email', $s['tutor'])->first();
            $student = User::where('email', $s['student'])->first();
            $subject = Subject::where('name', $s['subject'])->first();
            if (! $tutor || ! $student || ! $subject) {
                continue;
            }

            $tutorProfile = $tutor->tutorProfile;
            $isTrial = $s['is_trial'] ?? false;
            $rate = $isTrial ? $tutorProfile->trial_price : ($tutorProfile->tutorSubjects()->where('subject_id', $subject->id)->value('hourly_rate') ?? $tutorProfile->hourly_rate);
            $duration = 60;
            $price = $isTrial ? (float) $tutorProfile->trial_price : round($rate * ($duration / 60), 2);
            $date = now()->addDays($s['when']);

            $booking = Booking::create([
                'student_id' => $student->id,
                'tutor_id' => $tutor->id,
                'subject_id' => $subject->id,
                'scheduled_date' => $date->toDateString(),
                'start_time' => '16:00:00',
                'end_time' => '17:00:00',
                'timezone' => 'Asia/Kolkata',
                'duration_minutes' => $duration,
                'is_trial' => $isTrial,
                'price' => $price,
                'status' => match ($s['status']) {
                    'completed' => Booking::STATUS_COMPLETED,
                    'confirmed' => Booking::STATUS_CONFIRMED,
                    'pending' => Booking::STATUS_PENDING,
                    'cancelled' => Booking::STATUS_CANCELLED,
                    default => Booking::STATUS_PENDING,
                },
                'meeting_link' => 'https://meet.jit.si/IndorEdu-'.Str::random(12),
                'confirmed_at' => in_array($s['status'], ['completed', 'confirmed']) ? $date->copy()->subDays(1) : null,
                'completed_at' => $s['status'] === 'completed' ? $date : null,
                'cancelled_at' => $s['status'] === 'cancelled' ? $date : null,
                'cancelled_by' => $s['status'] === 'cancelled' ? $student->id : null,
                'cancellation_reason' => $s['cancel_reason'] ?? null,
            ]);

            // Conversation + a couple of messages for every booking.
            $conversation = Conversation::create(['booking_id' => $booking->id, 'subject' => 'Booking #'.$booking->id, 'last_message_at' => $date->copy()->subDays(2)]);
            $conversation->participants()->attach([
                $student->id => ['last_read_at' => now()],
                $tutor->id => ['last_read_at' => now()->subHours(3)],
            ]);
            Message::create(['conversation_id' => $conversation->id, 'sender_id' => $student->id, 'body' => "Hi {$tutor->name}, looking forward to our {$subject->name} session!", 'created_at' => $date->copy()->subDays(2)]);
            Message::create(['conversation_id' => $conversation->id, 'sender_id' => $tutor->id, 'body' => 'Looking forward to it! I\'ll share some prep material beforehand.', 'created_at' => $date->copy()->subDays(2)->addMinutes(20)]);

            // Payment record (skip for free trials and cancelled-without-payment).
            if ($price > 0 && $s['status'] !== 'cancelled') {
                $platformFee = round($price * 0.15, 2);
                Payment::create([
                    'booking_id' => $booking->id,
                    'payer_id' => $student->id,
                    'payee_id' => $tutor->id,
                    'gateway' => 'stripe',
                    'gateway_transaction_id' => 'cs_test_'.Str::random(16),
                    'amount' => $price,
                    'currency' => 'INR',
                    'platform_fee' => $platformFee,
                    'net_amount' => $price - $platformFee,
                    'status' => in_array($s['status'], ['completed', 'confirmed']) ? Payment::STATUS_COMPLETED : Payment::STATUS_PENDING,
                    'paid_at' => in_array($s['status'], ['completed', 'confirmed']) ? $date->copy()->subDays(1) : null,
                ]);

                if (in_array($s['status'], ['completed', 'confirmed'])) {
                    $tutorEarnings[$tutor->id] = ($tutorEarnings[$tutor->id] ?? 0) + ($price - $platformFee);
                }
            }

            if ($s['status'] === 'completed') {
                Attendance::create([
                    'booking_id' => $booking->id,
                    'student_status' => 'present',
                    'tutor_status' => 'present',
                    'student_check_in' => $date,
                    'tutor_check_in' => $date,
                    'marked_by' => $tutor->id,
                    'notes' => 'Covered planned material for the session.',
                ]);

                if (isset($s['rating'])) {
                    $review = Review::create([
                        'booking_id' => $booking->id,
                        'student_id' => $student->id,
                        'tutor_id' => $tutor->id,
                        'rating' => $s['rating'],
                        'comment' => $s['comment'] ?? null,
                        'tutor_response' => $s['response'] ?? null,
                        'tutor_responded_at' => isset($s['response']) ? $date->copy()->addHours(2) : null,
                        'is_flagged' => $s['flag'] ?? false,
                        'is_approved' => ! ($s['hide'] ?? false),
                    ]);
                }
            }
        }

        // Recalculate tutor stats from seeded reviews/bookings.
        foreach (TutorProfile::all() as $profile) {
            $stats = Review::where('tutor_id', $profile->user_id)->where('is_approved', true)
                ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')->first();

            $profile->update([
                'rating_avg' => round($stats->avg_rating ?? 0, 2),
                'rating_count' => $stats->total ?? 0,
                'total_sessions' => Booking::where('tutor_id', $profile->user_id)->where('status', Booking::STATUS_COMPLETED)->count(),
                'total_students' => Booking::where('tutor_id', $profile->user_id)->distinct('student_id')->count('student_id'),
            ]);
        }

        $this->seedCourses();
        $this->seedPayouts($tutorEarnings);
        $this->seedModeration();
        $this->seedActivityLog();
    }

    protected function seedCourses(): void
    {
        $courses = [
            ['tutor' => 'alice.tutor@tutorhub.test', 'subject' => 'Algebra', 'title' => 'Algebra Mastery Bootcamp', 'price' => 2499, 'sessions' => 4, 'enroll' => 'ethan.student@tutorhub.test'],
            ['tutor' => 'brian.tutor@tutorhub.test', 'subject' => 'Python Programming', 'title' => 'Python for Absolute Beginners', 'price' => 3499, 'sessions' => 5, 'enroll' => 'george.student@tutorhub.test'],
            ['tutor' => 'priya.tutor@tutorhub.test', 'subject' => 'Biology', 'title' => 'Board Exam Biology Crash Course', 'price' => 2999, 'sessions' => 6, 'enroll' => 'fatima.student@tutorhub.test'],
            ['tutor' => 'rohan.tutor@tutorhub.test', 'subject' => 'Machine Learning', 'title' => 'Intro to Machine Learning', 'price' => 4999, 'sessions' => 4, 'enroll' => 'mei.student@tutorhub.test'],
            ['tutor' => 'carla.tutor@tutorhub.test', 'subject' => 'Spanish', 'title' => 'Conversational Spanish Group Class', 'price' => 1999, 'sessions' => 4, 'group' => true, 'enroll' => null],
            ['tutor' => 'david.tutor@tutorhub.test', 'subject' => 'SAT Prep', 'title' => 'SAT Math Bootcamp', 'price' => 3299, 'sessions' => 5, 'enroll' => 'rohan.student@tutorhub.test'],
        ];

        foreach ($courses as $c) {
            $tutor = User::where('email', $c['tutor'])->first();
            $subject = Subject::where('name', $c['subject'])->first();
            if (! $tutor || ! $subject) {
                continue;
            }

            $course = Course::firstOrCreate(
                ['slug' => Str::slug($c['title'])],
                [
                    'tutor_profile_id' => $tutor->tutorProfile->id,
                    'subject_id' => $subject->id,
                    'title' => $c['title'],
                    'description' => "A focused, results-driven {$c['subject']} program designed to build real confidence session by session.",
                    'level' => 'all_levels',
                    'price' => $c['price'],
                    'duration_minutes' => 60,
                    'total_sessions' => $c['sessions'],
                    'is_group' => $c['group'] ?? false,
                    'max_students' => ($c['group'] ?? false) ? 6 : 1,
                    'status' => Course::STATUS_PUBLISHED,
                ]
            );

            if ($c['enroll']) {
                $student = User::where('email', $c['enroll'])->first();
                if ($student && $student->studentProfile) {
                    $enrollment = CourseEnrollment::firstOrCreate(
                        ['course_id' => $course->id, 'student_profile_id' => $student->studentProfile->id],
                        ['status' => 'active', 'enrolled_at' => now()->subDays(5)]
                    );

                    $fee = round($course->price * 0.15, 2);
                    Payment::firstOrCreate(
                        ['course_enrollment_id' => $enrollment->id],
                        [
                            'payer_id' => $student->id,
                            'payee_id' => $tutor->id,
                            'gateway' => 'paypal',
                            'gateway_transaction_id' => 'ORDER-'.Str::upper(Str::random(10)),
                            'amount' => $course->price,
                            'currency' => 'INR',
                            'platform_fee' => $fee,
                            'net_amount' => $course->price - $fee,
                            'status' => Payment::STATUS_COMPLETED,
                            'paid_at' => now()->subDays(5),
                        ]
                    );
                }
            }
        }

        // One course still pending admin review, for the moderation queue demo.
        $marcus = User::where('email', 'marcus.tutor@tutorhub.test')->first();
        $stats = Subject::where('name', 'Statistics')->first();
        if ($marcus && $stats) {
            Course::firstOrCreate(
                ['slug' => 'statistics-for-beginners'],
                [
                    'tutor_profile_id' => $marcus->tutorProfile->id,
                    'subject_id' => $stats->id,
                    'title' => 'Statistics for Beginners',
                    'description' => 'From averages to hypothesis testing, a gentle on-ramp to statistics.',
                    'level' => 'beginner',
                    'price' => 2199,
                    'duration_minutes' => 60,
                    'total_sessions' => 4,
                    'status' => Course::STATUS_PENDING,
                ]
            );
        }
    }

    protected function seedPayouts(array $tutorEarnings): void
    {
        $statuses = ['paid', 'pending', 'processing'];
        $i = 0;
        foreach ($tutorEarnings as $tutorId => $earned) {
            if ($earned < 500) {
                continue;
            }
            $tutor = User::find($tutorId);
            if (! $tutor) {
                continue;
            }

            $amount = round($earned * 0.6, 2);
            $status = $statuses[$i % count($statuses)];
            $i++;

            Payout::create([
                'tutor_profile_id' => $tutor->tutorProfile->id,
                'amount' => $amount,
                'currency' => 'INR',
                'method' => $i % 2 === 0 ? 'bank_transfer' : 'paypal',
                'status' => $status,
                'requested_at' => now()->subDays(6),
                'processed_at' => $status === 'pending' ? null : now()->subDays(2),
            ]);
        }
    }

    protected function seedModeration(): void
    {
        $reporter = User::where('email', 'mei.student@tutorhub.test')->first();
        $reportedTutor = User::where('email', 'fatima.tutor@tutorhub.test')->first();
        $flaggedReview = Review::where('is_flagged', true)->first();

        if ($reporter && $reportedTutor) {
            ContentReport::firstOrCreate(
                ['reporter_id' => $reporter->id, 'reportable_type' => User::class, 'reportable_id' => $reportedTutor->id],
                ['reason' => 'Frequently late to sessions', 'details' => 'Tutor has joined more than 10 minutes late twice.', 'status' => ContentReport::STATUS_PENDING]
            );
        }

        if ($reporter && $flaggedReview) {
            ContentReport::firstOrCreate(
                ['reporter_id' => $reporter->id, 'reportable_type' => Review::class, 'reportable_id' => $flaggedReview->id],
                ['reason' => 'Review seems unfair', 'details' => 'The tutor disputes this review as inaccurate.', 'status' => ContentReport::STATUS_PENDING]
            );
        }
    }

    protected function seedActivityLog(): void
    {
        foreach (TutorProfile::with('user')->get() as $profile) {
            AdminActionLog::create([
                'admin_id' => $this->admin->id,
                'action' => 'tutor.approved',
                'subject_type' => TutorProfile::class,
                'subject_id' => $profile->id,
                'details' => ['tutor_name' => $profile->user->name],
                'created_at' => $profile->approved_at ?? now()->subDays(10),
                'updated_at' => $profile->approved_at ?? now()->subDays(10),
            ]);
        }

        $paidPayout = Payout::where('status', 'paid')->first();
        if ($paidPayout) {
            AdminActionLog::create([
                'admin_id' => $this->admin->id,
                'action' => 'payout.paid',
                'subject_type' => Payout::class,
                'subject_id' => $paidPayout->id,
                'details' => ['amount' => $paidPayout->amount],
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);
        }
    }
}
