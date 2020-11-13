<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Tasks\UpsertYoutubeChannel;
use App\Lib\TaskBuilder\Utils\EventRecord;
use App\Lib\TaskBuilder\Utils\EventUtil;
use App\Models\Youtube;

class YoutubeChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:channel
        { --profile : Create profile (disable --all option) }
        { --all : Upsert All Record in DB }
        { ids?* : Youtube channelID (UCxxx) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upsert youtube channel';

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
        $createProfileMode = $this->option('profile'); // true で profile 推測生成モード
        $all = $this->option('all'); // true で DB の値を対象に upsert する
        $ids = $this->argument('ids') ?? []; // channel ids

        // create profile の ailias 処理
        if ($createProfileMode) {
            $res = Artisan::call(ProfileCreate::class, [
                'ids' => $ids,
            ], $this->getOutput());
            return $res;
        }

        // ID が指定されていなければ DB 全てを対象とする
        if ($all) {
            $ids = Youtube::select(['code'])->get()
                ->pluck('code')
                ->toArray();
        }

        UpsertYoutubeChannel::builder()
            ->addEvents($this->getEvents())
            ->exec($ids);

        return 0;
    }

    private function getEvents()
    {
        $events = [
            'beforeTask' => function ($val, EventRecord $e) {
                $length = EventUtil::allCount($val);
                logger()->notice("Upsert Youtube Channel (length: {$length})");
            },

            'beforeChunkLoop' => function ($val, EventRecord $e) {
                $length = EventUtil::allCount($val);
                logger()->info("Before loop (length: {$length})");
            },

            ///

            'beforeFetch' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk');
                $length = EventUtil::allCount($val);
                logger()->debug("{$pref}, Fetching... (length: {$length})");
            },

            'afterFetch' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk');
                $length = EventUtil::allCount($val);
                logger()->info("{$pref} Fetched! (length: {$length})");
            },

            ///

            'beforeHandleLoop' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk');
                $length = EventUtil::allCount($val);
                logger()->info("{$pref} Handle loop (length: {$length})");
            },

            'successHandle' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk', 'handle');
                $key = $e->getRecordValue('key', 'handle');
                logger()->debug("{$pref} success: {$key} => {$val}");
            },
            'skipHandle' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk', 'handle');
                $key = $e->getRecordValue('key', 'handle');
                logger()->debug("{$pref} skip: {$key}");
            },
            'throwHandle' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk', 'handle');
                $key = $e->getRecordValue('key', 'handle');
                logger()->error("{$pref} throw: {$key}");
                logger()->error($e->get('exception')); // 例外も吐いとく
            },

            'afterHandleLoop' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk', );
                $stat = EventUtil::statString($e, 'handle');
                logger()->info("{$pref} Handle loop finish! ({$stat})");
            },

            ///

            'afterChunkLoop' => function ($val, EventRecord $e) {
                $stat = EventUtil::allStatString($e, 'handle');
                logger()->info("Loop finish! ({$stat})");
            },

            'afterTask' => function ($val, EventRecord $e) {
                $stat = EventUtil::allStatString($e, 'handle');
                logger()->notice("Finish! Upsert youtube channel ({$stat})");
            },
        ];
        return $events;
    }
}
