<?php

namespace Tests\Unit;

use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function test_user_role_enum_cases(): void
    {
        $this->assertEquals(['SuperAdmin', 'Admin', 'Accountant'], UserRoleEnum::values());
    }

    public function test_user_status_enum_cases(): void
    {
        $this->assertEquals(['Active', 'Suspended'], UserStatusEnum::values());
    }
}
