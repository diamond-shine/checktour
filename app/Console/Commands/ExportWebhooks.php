<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExportWebhooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:webhooks';

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
        $createdBooking = app('Bookeo')->setWebook($webhook = [
            'id' => 'booking_created',
            'url' => route(config('services.bookeo.listener_url_booking_created')),
            'domain' => 'bookings',
            'type' => 'created'
        ]);

        dump($createdBooking);

        $updatedBooking = app('Bookeo')->setWebook($webhook = [
            'id' => 'booking_updated',
            'url' => route(config('services.bookeo.listener_url_booking_updated')),
            'domain' => 'bookings',
            'type' => 'updated'
        ]);

        dump($updatedBooking);

        $deletedBooking = app('Bookeo')->setWebook($webhook = [
            'id' => 'booking_deleted',
            'url' => route(config('services.bookeo.listener_url_booking_deleted')),
            'domain' => 'bookings',
            'type' => 'deleted'
        ]);

        dd($deletedBooking);
    }
}
