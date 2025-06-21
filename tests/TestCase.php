<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $installed = storage_path('installed');
        if (! file_exists($installed)) {
            file_put_contents($installed, 'installed');
        }
    }
}
