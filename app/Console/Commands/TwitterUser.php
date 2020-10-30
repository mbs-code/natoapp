<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Tasks\UpsertTwitterUser;
use App\Lib\Tasks\Utils\GeneralEvents;
use App\Models\Twitter;

class TwitterUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:user
        { --all : Upsert All Record in DB }
        {names?* : Twitter screenName (@xxx, no atmark) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upsert twitter user';

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
        $names = $this->argument('names') ?? []; // screen_name ids

        // ID が指定されていなければ DB 全てを対象とする
        if ($all) {
            $names = Twitter::select(['screen_name'])->get()
                ->pluck('screen_name')
                ->toArray();
        }

        UpsertTwitterUser::builder()
            ->addEvents(GeneralEvents::arrayTaskEvents('Upsert twitter users'))
            ->exec($names);

        return 0;
    }
}
