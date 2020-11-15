<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Tasks\Youtubes\CheckYoutubeFeed;
use App\Tasks\Utils\GeneralEvents;
use App\Models\Youtube;

class YoutubeFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:feed
        { --force : Create when there\'s no channel }
        { --skip : Skip when there\'s no channel }
        { --nonexist : Skip when exist in DB }
        { --all : Upsert All Record in DB }
        { ids?* : Youtube channelID (UCxxx) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $all = $this->option('all'); // true で DB の値を対象に
        $ids = $this->argument('ids') ?? []; // channel ids

        // ID が指定されていなければ DB 全てを対象とする
        if ($all) {
            $ids = Youtube::select(['code'])->get()
                ->pluck('code')
                ->toArray();
        }

        // feed から video id を取得
        $links = CheckYoutubeFeed::builder()
            ->addEvents(GeneralEvents::apiEvents('Check youtube feed'))
            ->exec($ids);

        // video 処理 command へ渡す
        logger()->notice('== pipe =>');
        Artisan::call(YoutubeVideo::class, [
            'ids' => $links,
            '--force' => $this->option('force'),
            '--skip' => $this->option('skip'),
            '--nonexist' => $this->option('nonexist'),
        ]);

        return 0;
    }
}
