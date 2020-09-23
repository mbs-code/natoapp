<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Models\Youtube;
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
            $code = data_get($item, 'id');

            $y = Youtube::firstOrNew(['code' => $code]);
            $y->code = $code;
            $y->name = data_get($item, 'snippet.title');
            $y->description = data_get($item, 'snippet.description');
            $y->playlist = data_get($item, 'contentDetails.relatedPlaylists.uploads');
            $y->thumbnail_url = $this->chooseYoutubeThumbnail(data_get($item, 'snippet.thumbnails'));
            $y->banner_url = data_get($item, 'brandingSettings.image.bannerTvHighImageUrl');

            $y->published_at = Util::UTCToLocalCarbon(data_get($item, 'snippet.publishedAt'));

            $y->views = data_get($item, 'statistics.viewCount');
            $y->comments = data_get($item, 'statistics.commentCount');
            $y->subscribers = data_get($item, 'statistics.subscriberCount');
            $y->videos = data_get($item, 'statistics.videoCount');

            $y->save();
            echo($y->code.' => ['.$y->id.']'.$y->name.' '.PHP_EOL);
        }

        return 0;
    }

    private function chooseYoutubeThumbnail(object $snippet_thumbnail)
    {
        $keys = ['maxers', 'standard', 'high', 'medium', 'default'];
        foreach ($keys as $key) {
            $url = data_get($snippet_thumbnail, $key.'.url');
            if ($url) {
                return $url;
            }
        }
        return null;
    }
}
