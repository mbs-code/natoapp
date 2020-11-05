<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Tasks\AddProfileFromYoutubeChannel;

class ProfileCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:create
        { --force : Create when there\'s duplicated profile }
        { ids?* : Youtube ChannelID (UCxxx) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create profile from youtube';

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
        $ids = $this->argument('ids') ?? []; // channel ids
        $force = $this->option('force'); // true で profile が重複してても作成する

        // TODO: ログ適当＆整合性未チェック
        AddProfileFromYoutubeChannel::builder()
            ->duplicateRename($force)
            ->addEvent('fetched', function ($e) {
                $find = json_encode($e->fetchResponse);
                $mes = "Scraping: {$find}";
                logger()->info($mes);
            })
            ->addEvent('beforeOuterLoop', function ($e) {
                $total = count($e->execProps);
                $mes = "Outer Loop (length: {$e->outerLength}, total: {$total})";
                logger()->notice($mes);
            })
            ->exec($ids);

        return 0;
    }
}
