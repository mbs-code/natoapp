<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Lib\Tasks\UpsertYoutubeChannel;
use App\Lib\Tasks\Utils\GeneralEvents;
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
            return Artisan::call(ProfileCreate::class, [
                'ids' => $ids,
            ]);
        }

        // ID が指定されていなければ DB 全てを対象とする
        if ($all) {
            $ids = Youtube::select(['code'])->get()
                ->pluck('code')
                ->toArray();
        }

        UpsertYoutubeChannel::builder()
            ->addEvents(GeneralEvents::arrayTaskEvents('Upsert youtube channels'))
            ->exec($ids);

        return 0;
    }
}
