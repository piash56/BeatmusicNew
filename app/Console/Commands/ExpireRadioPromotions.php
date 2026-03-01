<?php

namespace App\Console\Commands;

use App\Models\RadioPromotion;
use Illuminate\Console\Command;

class ExpireRadioPromotions extends Command
{
    protected $signature = 'radio:expire-promotions';

    protected $description = 'Set radio promotions to finished when finish_date has passed';

    public function handle(): int
    {
        $count = RadioPromotion::where('status', 'published')
            ->where('finish_date', '<=', now())
            ->update(['status' => 'finished']);

        $this->info("Expired {$count} radio promotion(s).");

        return self::SUCCESS;
    }
}
