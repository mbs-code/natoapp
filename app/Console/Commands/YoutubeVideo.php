<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Tasks\UpsertYoutubeVideo;
use App\Lib\Tasks\Utils\GeneralEvents;
use App\lib\TimeUtil;
use App\Models\Video;

class YoutubeVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:video
        { --force : Create when there\'s no channel }
        { --skip : Skip when there\'s no channel }
        { --nonexist : Skip when exist in DB }
        { --all : Upsert All Record in DB }
        { --dump : dump video list (no updated) }
        { --before= : Range of hours to get from now (-1(all), 0(now), 1, 2, ...) }
        { --after= : Range of hours to get from now (-1(all), 0(now), 1, 2, ...) }
        { --type= : Type of video (VIDEO, UPCOMING, LIVE, ARCHIVE, PREMIERE) }
        { --status= : Status of video (PUBLIC, UNLISTED, PRIVATE, DELETE) }
        { ids?* : Youtube videoID (?v=xxx) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upsert youtube video (Default Before and after one hour)';

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
        $dump = $this->option('dump'); // true で 対象の DB の値を dump する

        $force = $this->option('force'); // true で channel が無いとき create する
        $skip = $this->option('skip'); // true で channel が無いとき skip する
        $nonexist = $this->option('nonexist'); // true で 存在しない video のみ対象
        $ids = $this->argument('ids') ?? []; // video ids

        // ID が指定されていなければ DB を対象とする
        if ($all || $dump) {
            $before = $this->option('before') ?? 24; // 現在から過去に向けて取得する範囲（hour) (default = 24hour)
            $after = $this->option('after') ?? 24; // 現在から未来に向けて取得する範囲（hour) (default = 24hour)
            $type = collect($this->option('type')); // 取得する video type (default = null(all))
            $status = collect($this->option('status')); // 取得する video type (default = null(all))

            $q = Video::query()->orderBy('start_time', 'asc');
            if ($type->count()) $q->whereIn('type', $type);
            if ($status->count()) $q->whereIn('status', $status);
            if ($before >= 0) {
                $start = TimeUtil::LocaleCarbonNow()->subHours(intval($before))->toDateTime();
                $q->whereDate('end_time', '>=', $start);
            }
            if ($after >= 0) {
                $end = TimeUtil::LocaleCarbonNow()->subHours(intval($after))->toDateTime();
                $q->whereDate('start_time', '<', $end);
            }

            // dump モードなら全て表示して終了
            if ($dump) {
                $videos = $q->get();
                foreach ($videos as $video) {
                    $this->line($video);
                }
                return 0;
            }

            $ids = $q
                ->select(['code'])
                ->get()
                ->pluck('code')
                ->toArray();
        }

        UpsertYoutubeVideo::builder()
            ->createNewChannel($force)
            ->skipNewChannel($skip)
            ->skipExistVideo($nonexist)
            ->addEvents(GeneralEvents::arrayTaskEvents('Upsert youtube videos'))
            ->exec($ids);

        return 0;
    }
}
