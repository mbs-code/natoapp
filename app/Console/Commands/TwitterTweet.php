<?php

namespace App\Console\Commands;

use App\Lib\Tasks\CheckTwitterTweet;
use App\Lib\Tasks\Utils\GeneralEvents;
use Illuminate\Console\Command;
use Thujohn\Twitter\Facades\Twitter as TwitterAPI;

class TwitterTweet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:tweet
    { --all : Upsert All Record in DB }
    {names?* : Twitter screenName (@xxx, no atmark) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check recent tweets';

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
        // if ($all) {
        //     $names = Twitter::select(['screen_name'])->get()
        //         ->pluck('screen_name')
        //         ->toArray();
        // }

        $names = ['sana_natori'];
        $links = CheckTwitterTweet::builder()
            ->addEvents(GeneralEvents::seriesArrayTaskEvents('Check twitter tweets'))
            ->exec($names);
        var_dump($links);

        // $names = ['mochi8hiyoko'];

        // $names = collect($names)->implode(',');
        // $tweets = TwitterAPI::getUserTimeline([
        //     'screen_name' => $names,
        //     'count' => 2,
        //     'format' => 'object',
        //     'max_id' => '1318919644262977537',
        // ]);
        // echo(1318919644262977537 -1);
        // var_dump(collect($tweets)->pluck('id_str', 'text')->toArray());

        return 0;
    }
}
