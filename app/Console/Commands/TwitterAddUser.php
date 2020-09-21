<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Thujohn\Twitter\Facades\Twitter as TwitterAPI;
use App\Models\Twitter;
use App\lib\Util;

class TwitterAddUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:add {names*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add twitter item$item
        {names* : twitter screen name (@xxx)}';

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
        $names = $this->argument('names');

        // doc: https://github.com/atymic/twitter
        // api ref: https://developer.twitter.com/en/docs/twitter-api
        $params = [
            'screen_name' => implode(',', $names), // max: 100
            'format' => 'object'
        ];
        $items = TwitterAPI::getUsersLookup($params);

        foreach ($items as $item) {
            $code = $item->id_str;

            $twitter = Twitter::firstOrNew(['code' => $code]);
            $twitter->code = $code;
            $twitter->name = $item->name;
            $twitter->screen_name = $item->screen_name;
            $twitter->location = $item->location;
            $twitter->description = $item->description;
            $twitter->url = $item->url;
            $twitter->thumbnail_url = str_replace('_normal.jpg', '.jpg', $item->profile_image_url_https);
            $twitter->banner_url = str_replace('_normal.jpg', '.jpg', $item->profile_banner_url);

            $twitter->protected = $item->protected;
            $twitter->published_at = Util::UTCToLocalCarbon($item->created_at);

            $twitter->followers = $item->followers_count;
            $twitter->friends = $item->friends_count;
            $twitter->listed = $item->listed_count;
            $twitter->favourites = $item->favourites_count;
            $twitter->statuses = $item->statuses_count;

            $twitter->save();
            echo($twitter->screen_name.' => ['.$twitter->id.']'.$twitter->name.' '.PHP_EOL);
        }

        return 0;
    }
}
