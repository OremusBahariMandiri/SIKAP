<?php
// app/Traits/TracksChanges.php di aplikasi klien

namespace App\Traits;

use App\Services\ActivityHubClient;

trait TracksChanges
{
    protected static function bootTracksChanges()
    {
        static::created(function ($model) {
            app(ActivityHubClient::class)->logDataChange(
                $model->getTable(),
                $model->getKey(),
                'create',
                null,
                $model->getAttributes()
            );
        });

        static::updated(function ($model) {
            app(ActivityHubClient::class)->logDataChange(
                $model->getTable(),
                $model->getKey(),
                'update',
                $model->getOriginal(),
                $model->getAttributes()
            );
        });

        static::deleted(function ($model) {
            app(ActivityHubClient::class)->logDataChange(
                $model->getTable(),
                $model->getKey(),
                'delete',
                $model->getAttributes(),
                null
            );
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                app(ActivityHubClient::class)->logDataChange(
                    $model->getTable(),
                    $model->getKey(),
                    'restore',
                    null,
                    $model->getAttributes()
                );
            });
        }
    }
}