<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Mail\BirthdayWish;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBirthdayNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:birthdays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía felicitaciones de cumpleaños a los clientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->format('m-d');
        $customers = Customer::whereRaw("DATE_FORMAT(birthday, '%m-%d') = ?", [$today])->get();

        foreach ($customers as $customer) {
            if ($customer->email) {
                Mail::to($customer->email)->send(new BirthdayWish($customer));
                $this->info("Enviado saludo a: {$customer->first_name}");
            }
        }

        $this->info('Proceso de notificaciones de cumpleaños completado.');
    }
}
