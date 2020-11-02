<?php

namespace App\Console\Commands;

use App\Enums\VideoType;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Cron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron
        {--time= : Overwrite time to run }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run cron command';

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
        $time = $this->option('time');
        $carbon = $time ? Carbon::parse($time) : Carbon::now();

        $minute = $carbon->minute; // 実行した minute

        $stStr = "[START]: cron batch ({$carbon->toDateTimeString()}) ";
        logger()->notice(str_pad($stStr, 100, '*'));

        // 一時間おきに
        if ($minute === 0) {
            // 全 channel を更新する
            Artisan::call(YoutubeChannel::class, [
                '--all' => true,
            ]);

            // 全 twitter user を更新する
            Artisan::call(TwitterUser::class, [
                '--all' => true,
            ]);

            // 全 upcoming video を更新する
            // TODO: record を実行しないモード！
            Artisan::call(YoutubeVideo::class, [
                '--all' => true,
                '--before' => -1, // 全期間
                '--after' => -1, // 全期間
                '--type' => [VideoType::UPCOMING()],
                '--skip' => true, // チャンネルが無かったらスキップ
            ]);
        }

        // 5分おきに
        if ($minute % 5 === 0) {
            // live と1時間前からの upcoming の video を更新する
            // (live の start は必ず 1時間の範囲に入ってる)
            $types = $minute === 0
                ? [VideoType::LIVE()] // minute = 0 のときは上で実行してる
                : [VideoType::LIVE(), VideoType::UPCOMING()];
            Artisan::call(YoutubeVideo::class, [
                '--all' => true,
                '--before' => 1,
                '--after' => -1, // 全期間
                '--type' => $types,
                '--skip' => true, // チャンネルが無かったらスキップ
            ]);

            // 終了後、一時間はログを取る
            Artisan::call(YoutubeVideo::class, [
                '--all' => true,
                '--before' => 1,
                '--after' => 0, // 今
                '--type' => [VideoType::ARCHIVE(), VideoType::VIDEO(), VideoType::PREMIERE()],
                '--skip' => true, // チャンネルが無かったらスキップ
            ]);
        }

        // 15分おきに
        if (($minute - 5) % 15 === 0) {
            // feed から video を収集する
            Artisan::call(YoutubeFeed::class, [
                '--all' => true,
                '--skip' => true, // チャンネルが無かったらスキップ
                '--nonexist' => true, // DBに存在しないもののみ
            ]);
        }

        $msec = number_format($carbon->diffInMilliseconds() / 1000, 2);
        $edStr = "[END]: cron batch ({$msec}sec, {$carbon->toDateTimeString()}) ";
        logger()->notice(str_pad($edStr, 100, '*'));

        return 0;
    }
}
