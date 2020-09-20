<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Models\Channel;
use App\lib\Util;

class YoutubeAddChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:add {ids*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add youtube user
    {ids* : youtube channelid (UCxxx)}';

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

        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        $params = [
            'part' => 'id, snippet, statistics, contentDetails, brandingSettings',
            'maxResults' => 50 // max: 50
        ];
        $items = YoutubeAPI::getChannelById($ids, $params);

        foreach ($items as $item) {
            $chid = data_get($item, 'id');

            $c = Channel::firstOrNew(['key' => $chid]);
            $c->key = $chid;
            $c->name = data_get($item, 'snippet.title');
            $c->description = data_get($item, 'snippet.description');
            $c->playlist = data_get($item, 'contentDefails.relatedPlaylists.uploads');
            $c->published_at = Util::UTCToLocalCarbon(data_get($item, 'snippet.publishedAt'));

            $c->views = data_get($item, 'statistics.viewCount');
            $c->comments = data_get($item, 'statistics.commentCount');
            $c->subscribers = data_get($item, 'statistics.subscriberCount');
            $c->videos = data_get($item, 'statistics.videoCount');

            $c->save();
            echo($c->key.' => ['.$c->id.']'.$c->name.' '.PHP_EOL);
        }

        return 0;
    }
}
