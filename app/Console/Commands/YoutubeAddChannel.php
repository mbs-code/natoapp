<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Tasks\UpsertYoutubeChannel;
use App\Lib\Tasks\AddProfileFromYoutubeChannel;

class YoutubeAddChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:add {--profile} {ids*} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add youtube channel
        {--profile : Create Profile. }
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
        $createProfile = $this->option('profile'); // true で profile 生成モード

        if ($createProfile) {
            foreach ($ids as $id) {
                $res = AddProfileFromYoutubeChannel::run($id);
                echo('profile: ['.$res->id.']'.$res->name);
            }
        } else {
            UpsertYoutubeChannel::getInstance(true)
            ->addEvent('inserted', function ($item, $index, $length) {
                $pref = '['.($index+1).'/'.$length.']insert: ';
                echo($pref.$item->code.' => ['.$item->id.']'.$item->name.' '.PHP_EOL);
            })
            ->execArray($ids);
        }

        return 0;
    }
}
