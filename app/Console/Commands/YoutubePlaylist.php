<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Tasks\Utils\GeneralEvents;
use App\Models\Youtube;
use App\Tasks\Youtubes\CheckYoutubePlaylist;

class YoutubePlaylist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:playlist
        { --force : Create when there\'s no channel }
        { --skip : Skip when there\'s no channel }
        { --nonexist : Skip when exist in DB }
        { --all : Upsert All Record in DB }
        { --loop= : Max loop count (0: all playlist item)}
        { ids?* : Youtube channelID (UCxxx) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check youtube playlist';

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
        $all = $this->option('all'); // true で DB の値を対象に upsert する
        $ids = $this->argument('ids') ?? []; // channel ids

        $loop = $this->option('loop'); // 最大ループ数, 0で全て
        $maxLoop = $loop !== false ? intval($loop) : 1; // 初期値は 1

        // ID が指定されていなければ DB 全てを対象とする
        if ($all) {
            $ids = Youtube::select(['code'])->get()
                ->pluck('code')
                ->toArray();
        }

        $items = CheckYoutubePlaylist::builder()
            ->maxLoop($maxLoop)
            ->addEvents(GeneralEvents::apiEvents('Check youtube playlist'))
            ->exec($ids);

        // video 処理 command へ渡す
        logger()->notice('== pipe =>');
        Artisan::call(YoutubeVideo::class, [
            'ids' => $items,
            '--force' => $this->option('force'),
            '--skip' => $this->option('skip'),
            '--nonexist' => $this->option('nonexist'),
        ]);

        return 0;
    }
}
