<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use App\Services\StudentCertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mirrors Frontend\UserCertificateController, both backed by
 * StudentCertificateService.
 */
class CertificateController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly StudentCertificateService $certificates)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $certificates = $this->certificates->listFor($request->user('api'));

        return $this->ok([
            'certificates' => CertificateResource::collection($certificates),
            'pagination' => [
                'current_page' => $certificates->currentPage(),
                'last_page' => $certificates->lastPage(),
                'per_page' => $certificates->perPage(),
                'total' => $certificates->total(),
            ],
        ]);
    }

    public function show(Request $request, int $certificate): JsonResponse
    {
        $found = $this->certificates->findOwned($request->user('api'), $certificate);

        return $this->ok(new CertificateResource($found));
    }
}
