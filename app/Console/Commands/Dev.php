<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Parsers\YoutubeVideoParser;
use App\Lib\Tasks\UpsertTwitterUser;
use App\Lib\Tasks\UpsertYoutubeChannel;
use App\Lib\Tasks\UpsertYoutubeVideo;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Enums\VideoType;
use App\Helpers\Helper;
use App\Lib\Tasks\AddProfileFromYoutubeChannel;
use App\Lib\Tasks\AddYoutubePlaylist;
use App\Models\Youtube;
use Illuminate\Support\Facades\Artisan;

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

        // $channelIds = ['UC1519-d1jzGiL1MPTxEdtSA'];
        // $res = UpsertYoutubeChannel::runs($channelIds);

        // $videoIds = ['gSnnGL2trao'];
        // $res = UpsertYoutubeVideo::runs($videoIds);

        // $channelID = 'UCIdEIHpS0TdkqRkHL5OkLtA';
        // $res = AddYoutubePlaylist::run($channelID);

        // $channelID = 'UCP9ZgeIJ3Ri9En69R0kJc9Q';
        // $res = AddProfileFromYoutubeChannel::run($channelID);
        // echo($res);

        // var_dump($res->map(function ($item) {
        //     return $item->title;
        // }));


        Artisan::call(YoutubeVideo::class, [
            '--all' => true,
            '--before' => 24,
            '--after' => 0,
            // '--after' => 16,
            '--dump' => true,
            // '--before' => 2,
            // '--range' => 1,
            // '--type' => [VideoType::LIVE(), VideoType::UPCOMING()],
            // '--skip' => true, // チャンネルが無かったらスキップ
        ]);
        $this->line(Artisan::output());

        return 0;
    }
}
