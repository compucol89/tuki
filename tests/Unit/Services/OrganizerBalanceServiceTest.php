<?php

namespace Tests\Unit\Services;

use App\Models\Organizer;
use App\Services\OrganizerBalanceService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrganizerBalanceServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('organizers')) {
            Schema::create('organizers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('email')->unique();
                $table->string('username')->nullable();
                $table->string('password')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function test_credit_accumulates_balance_using_price_minus_commission(): void
    {
        $organizer = Organizer::forceCreate([
            'email' => 'org@example.test',
            'username' => 'org1',
            'password' => 'secret',
            'amount' => 100,
        ]);

        $service = new OrganizerBalanceService();

        $service->credit(['organizer_id' => $organizer->id, 'price' => 200, 'commission' => 20]);
        $service->credit(['organizer_id' => $organizer->id, 'price' => 50, 'commission' => 0]);

        $this->assertSame(330.0, (float) $organizer->fresh()->amount);
    }

    public function test_credit_is_noop_for_unknown_organizer(): void
    {
        $service = new OrganizerBalanceService();

        $service->credit(['organizer_id' => 999999, 'price' => 100, 'commission' => 10]);

        $this->assertSame(0, Organizer::count());
    }
}
