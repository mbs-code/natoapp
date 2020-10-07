<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Tasks\UpsertYoutubeVideo;

class YoutubeAddVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:video {--force} {ids*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add youtube video
    {--force : Create when there is no channel. }
    {ids* : youtube videoId (?v=xxx)}';

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
        $ids = $this->argument('ids');
        $force = $this->option('force'); // true で channel が無いとき create する

        UpsertYoutubeVideo::getInstance(true)
            ->notExistChannel($force)
            ->addEvent('inserted', function ($item, $index, $length) {
                $pref = '['.($index+1).'/'.$length.']insert: ';
                echo($pref.$item->code.' => ['.$item->id.']'.$item->title.' '.PHP_EOL);
            })
            ->execArray($ids);

        return 0;
    }
}
