<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use App\BuyerSellerMeeting;
use Illuminate\Console\Command;

class MeetingCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:meetingcron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $meeting = BuyerSellerMeeting::where('meeting_date', '<', Carbon::now())->where('meeting_time', '>', now()->format('H:i:s'))->get();
        if (count($meeting) > 0) {
            foreach ($meeting as $met) {
                $met->update([
                    'status' => 4,
                    'meeting_link'=>"Link Expired"
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
