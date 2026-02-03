<?php

declare(strict_types=1);

namespace Rosolovsky\MoonshineGmapField\Fields;

use MoonShine\UI\Fields\Field;
use Closure;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use Illuminate\Contracts\Support\Renderable;

class Gmap extends Field
{
    protected string $view = 'gmap::fields.gmap';

    protected ?int $zoom = null;
    protected ?int $maxZoom = null;
    protected ?int $minZoom = null;

    public function zoom(int $zoom): static
    {
        $this->zoom = $zoom;
        return $this;
    }

    public function maxZoom(int $maxZoom): static
    {
        $this->maxZoom = $maxZoom;
        return $this;
    }

    public function minZoom(int $minZoom): static
    {
        $this->minZoom = $minZoom;
        return $this;
    }

    public function getZoom(): int
    {
        return $this->zoom;
    }

    public function getMinZoom(): int
    {
        return $this->minZoom;
    }

    public function getMaxZoom(): int
    {
        return $this->maxZoom;
    }

    protected function resolveBeforeApply(mixed $data): mixed
    {
        $requestData = request()->all();
        $column = $this->getColumn();
        if (isset($requestData["{$column}_lat"]) && isset($requestData["{$column}_lng"])) {
            $location = [
                'lat' => (float) $requestData["{$column}_lat"],
                'lng' => (float) $requestData["{$column}_lng"]
            ];
            data_set($data, $column, $location);
        }
        return $data;
    }

    protected function viewData(): array
    {
        $location = $this->toValue();
        $latitude = is_array($location) && isset($location['lat']) ? $location['lat'] : config('moonshine-gmap.default_latitude');
        $longitude = is_array($location) && isset($location['lng']) ? $location['lng'] : config('moonshine-gmap.default_longitude');
        return [
            'apiKey' => config('moonshine-gmap.api_key'),
            'language' => config('moonshine-gmap.language'),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'zoom' => $this->getZoom(),
            'minZoom' => $this->getMinZoom(),
            'maxZoom' => $this->getMaxZoom(),
        ];
    }
}
