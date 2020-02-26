<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\ImportBooking;
use App\Models\Import;
use Carbon\Carbon;

class ImportBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:bookings';

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
        $this->info("\nImporting bookings");

        $startTime = Carbon::createMidnightDate()
            ->toIso8601String();

        // $endTime = Carbon::createFromFormat('Y-m-d H:i:s', '2019-11-16 00:00:01')
        $endTime = Carbon::now()->add(2, 'day')->toIso8601String();

        $import = Import::create([
            'type' => 'Automatic (cron)'
        ]);

        $responceData = app('Bookeo')->getBookings([
            'startTime'       => $startTime,
            'endTime'         => $endTime,
            'expand_customer' => 'true'
        ]);

        $bookings = $responceData['data'] ?? $responceData['data'];

        $updated = 0;
        $created = 0;

        foreach ($bookings as $item) {
            if (!ImportBooking::handle($item)) {
                ImportBooking::handleUpdate($item);
                $updated++;
            } else {
                $created++;
            }
        }

        $import->fill([
            'updated' => $updated,
            'created' => $created,
            'status'  => 'completed'
        ])->save();

        $bar = $this->output->createProgressBar(count($bookings));

        $bar->advance();
        $bar->finish();
        $this->info("\nImporting bookings portion completed\n\n");
    }
}
