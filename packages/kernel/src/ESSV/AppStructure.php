<?php

namespace Shelter\Kernel\ESSV;

use Illuminate\Foundation\Application;

class AppStructure
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        // public path
        $this->app->instance(
            'path.public',
            $this->app->basePath() . DIRECTORY_SEPARATOR . 'static'
        );

        // storage path
        $this->app->useStoragePath(
            $this->app->basePath() . DIRECTORY_SEPARATOR . 'var'
        );
    }
}
