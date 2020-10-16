<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Tasks\UpsertYoutubeVideo;
use App\Lib\Tasks\Utils\GeneralEvents;
use App\Models\Video;

class UpsertYoutubeVideoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:video
        { --force : Create when there is no channel }
        { --all : Upsert All Record in DB }
        { ids?* : Youtube videoID (?v=xxx) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upsert youtube video';

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
        $force = $this->option('force'); // true で channel が無いとき create する

        // ID が指定されていなければ DB 全てを対象とする
        $ids = $this->argument('ids') ?? [];
        if ($all) {
            $ids = Video::select(['code'])->get()
                ->pluck('code')
                ->toArray();
        }

        UpsertYoutubeVideo::builder()
            ->notExistChannel($force)
            ->addEvents(GeneralEvents::arrayTaskEvents('Upsert youtube videos'))
            ->exec($ids);

        return 0;
    }
}
