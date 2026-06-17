<?php

namespace App\Console\Commands;

use App\Actions\AutoUpdateBookings as AutoUpdateAction;
use Illuminate\Console\Command;

class AutoUpdateBookings extends Command
{
    protected $signature   = 'bookings:auto-update';
    protected $description = 'Aktifkan booking yang sudah melewati tanggal mulai, selesaikan yang sudah melewati tanggal selesai';

    public function handle(): int
    {
        $result = AutoUpdateAction::run();

        if ($result['activated'] > 0) {
            $this->info("✅ {$result['activated']} booking diaktifkan (status: confirmed → active)");
        }
        if ($result['completed'] > 0) {
            $this->info("🏁 {$result['completed']} booking diselesaikan (status: active → completed)");
        }
        if ($result['activated'] === 0 && $result['completed'] === 0) {
            $this->line('Tidak ada booking yang perlu diperbarui.');
        }

        return Command::SUCCESS;
    }
}
