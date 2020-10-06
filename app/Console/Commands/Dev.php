<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Parsers\YoutubeVideoParser;
use App\Lib\Tasks\UpsertTwitterUser;
use App\Lib\Tasks\UpsertYoutubeChannel;
use App\Lib\Tasks\UpsertYoutubeVideo;

class Dev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'dev command';

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
        // $twitterIds = ['YaezawaNatori'];
        // $res = UpsertTwitterUser::runs($twitterIds);
        // var_dump($res->toArray());

        // $channelIds = ['UC1519-d1jzGiL1MPTxEdtSA'];
        // $res = UpsertYoutubeChannel::runs($channelIds);
        // var_dump($res->toArray());

        // $videoIds = ['gSnnGL2trao'];
        // $res = UpsertYoutubeVideo::runs($videoIds);
        // var_dump($res->map(function ($item) {
        //     return $item->title;
        // }));

        return 0;
    }
}
