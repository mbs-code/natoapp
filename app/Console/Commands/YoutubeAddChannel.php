<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Tasks\UpsertYoutubeChannel;

class YoutubeAddChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:add {ids*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add youtube channel
        {ids* : youtube channelid (UCxxx)}';

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
     * @return int
     */
    public function handle()
    {
        $ids = $this->argument('ids');
        UpsertYoutubeChannel::getInstance(true)
            ->addEvent('inserted', function ($item, $index, $length) {
                $pref = '['.($index+1).'/'.$length.']insert: ';
                echo($pref.$item->code.' => ['.$item->id.']'.$item->name.' '.PHP_EOL);
            })
            ->execArray($ids);

        return 0;
    }
}
