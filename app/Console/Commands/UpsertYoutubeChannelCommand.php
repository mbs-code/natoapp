<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Tasks\UpsertYoutubeChannel;
use App\Lib\Tasks\AddProfileFromYoutubeChannel;
use App\Lib\Tasks\Utils\GeneralEvents;
use App\Models\Youtube;

class UpsertYoutubeChannelCommand extends Command
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
        $all = $this->option('all'); // true で DB の値を対象に upsert する
        $createProfile = $this->option('profile'); // true で profile 推測生成モード

        // create profile 中は all は無視される
        if ($createProfile) {
            $ids = $this->argument('ids') ?? [];
            // TODO: ログ適当＆整合性未チェック
            AddProfileFromYoutubeChannel::builder()
                ->addEvent('fetched', function ($e) {
                    $find = json_encode($e->fetchResponse);
                    $mes = "find: {$find}";
                    logger()->info($mes);
                })
                ->addEvent('beforeOuterLoop', function ($e) {
                    $total = count($e->execProps);
                    $mes = "Outer Loop (length: {$e->outerLength}, total: {$total})";
                    logger()->notice($mes);
                })
                ->exec($ids);
        }

        // 通常実行
        if (!$createProfile) {
            // ID が指定されていなければ DB 全てを対象とする
            $ids = $this->argument('ids') ?? [];
            if ($all) {
                $ids = Youtube::select(['code'])->get()
                    ->pluck('code')
                    ->toArray();
            }

            UpsertYoutubeChannel::builder()
                ->addEvents(GeneralEvents::arrayTaskEvents('Upsert youtube channels'))
                ->exec($ids);
        }

        return 0;
    }
}
