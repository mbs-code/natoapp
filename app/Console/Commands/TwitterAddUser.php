<?php

namespace App\Console\Commands;

use App\Models\Twitter;
use App\lib\Util;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Thujohn\Twitter\Facades\Twitter as TwitterAPI;

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
    protected $description = 'add twitter user
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

        // ref: https://github.com/atymic/twitter
        $users = TwitterAPI::getUsersLookup(['screen_name' => implode(',', $names), 'format' => 'object']);

        foreach ($users as $user) {
            $user_id = $user->id_str;

            $twitter = Twitter::firstOrNew(['twitter_id' => $user_id]);
            $twitter->twitter_id = $user->id_str;
            $twitter->name = $user->name;
            $twitter->screen_name = $user->screen_name;
            $twitter->location = $user->location;
            $twitter->description = $user->description;
            $twitter->url = $user->url;
            $twitter->profile_image_url = str_replace('_normal.jpg', '.jpg', $user->profile_image_url_https);
            $twitter->profile_banner_url = str_replace('_normal.jpg', '.jpg', $user->profile_banner_url);

            $twitter->protected = $user->protected;
            $twitter->published_at = Util::UTCToLocalCarbon($user->created_at);

            $twitter->followers = $user->followers_count;
            $twitter->friends = $user->friends_count;
            $twitter->listed = $user->listed_count;
            $twitter->favourites = $user->favourites_count;
            $twitter->statuses = $user->statuses_count;

            $twitter->save();
            echo($twitter->screen_name.' => ['.$twitter->id.']'.$twitter->name.' '.PHP_EOL);
        }
        return 0;
    }
}
