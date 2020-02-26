<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Import;

class ImportsClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imports:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        $this->clearOldRecords();
    }

    public function clearOldRecords()
    {
        $date = Carbon::now()->subDays(2);
        $this->info("\n\nRemove data from import logs table older than ====>>> " . $date->format('Y-m-d H:i:s'));

        $query        = Import::where('created_at', '<', $date);
        $recordsCount = $query->count();
        $bar          = $this->output->createProgressBar($recordsCount);
        $iterator     = 0;

        $query->delete();

        $bar->advance();
        $bar->finish();

        $this->info("\nCompleted\n");
    }
}
