<?php

namespace Tests\Unit;

use App\Http\Requests\Event\StoreRequest;
use App\Http\Requests\Event\UpdateRequest;
use App\Models\Organizer;
use Illuminate\Support\Facades\Auth;
use ReflectionMethod;
use Tests\TestCase;

class EventStoreRequestTest extends TestCase
{
  public function test_prepare_for_validation_uses_authenticated_organizer_id(): void
  {
    $organizer = new Organizer();
    $organizer->id = 123;

    Auth::guard('organizer')->setUser($organizer);

    $request = StoreRequest::create('/organizer/event-store', 'POST');
    $method = new ReflectionMethod(StoreRequest::class, 'prepareForValidation');
    $method->setAccessible(true);
    $method->invoke($request);

    $this->assertSame(123, $request->input('organizer_id'));
  }

  public function test_update_prepare_for_validation_uses_authenticated_organizer_id(): void
  {
    $organizer = new Organizer();
    $organizer->id = 123;

    Auth::guard('organizer')->setUser($organizer);

    $request = UpdateRequest::create('/organizer/event-update', 'POST');
    $method = new ReflectionMethod(UpdateRequest::class, 'prepareForValidation');
    $method->setAccessible(true);
    $method->invoke($request);

    $this->assertSame(123, $request->input('organizer_id'));
  }
}
