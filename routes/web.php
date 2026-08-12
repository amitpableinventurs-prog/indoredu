<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ContentReportController as AdminContentReportController;
use App\Http\Controllers\Admin\CourseModerationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PayoutController as AdminPayoutController;
use App\Http\Controllers\Admin\ReviewModerationController;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\TutorApplicationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ContentReportController;
use App\Http\Controllers\CourseCatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SecureFileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Student\BookingController as StudentBookingController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\EnrollmentController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\ReportController as StudentReportController;
use App\Http\Controllers\Tutor\AvailabilityController;
use App\Http\Controllers\Tutor\BookingController as TutorBookingController;
use App\Http\Controllers\Tutor\CourseController as TutorCourseController;
use App\Http\Controllers\Tutor\DashboardController as TutorDashboardController;
use App\Http\Controllers\Tutor\PayoutController as TutorPayoutController;
use App\Http\Controllers\Tutor\ProfileController as TutorProfileController;
use App\Http\Controllers\Tutor\ReportController as TutorReportController;
use App\Http\Controllers\Tutor\StudentController as TutorStudentController;
use App\Http\Controllers\TutorSearchController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/tutors', [TutorSearchController::class, 'index'])->name('tutors.index');
Route::get('/tutors/{tutorProfile}', [TutorSearchController::class, 'show'])->name('tutors.show');
Route::get('/tutors/{tutorProfile}/slots', [CalendarController::class, 'tutorSlots'])->name('tutors.slots');

Route::get('/courses', [CourseCatalogController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseCatalogController::class, 'show'])->name('courses.show');

// Friendly redirects for the singular/bare role paths people naturally try
// (e.g. /tutor instead of /tutors or /tutor/dashboard) instead of a 404.
Route::get('/tutor', fn () => auth()->check() && auth()->user()->role === \App\Models\User::ROLE_TUTOR
    ? redirect()->route('tutor.dashboard')
    : redirect()->route('tutors.index'));

Route::get('/student', fn () => auth()->check() && auth()->user()->role === \App\Models\User::ROLE_STUDENT
    ? redirect()->route('student.dashboard')
    : redirect()->route('home'));

Route::get('/admin', fn () => auth()->check() && auth()->user()->role === \App\Models\User::ROLE_ADMIN
    ? redirect()->route('admin.dashboard')
    : redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Webhooks (no CSRF, no auth — verified per-gateway inside the controller)
|--------------------------------------------------------------------------
*/

Route::post('/webhooks/{gateway}', [WebhookController::class, 'handle'])->name('webhooks.handle')->middleware('throttle:60,1');

/*
|--------------------------------------------------------------------------
| Authenticated routes (any role)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'throttle:120,1'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Account settings (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notification preferences
    Route::get('/settings/notifications', [SettingsController::class, 'edit'])->name('settings.notifications.edit');
    Route::put('/settings/notifications', [SettingsController::class, 'update'])->name('settings.notifications.update');

    // Privacy / data export
    Route::get('/privacy/export', [PrivacyController::class, 'export'])->name('privacy.export');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Messaging
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::middleware('throttle:30,1')->group(function () {
        Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/messages/start', [MessageController::class, 'start'])->name('messages.start');
    });

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Bookings (student creates; both parties can view/cancel; tutor completes)
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store')->middleware('throttle:20,1');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking}/attendance', [AttendanceController::class, 'update'])->name('bookings.attendance');
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->name('bookings.review');
    Route::post('/reviews/{review}/respond', [ReviewController::class, 'respond'])->name('reviews.respond');

    // Course enrollment (guarded to students inside the controller)
    Route::post('/courses/{course:slug}/enroll', [EnrollmentController::class, 'store'])->name('courses.enroll');

    // Payments
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
    Route::get('/payments/{payment}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::get('/payments/{payment}/redirect', [PaymentController::class, 'redirect'])->name('payments.redirect');
    Route::get('/payments/{payment}/return', [PaymentController::class, 'return'])->name('payments.return');
    Route::get('/payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    // Safety: report & block
    Route::post('/reports', [ContentReportController::class, 'store'])->name('reports.store')->middleware('throttle:10,1');
    Route::post('/users/{user}/block', [BlockController::class, 'store'])->name('users.block');
    Route::delete('/users/{user}/block', [BlockController::class, 'destroy'])->name('users.unblock');

    // Secure private file access
    Route::get('/files/identity/{tutorProfile}', [SecureFileController::class, 'identityDocument'])->name('files.identity');
    Route::get('/files/certificate/{certificate}', [SecureFileController::class, 'certificate'])->name('files.certificate');
    Route::get('/files/message-attachment/{message}', [SecureFileController::class, 'messageAttachment'])->name('files.message-attachment');

    /*
    |----------------------------------------------------------------------
    | Student area
    |----------------------------------------------------------------------
    */
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [StudentProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [StudentProfileController::class, 'update'])->name('profile.update');
        Route::get('/bookings', [StudentBookingController::class, 'index'])->name('bookings.index');
        Route::get('/courses', [EnrollmentController::class, 'index'])->name('courses.index');
        Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
        Route::get('/reports', [StudentReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [StudentReportController::class, 'export'])->name('reports.export');
    });

    /*
    |----------------------------------------------------------------------
    | Tutor area
    |----------------------------------------------------------------------
    */
    Route::middleware('role:tutor')->prefix('tutor')->name('tutor.')->group(function () {
        Route::get('/dashboard', [TutorDashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [TutorProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [TutorProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/subjects', [TutorProfileController::class, 'updateSubjects'])->name('profile.subjects');
        Route::post('/profile/certificates', [TutorProfileController::class, 'storeCertificate'])->name('profile.certificates.store');
        Route::delete('/profile/certificates/{certificate}', [TutorProfileController::class, 'destroyCertificate'])->name('profile.certificates.destroy');

        Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
        Route::post('/availability', [AvailabilityController::class, 'store'])->name('availability.store');
        Route::delete('/availability/{availability}', [AvailabilityController::class, 'destroy'])->name('availability.destroy');
        Route::post('/time-off', [AvailabilityController::class, 'storeTimeOff'])->name('time-off.store');
        Route::delete('/time-off/{timeOff}', [AvailabilityController::class, 'destroyTimeOff'])->name('time-off.destroy');

        Route::get('/courses', [TutorCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [TutorCourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [TutorCourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}/edit', [TutorCourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [TutorCourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [TutorCourseController::class, 'destroy'])->name('courses.destroy');

        Route::get('/bookings', [TutorBookingController::class, 'index'])->name('bookings.index');
        Route::put('/bookings/{booking}/notes', [TutorStudentController::class, 'updateNotes'])->name('bookings.notes');

        Route::get('/students', [TutorStudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [TutorStudentController::class, 'show'])->name('students.show');

        Route::get('/payouts', [TutorPayoutController::class, 'index'])->name('payouts.index');
        Route::post('/payouts', [TutorPayoutController::class, 'store'])->name('payouts.store');

        Route::get('/reports', [TutorReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [TutorReportController::class, 'export'])->name('reports.export');
    });

    /*
    |----------------------------------------------------------------------
    | Admin area
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::put('/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.status');

        Route::get('/tutor-applications', [TutorApplicationController::class, 'index'])->name('tutor-applications.index');
        Route::get('/tutor-applications/{tutorProfile}', [TutorApplicationController::class, 'show'])->name('tutor-applications.show');
        Route::post('/tutor-applications/{tutorProfile}/approve', [TutorApplicationController::class, 'approve'])->name('tutor-applications.approve');
        Route::post('/tutor-applications/{tutorProfile}/reject', [TutorApplicationController::class, 'reject'])->name('tutor-applications.reject');
        Route::put('/certificates/{certificate}/verify', [TutorApplicationController::class, 'verifyCertificate'])->name('certificates.verify');

        Route::get('/courses', [CourseModerationController::class, 'index'])->name('courses.index');
        Route::post('/courses/{course}/approve', [CourseModerationController::class, 'approve'])->name('courses.approve');
        Route::post('/courses/{course}/reject', [CourseModerationController::class, 'reject'])->name('courses.reject');
        Route::post('/courses/{course}/archive', [CourseModerationController::class, 'archive'])->name('courses.archive');

        Route::get('/reviews', [ReviewModerationController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/toggle', [ReviewModerationController::class, 'toggleVisibility'])->name('reviews.toggle');
        Route::delete('/reviews/{review}', [ReviewModerationController::class, 'destroy'])->name('reviews.destroy');

        Route::get('/reports', [AdminContentReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/{report}/resolve', [AdminContentReportController::class, 'resolve'])->name('reports.resolve');
        Route::post('/reports/{report}/dismiss', [AdminContentReportController::class, 'dismiss'])->name('reports.dismiss');

        Route::get('/subjects', [AdminSubjectController::class, 'index'])->name('subjects.index');
        Route::post('/subject-categories', [AdminSubjectController::class, 'storeCategory'])->name('subject-categories.store');
        Route::post('/subjects', [AdminSubjectController::class, 'storeSubject'])->name('subjects.store');
        Route::post('/subjects/{subject}/toggle', [AdminSubjectController::class, 'toggleSubject'])->name('subjects.toggle');
        Route::post('/subjects/{subject}/syllabus', [AdminSubjectController::class, 'uploadSyllabus'])->name('subjects.syllabus.upload');
        Route::delete('/subjects/{subject}/syllabus', [AdminSubjectController::class, 'destroySyllabus'])->name('subjects.syllabus.destroy');
        Route::delete('/subjects/{subject}', [AdminSubjectController::class, 'destroySubject'])->name('subjects.destroy');

        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('payments.refund');

        Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
        Route::post('/payouts/{payout}/mark-paid', [AdminPayoutController::class, 'markPaid'])->name('payouts.mark-paid');
        Route::post('/payouts/{payout}/reject', [AdminPayoutController::class, 'reject'])->name('payouts.reject');

        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');

        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log');
    });
});

require __DIR__.'/auth.php';
