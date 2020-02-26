<?php

return $factory->hasMigrations()->config('guard', 'shelter.guard')->commands([
    \Shelter\Guard\Commands\CreateRole::class,
    \Shelter\Guard\Commands\CreateUser::class,
]);
