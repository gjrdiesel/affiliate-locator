<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LocateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locate {--km=100}';

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
     * @return int
     */
    public function handle()
    {


        return 0;
    }
}
