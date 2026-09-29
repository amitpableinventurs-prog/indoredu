<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\TutorProfile;
use App\Models\User;
use App\Notifications\EnquiryNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    use RefreshDatabase;

    private function makeTutor(): User
    {
        $tutor = User::factory()->create(['role' => User::ROLE_TUTOR]);
        TutorProfile::create(['user_id' => $tutor->id, 'status' => TutorProfile::STATUS_APPROVED]);

        return $tutor;
    }

    private function sendEnquiry(User $student, User $tutor): Enquiry
    {
        $this->actingAs($student)->post('/enquiries', [
            'tutor_id' => $tutor->id,
            'questions' => ['trial', 'fees', 'fees'],
            'message' => 'Can you help me prepare for board exams?',
            'grade' => 'class_10',
            'preferred_mode' => 'online',
        ])->assertRedirect()->assertSessionHasNoErrors();

        return Enquiry::latest('id')->firstOrFail();
    }

    public function test_student_can_send_enquiry_and_tutor_is_notified(): void
    {
        Notification::fake();
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $tutor = $this->makeTutor();

        $enquiry = $this->sendEnquiry($student, $tutor);

        $this->assertSame(Enquiry::STATUS_PENDING, $enquiry->status);
        $this->assertSame($tutor->id, $enquiry->tutor_id);
        Notification::assertSentTo($tutor, EnquiryNotification::class, fn ($n) => $n->event === 'received');

        $this->assertSame(['fees', 'trial'], $enquiry->questions);
        $this->assertSame('General enquiry', $enquiry->title);
        $this->actingAs($tutor)->get('/enquiries')->assertOk()->assertSee('2 questions');
        $this->actingAs($tutor)->get("/enquiries/{$enquiry->id}")->assertOk()
            ->assertSee(Enquiry::QUESTIONS['fees'])->assertSee('Send answers');
        $this->actingAs($student)->get("/enquiries/{$enquiry->id}")->assertOk()->assertSee('Waiting for');
    }

    public function test_tutor_reply_connects_both_in_a_conversation(): void
    {
        Notification::fake();
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $tutor = $this->makeTutor();
        $enquiry = $this->sendEnquiry($student, $tutor);

        // Every ticked question must be answered.
        $this->actingAs($tutor)->post("/enquiries/{$enquiry->id}/reply", [
            'answers' => ['fees' => 'Rs 2000 / month'],
            'tutor_reply' => 'Yes, 3 classes a week.',
        ])->assertSessionHasErrors('answers.trial');

        $this->actingAs($tutor)->post("/enquiries/{$enquiry->id}/reply", [
            'answers' => ['fees' => 'Rs 2000 / month', 'trial' => 'First class is free'],
            'tutor_reply' => 'Yes, 3 classes a week.',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $enquiry->refresh();
        $this->assertSame(Enquiry::STATUS_REPLIED, $enquiry->status);
        $this->assertSame('First class is free', $enquiry->answers['trial']);
        $this->actingAs($student)->get("/enquiries/{$enquiry->id}")->assertOk()->assertSee('Rs 2000 / month');
        $this->assertNotNull($enquiry->conversation_id);
        $this->assertEqualsCanonicalizing(
            [$student->id, $tutor->id],
            $enquiry->conversation->participants->pluck('id')->all()
        );
        $this->assertSame(2, $enquiry->conversation->messages()->count());
        Notification::assertSentTo($student, EnquiryNotification::class, fn ($n) => $n->event === 'replied');

        $this->actingAs($student)->get("/messages/{$enquiry->conversation_id}")->assertOk();
        $this->actingAs($student)->post("/enquiries/{$enquiry->id}/close")->assertRedirect();
        $this->assertSame(Enquiry::STATUS_CLOSED, $enquiry->fresh()->status);
    }

    public function test_only_the_addressed_tutor_can_reply_and_outsiders_cannot_view(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $tutor = $this->makeTutor();
        $otherTutor = $this->makeTutor();
        $enquiry = $this->sendEnquiry($student, $tutor);

        $this->actingAs($otherTutor)->get("/enquiries/{$enquiry->id}")->assertForbidden();
        $this->actingAs($otherTutor)->post("/enquiries/{$enquiry->id}/reply", ['tutor_reply' => 'Hi'])->assertForbidden();
        $this->actingAs($student)->post("/enquiries/{$enquiry->id}/reply", ['tutor_reply' => 'Hi'])->assertForbidden();
    }

    public function test_student_sees_enquiry_button_on_tutor_profile(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $tutor = $this->makeTutor();

        $this->actingAs($student)->get("/tutors/{$tutor->tutorProfile->id}")
            ->assertOk()
            ->assertSee('Send enquiry');
    }

    public function test_tutor_can_decline_and_tutors_cannot_send_enquiries(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $tutor = $this->makeTutor();
        $enquiry = $this->sendEnquiry($student, $tutor);

        $this->actingAs($tutor)->post("/enquiries/{$enquiry->id}/decline", ['tutor_reply' => 'Not available'])->assertRedirect();
        $this->assertSame(Enquiry::STATUS_DECLINED, $enquiry->fresh()->status);

        $this->actingAs($tutor)->post('/enquiries', [
            'tutor_id' => $this->makeTutor()->id,
            'message' => 'y',
        ])->assertForbidden();

        // A student must tick a question or write one.
        $this->actingAs($student)->post('/enquiries', ['tutor_id' => $tutor->id])
            ->assertSessionHasErrors('message');
        $this->actingAs($student)->post('/enquiries', ['tutor_id' => $tutor->id, 'questions' => ['hack']])
            ->assertSessionHasErrors('questions.0');
    }
}
