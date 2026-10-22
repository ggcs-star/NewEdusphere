<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Lists/shows a student's own issued certificates — shared by
 * Frontend\UserCertificateController and Api\V1\CertificateController.
 * Certificate *issuance* itself is CertificateIssuanceService's job.
 */
class StudentCertificateService
{
    public function listFor(User $user, int $perPage = 9): LengthAwarePaginator
    {
        return Certificate::with(['course', 'template'])
            ->where('user_id', $user->id)
            ->latest('issued_at')
            ->paginate($perPage);
    }

    /**
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOwned(User $user, int $certificateId): Certificate
    {
        $certificate = Certificate::with(['course', 'template', 'user'])
            ->findOrFail($certificateId);

        // Students can only open their own certificates.
        abort_unless((int) $certificate->user_id === (int) $user->id, 403);

        return $certificate;
    }
}
