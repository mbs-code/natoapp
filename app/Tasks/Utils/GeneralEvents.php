<?php

namespace App\Tasks\Utils;

use App\Lib\TaskBuilder\Utils\EventRecord;
use App\Lib\TaskBuilder\Utils\EventUtil;
use Illuminate\Database\Eloquent\Model;
use App\Models\Video;
use App\Enums\VideoStatus;

class GeneralEvents
{

    public static function apiEvents(string $eventName)
    {
        $events = [
            'beforeTask' => function ($val, EventRecord $e) use ($eventName) {
                $length = EventUtil::allCount($val);
                logger()->notice("{$eventName} (length: {$length})");
            },

            'afterFilter' => function ($val, EventRecord $e) {
                $before = $e->getRecordValue('length', 'filter');
                $after = EventUtil::allCount($val);
                logger()->info("Filtered (length: {$before} => {$after})");
            },

            'afterChunk' => function ($val, EventRecord $e) {
                $before = $e->getRecordValue('length', 'chunk');
                $after = EventUtil::allCount($val);
                logger()->info("Chunked (length: {$before} => {$after})");
            },

            /// ////////////////////////////////////////

            'beforeChunkLoop' => function ($val, EventRecord $e) {
                $length = EventUtil::allCount($val);
                logger()->debug("Before loop (length: {$length})");
            },

            ///

            'beforeFetch' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk');

                $length = $e->getRecordValue('length', 'fetch');
                $key = $e->getRecordValue('key', 'fetch');
                $suff = $key ? "key: {$key}" : "key: {$length} items";
                logger()->debug("{$pref} Fetching... ({$suff})");
            },

            'afterFetch' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk');

                $length = $e->getRecordValue('length', 'fetch');
                $key = $e->getRecordValue('key', 'fetch');
                $fetchLength = EventUtil::allCount($val);
                $suff = $key ? "key: {$key}" : "key: {$length} items";
                logger()->info("{$pref} Fetched! ({$suff}, get: {$fetchLength} items)");
            },

            ///

            'beforeHandleLoop' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk');
                $length = EventUtil::allCount($val);
                logger()->debug("{$pref} Handle loop (length: {$length})");
            },

            'successHandle' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk', 'handle');
                $key = $e->getRecordValue('key', 'handle');
                $method = static::checkMethod($val) ?? 'success'; // create, update, delete
                logger()->debug("{$pref} {$method}: {$key} => {$val}");
            },
            'skipHandle' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk', 'handle');
                $key = $e->getRecordValue('key', 'handle');
                logger()->debug("{$pref} skip: {$key}");
            },
            'throwHandle' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk', 'handle');
                $key = $e->getRecordValue('key', 'handle');
                logger()->error("{$pref} throw: {$key}");
                logger()->error($e->get('exception')); // 例外も吐いとく
            },

            'afterHandleLoop' => function ($val, EventRecord $e) {
                $pref = EventUtil::prefString($e, 'chunk', );
                $stat = EventUtil::statString($e, 'handle');
                logger()->info("{$pref} Handle loop finish! ({$stat})");
            },

            ///

            'afterChunkLoop' => function ($val, EventRecord $e) {
                $stat = EventUtil::allStatString($e, 'handle');
                logger()->debug("Loop finish! ({$stat})");
            },

            /// ////////////////////////////////////////

            'afterTask' => function ($val, EventRecord $e) use ($eventName) {
                $stat = EventUtil::allStatString($e, 'handle');
                logger()->notice("Finish! {$eventName} ({$stat})");
            },
        ];
        return $events;
    }

    ///

    protected static function checkMethod($val)
    {
        if ($val instanceof Model) {
            if ($val instanceof Video) {
                if (VideoStatus::DELETE()->equals($val->status)) {
                    // TODO: delete に切り替えた時だけでもいいかも
                    return 'delete';
                }
            }
            return $val->wasRecentlyCreated ? 'create' : 'update';
        }

        return null;
    }
}
