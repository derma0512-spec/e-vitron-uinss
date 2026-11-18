<?php

namespace Tests;

use App\Models\Setting;
use App\Models\Submission;
use App\Models\Videotron;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Setting::clearCache();
        Submission::clearRuntimeCache();
        Videotron::clearRuntimeCache();
    }
}
