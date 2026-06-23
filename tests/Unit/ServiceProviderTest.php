<?php

namespace YenaingtunDev\MMQR\Tests\Unit;

use Illuminate\Support\ServiceProvider;
use YenaingtunDev\MMQR\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_config_is_merged(): void
    {
        $this->assertSame('uat', config('mmqr.environment'));
        $this->assertSame('MMK', config('mmqr.currency'));
        $this->assertArrayHasKey('access_token_url', config('mmqr.uat'));
        $this->assertArrayHasKey('qr_url', config('mmqr.production'));
    }

    public function test_config_is_publishable(): void
    {
        $this->assertContains('mmqr-config', ServiceProvider::publishableGroups());

        $paths = ServiceProvider::pathsToPublish(null, 'mmqr-config');

        $this->assertNotEmpty($paths);
        $this->assertContains(config_path('mmqr.php'), $paths);
    }

    public function test_mmqr_service_is_registered(): void
    {
        $this->assertTrue($this->app->bound(\YenaingtunDev\MMQR\MMQRService::class));
    }
}
