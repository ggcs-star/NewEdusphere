<?php

namespace Piyush\PassportAuth\Tests;

use PHPUnit\Framework\TestCase;
use Piyush\PassportAuth\Services\LegacyPasswordService;

class PackageStructureTest extends TestCase
{
    public function test_legacy_sha1_and_md5_passwords_are_supported(): void
    {
        $service = new LegacyPasswordService();
        $this->assertTrue($service->check('secret123', sha1('secret123')));
        $this->assertTrue($service->check('secret123', md5('secret123')));
        $this->assertFalse($service->check('wrong', sha1('secret123')));
    }

    public function test_package_files_exist(): void
    {
        $root = dirname(__DIR__);
        foreach ([
            'composer.json',
            'config/passport-auth.php',
            'routes/api.php',
            'src/Services/IpAddressService.php',
            'src/Services/SecurityService.php',
            'database/migrations/2026_10_07_000001_create_passport_auth_security_tables.php',
            'database/migrations/2026_10_07_000002_add_email_verified_at_to_users.php',
            'database/migrations/2026_10_07_000003_upgrade_security_ip_and_audit_tables.php',
        ] as $file) {
            $this->assertFileExists($root . '/' . $file);
        }
    }
}
