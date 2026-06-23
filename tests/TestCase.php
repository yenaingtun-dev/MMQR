<?php

namespace YenaingtunDev\MMQR\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use YenaingtunDev\MMQR\MMQRServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MMQRServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        //
    }
}
