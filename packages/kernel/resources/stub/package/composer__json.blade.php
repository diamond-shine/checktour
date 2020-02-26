{
    "name": "shelter/{{ $name }}",
    "license": "proprietary",
    "autoload": {
        "psr-4": {
            "Shelter\\{{ Str::studly($name) }}\\": "src/"
        }
    },
    "extra": {
        "shelter": {
            "providers": [
                "Shelter\\{{ Str::studly($name) }}\\Providers\\{{ Str::studly($name) }}ServiceProvider"
            ]
        }
    }
}
