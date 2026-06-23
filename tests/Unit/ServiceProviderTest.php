<?php

namespace YenaingtunDev\MMQR\Tests\Unit;

use Illuminate\Support\ServiceProvider;
use YenaingtunDev\MMQR\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_config_is_merged(): void
    {
        $this->assertSame('458', config('mmqr.currency'));
        $this->assertSame('MY', config('mmqr.country_code'));
        $this->assertSame('static', config('mmqr.mode'));
    }

    public function test_config_is_publishable(): void
    {
        $this->assertContains('mmqr-config', ServiceProvider::publishableGroups());

        $paths = ServiceProvider::pathsToPublish(null, 'mmqr-config');

        $this->assertNotEmpty($paths);
        $this->assertContains(config_path('mmqr.php'), $paths);
    }
}
