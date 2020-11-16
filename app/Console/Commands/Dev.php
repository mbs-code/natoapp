<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Parsers\YoutubeVideoParser;
use App\Lib\Tasks\UpsertTwitterUser;
use App\Tasks\Youtubes\UpsertYoutubeVideo;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Enums\VideoType;
use App\Helpers\Helper;
use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\Tests\FizzBuzzTask;
use App\Lib\Tasks\AddProfileFromYoutubeChannel;
use App\Lib\Tasks\AddYoutubePlaylist;
use App\Models\Profile;
use App\Models\Youtube;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use App\Lib\TaskBuilder\Events\DebugTaskEventer;
use App\Lib\TaskBuilder\Events\DebugBlackTaskEventer;
use App\Lib\TaskBuilder\Utils\EventRecord;
use App\Lib\TaskBuilder\Utils\EventUtil;
use App\Tasks\Twitters\CheckTwitterTweet;
use App\Tasks\Utils\GeneralEvents;
use App\Tasks\Youtubes\CheckYoutubeFeed;
use App\Tasks\Youtubes\UpsertYoutubeChannel;

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


        // Artisan::call(YoutubeVideo::class, [
        //     '--all' => true,
        //     '--before' => 24,
        //     '--after' => 0,
        //     // '--after' => 16,
        //     '--dump' => true,
        //     // '--before' => 2,
        //     // '--range' => 1,
        //     // '--type' => [VideoType::LIVE(), VideoType::UPCOMING()],
        //     // '--skip' => true, // チャンネルが無かったらスキップ
        // ]);
        // $this->line(Artisan::output());

        // \DB::enableQueryLog();

        // $profile = Profile::where('id', 23)
        //     // ->with(['twitters', 'youtubes'])
        //     ->get()
        //     // ->append('published_at')
        //     ->append('youtube_subscribers')
        //     ->toArray();
        // // $profile = Profile::where('id', 23)->get();
        // dump($profile);

        // \DB::transaction(function () {
        //     $profile = Profile::where('id', 23)->first();
        //     dump($profile);

        //     $profile->name = 'てすと';
        //     $profile->save();

        //     $profile->twitters()->sync([20, 23]);

        //     $profile->cacheSync();
        //     $profile->save();

        //     echo('-------------------------------'.PHP_EOL);
        //     dump($profile->toArray());
        //     dump(Helper::parseQueryLog());
        //     throw new Exception('stop');
        // });

        // $cids = Youtube::select(['code'])->get()
        //     ->pluck('code')
        //     ->toArray();
        $vids = ['gSnnGL2trao', '_vgQK5-1w6M'];
        $cids = ['UCIdEIHpS0TdkqRkHL5OkLtA', 'UCP9ZgeIJ3Ri9En69R0kJc9Q'];
        $tids = ['minatoaqua'];

        // $res = UpsertYoutubeVideo::builder()
            // ->addEvents($events)
            // ->dumpColor()
            // ->skipExistVideo()
            // ->addEvents(GeneralEvents::apiEvents('Upsert youtube video'))
            // ->exec($vids);
            // ->exec($tcids, new DebugBlackTaskEventer());
            // ->exec($cids);

        $res = CheckTwitterTweet::builder()
            ->addEvents(GeneralEvents::apiEvents('Check twitter tweet'))
            ->exec($tids);

        echo('---------'.PHP_EOL);
        dump($res);
        // echo(json_encode($res->toArray()).PHP_EOL);

        return 0;
    }
}
