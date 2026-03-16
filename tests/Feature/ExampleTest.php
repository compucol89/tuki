<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
  public function test_application_bootstraps()
  {
    $this->assertTrue(app()->bound('router'));
    $this->assertTrue(app()->bound('view'));
  }
}
