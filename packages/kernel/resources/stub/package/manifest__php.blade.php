use Shelter\Kernel\Packages\Manifest\Factory as ManifestFactory;

@verbatim/** @var ManifestFactory $factory */@endverbatim
return $factory->hasMigrations()@if($hasConfig)->config('{{ Str::singular($name) }}', 'shelter.{{ Str::singular($name) }}')@endif;
