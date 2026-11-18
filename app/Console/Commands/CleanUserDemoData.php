<?php

namespace App\Console\Commands;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Console\Command;

class CleanUserDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sipetron:clean-user-demo-data {identifier=2388010023} {--prodi= : Update unit_or_prodi pengguna}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan data dummy pengajuan dari akun pengguna tertentu';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $identifier = (string) $this->argument('identifier');
        $user = User::where('identifier', $identifier)->first();

        if (! $user) {
            $this->error("Pengguna dengan identifier {$identifier} tidak ditemukan.");

            return self::FAILURE;
        }

        $submissions = Submission::where('user_id', $user->id)->get();
        $count = $submissions->count();

        foreach ($submissions as $sub) {
            $sub->videotrons()->detach();
            $sub->statusHistories()->delete();
            $sub->revisions()->delete();
            $sub->delete();
        }

        // Hapus notifikasi dummy lama
        $user->notifications()->delete();

        if ($prodi = $this->option('prodi')) {
            $user->unit_or_prodi = (string) $prodi;
            $user->save();
            $this->info("Unit/Prodi pengguna {$user->name} berhasil diperbarui menjadi {$prodi}.");
        }

        $this->info("Berhasil memproses akun {$user->name} ({$identifier}).");

        return self::SUCCESS;
    }
}
