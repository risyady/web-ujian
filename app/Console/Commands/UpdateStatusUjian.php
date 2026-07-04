<?php

namespace App\Console\Commands;

use App\Models\Ujian;
use Illuminate\Console\Command;

class UpdateStatusUjian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ujian:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status ujian dari published ke ongoing dan ongoing ke finished';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        $ongoing = Ujian::where('status', 'published')
            ->whereDate('tanggal_ujian', $now->toDateString())
            ->where('waktu_mulai', $now->format('H:i:s'))
            ->update(['status' => 'ongoing']);

        $finished = Ujian::where('status', 'ongoing')
            ->whereDate('tanggal_ujian', $now->toDateString())
            ->where('waktu_selesai', $now->format('H:i:s'))
            ->update(['status' => 'finished']);

        $this->info("Ongoing: {$ongoing}, Finished: {$finished}");
    }
}
