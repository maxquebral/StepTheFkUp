<?php
declare(strict_types=1);

namespace StepTheFkUp\Pagination\Tests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Mockery\MockInterface;

abstract class AbstractLaravelProvidersTestCase extends AbstractTestCase
{
    /**
     * Mock Foundation application with minimum required calls.
     *
     * @return \Mockery\MockInterface
     */
    protected function mockApp(): MockInterface
    {
        return $this->mock(Application::class, function (MockInterface $app): void {
            $config = new class () {
                public function get(string $key, $default = null) {
                    if ($default !== null) {
                        return $default;
                    }

                    return [$key];
                }
            };

            $app->shouldReceive('get')->twice()->with('config')->andReturn($config);
            $app->shouldReceive('get')
                ->once()
                ->with('request')
                ->andReturn(new Request([], [], [], [], [], ['ORIG_PATH_INFO' => 'https://google.com']));
        });
    }
}