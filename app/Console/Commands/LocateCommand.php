<?php

namespace App\Console\Commands;

use App\AffiliateLocator;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class LocateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locate:within {distance=100km} {--latitude=} {--longitude=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Locate affiliates within certain distance of the office';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param AffiliateLocator $locator
     * @return int
     */
    public function handle(AffiliateLocator $locator)
    {
        $distance = $this->argument('distance');

        $coordinates = null;
        if ($this->option('latitude') || $this->option('longitude')) {
            $coordinates = Arr::only($this->options(), ['latitude', 'longitude']);
        }

        $affiliates = $locator
            ->loadFile(base_path('affiliates.txt'))
            ->within($distance, $coordinates)
            ->sortBy('affiliate_id')
            ->map(fn($affiliate) => [
                'Affiliate ID' => $affiliate->affiliate_id,
                'Name' => $affiliate->name,
                //'Distance' => $affiliate->distance, // For debugging
            ]);

        if ($affiliates->isEmpty()) {
            $this->info("No affiliates found within {$distance}");
            return 1;
        }

        $this->table(array_keys($affiliates->first()), $affiliates);

        return 0;
    }
}
