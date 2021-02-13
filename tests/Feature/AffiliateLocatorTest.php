<?php

namespace Tests\Feature;

use Facades\App\AffiliateLocator;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AffiliateLocatorTest extends TestCase
{
    public function test_loads_json_affiliates()
    {
        $affiliates = AffiliateLocator::loadString('{"latitude": "52.986375", "affiliate_id": 12, "name": "Yosef Giles", "longitude": "-6.043701"}');

        $this->assertEquals('Yosef Giles', $affiliates[0]->name);
    }

    public function test_loads_file()
    {
        $affiliates = AffiliateLocator::loadFile(base_path('affiliates.txt'));
        $this->assertNotEmpty($affiliates);
        $this->assertEquals('Lance Keith', $affiliates->skip(1)->first()->name);
    }

    public function test_example_file()
    {
        Artisan::call('locate:within', ['distance' => '10km']);
        $this->assertEquals(
            "+--------------+------------+\n" .
            "| Affiliate ID | Name       |\n" .
            "+--------------+------------+\n" .
            "| 4            | Inez Blair |\n" .
            "+--------------+------------+\n", Artisan::output());
    }

    public function test_empty_results()
    {
        Artisan::call('locate:within', ['distance' => '1ft']);
        $this->assertEquals("No affiliates found within 1ft\n", Artisan::output());
    }

    public function test_handles_missing_option()
    {
        $this->expectErrorMessage('Missing latitude,longitude from {"latitude":null,"longitude":1}');

        Artisan::call('locate:within', ['distance' => '1ft', '--longitude' => 1]);
        $this->assertEquals("No affiliates found within 1ft\n", Artisan::output());
    }

    public function test_handles_missing_coordinates()
    {
        $this->expectErrorMessage('Missing latitude,longitude from {"affiliate_id":12,"name":"Yosef Giles","longitude":"-6.043701"}');

        AffiliateLocator::loadString(json_encode([
            'affiliate_id' => 12,
            'name' => 'Yosef Giles',
            'longitude' => '-6.043701'
        ]))->within('100km');
    }

    public function test_handles_invalid_coordinates()
    {
        $this->expectErrorMessage('Invalid latitude,longitude from {"latitude":"","affiliate_id":12,"name":"Yosef Giles","longitude":"-6.043701"}');

        AffiliateLocator::loadString(json_encode([
            'latitude' => '',
            'affiliate_id' => 12,
            'name' => 'Yosef Giles',
            'longitude' => '-6.043701'
        ]))->within('100km');
    }
}
