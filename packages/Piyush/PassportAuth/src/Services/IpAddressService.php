<?php

namespace Piyush\PassportAuth\Services;

use Illuminate\Http\Request;

class IpAddressService
{
    public function __construct(protected ?Request $request = null) {}

    public function address(): ?string
    {
        $request = $this->request ?: request();
        $ip = $request->getClientIp();
        return $this->normalize($ip);
    }

    public function all(): array
    {
        $request = $this->request ?: request();
        return array_values(array_unique(array_filter(array_map(
            fn ($ip) => $this->normalize($ip),
            $request->getClientIps()
        ))));
    }

    public function isPrivateOrLocal(?string $ip = null): bool
    {
        $ip = $ip ?: $this->address();
        return $ip !== null && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    protected function normalize(?string $ip): ?string
    {
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }
        return strtolower($ip);
    }
}
