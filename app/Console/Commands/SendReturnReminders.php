<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Mail\ReturnReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendReturnReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-return-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email pengingat pengembalian buku 1 hari sebelum due date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        
        $borrowings = Borrowing::with('user', 'book')
            ->where('status', 'dipinjam')
            ->whereDate('due_date', $tomorrow)
            ->get();

        $count = 0;
        foreach ($borrowings as $borrowing) {
            Mail::to($borrowing->user->email)->send(new ReturnReminderMail($borrowing));
            $count++;
        }

        $this->info("Berhasil mengirim {$count} email pengingat.");
    }
}
