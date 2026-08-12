<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\TutorCertificate;
use App\Models\TutorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SecureFileController extends Controller
{
    public function identityDocument(Request $request, TutorProfile $tutorProfile)
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->id === $tutorProfile->user_id, 403);
        abort_unless($tutorProfile->identity_document, 404);

        return Storage::disk('local')->response($tutorProfile->identity_document);
    }

    public function certificate(Request $request, TutorCertificate $certificate)
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->id === $certificate->tutorProfile->user_id, 403);

        return Storage::disk('local')->response($certificate->file_path);
    }

    public function messageAttachment(Request $request, Message $message)
    {
        $user = $request->user();
        $isParticipant = $message->conversation->participants()->where('user_id', $user->id)->exists();
        abort_unless($user->isAdmin() || $isParticipant, 403);
        abort_unless($message->attachment_path, 404);

        return Storage::disk('local')->response($message->attachment_path, $message->attachment_name);
    }
}
