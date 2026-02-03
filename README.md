# Moonshine 4 Google Map Field

Google Maps field for MoonShine v4.

## Installation

```bash
composer require rosolovsky/moonshine-gmap-field
```

## Publish config

```bash
php artisan vendor:publish --tag=moonshine-gmap-config
```

## Usage

As example 'location' column in table.

In model add:

```
protected $casts = [
    'location' => 'array'
];
```

```
use Rosolovsky\MoonshineGmapField\Fields\Gmap;

Gmap::make('Location', 'location')
    ->zoom(8)
    ->maxZoom(18)
    ->minZoom(5), 

```