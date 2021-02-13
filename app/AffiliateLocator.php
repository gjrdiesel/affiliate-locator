<?php

namespace App;

use Illuminate\Support\Collection;

class AffiliateLocator extends Collection
{
    const MEASUREMENTS = [
        'm' => 6371009,
        "km" => 6371.009,
        "mi" => 3958.761,
        "nm" => 3440.070,
        "yd" => 6967420,
        "ft" => 20902260,
    ];

    protected string $measurement = 'km';

    /**
     * Load affiliates from a file
     *
     * @param string $path
     * @return Collection
     * @throws \Exception
     */
    public function loadFile(string $path): Collection
    {
        if (!is_file($path)) {
            throw new \Exception("Invalid path: $path");
        }

        $data = file_get_contents($path);

        if ($data === false) {
            throw new \Exception("Error loading file: $path");
        }

        return $this->loadString($data);
    }

    /**
     * Load affiliates from a string
     *
     * @param string $data
     * @return AffiliateLocator
     */
    public function loadString(string $data)
    {
        return (new self(
            explode("\n", $data)
        ))->map(fn($line) => json_decode($line));
    }

    /**
     * Validate coordinates exist and are floats as we expect
     *
     * @param object $coordinates
     * @throws \Exception
     */
    private function validateCoordinates(object $coordinates)
    {
        if (!isset($coordinates->latitude, $coordinates->longitude)) {
            throw new \Exception("Missing latitude,longitude from " . json_encode($coordinates));
        }
        if (count(array_filter([(float)$coordinates->latitude, (float)$coordinates->longitude], fn($value) => $value === 0.0)) > 0) {
            throw new \Exception("Invalid latitude,longitude from " . json_encode($coordinates));
        }
    }

    /**
     * Adds a "distance" property to each item in the collection based on a set of coordinates
     *
     * @param array|null $coordinates
     * @return AffiliateLocator
     */
    public function calculateDistance(?array $coordinates = null)
    {
        if (!$coordinates) {
            $coordinates = config('kax-media.office.coordinates');
        }

        $this->validateCoordinates((object)$coordinates);
        $coordinates = array_map('deg2rad', $coordinates);

        return $this->map(function ($row) use ($coordinates) {

            $this->validateCoordinates($row);

            $latitude = deg2rad($row->latitude);
            $longitude = deg2rad($row->longitude);

            $lonDelta = $longitude - $coordinates['longitude'];
            $a = pow(cos($latitude) * sin($lonDelta), 2) + pow(cos($coordinates['latitude']) * sin($latitude) - sin($coordinates['latitude']) * cos($latitude) * cos($lonDelta), 2);
            $b = sin($coordinates['latitude']) * sin($latitude) + cos($coordinates['latitude']) * cos($latitude) * cos($lonDelta);
            $angle = atan2(sqrt($a), $b);

            $row->distance = $angle * self::MEASUREMENTS[$this->measurement];

            return $row;
        });
    }

    /**
     * Takes a distance string like "100km" or "2000mi" and a set of coordinates
     * and filters all items within the collection to being _within_ that distance
     *
     * @param string $distanceString
     * @param array|null $coordinates
     * @return $this
     * @throws \Exception
     */
    public function within(string $distanceString, ?array $coordinates = null): self
    {
        $distance = $this->convertDistanceString($distanceString);

        return $this
            ->calculateDistance($coordinates)
            ->filter(fn($row) => $distance > $row->distance);
    }

    /**
     * Takes the "100km" string and extracts the 100 into distance and validates then stores
     * the measurement to use as "km"
     *
     * @param string $distanceString
     * @return int
     * @throws \Exception
     */
    private function convertDistanceString(string $distanceString)
    {
        $distance = intval($distanceString);
        $measurement = strtolower(str_replace($distance, '', $distanceString));

        if (!$measurement || !isset(self::MEASUREMENTS[$measurement])) {
            $measurements = implode(",", array_keys(self::MEASUREMENTS));
            throw new \Exception("Incorrect or missing measurement, please use one of the following: $measurements");
        }

        $this->measurement = $measurement;

        return $distance;
    }
}
