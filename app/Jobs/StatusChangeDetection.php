<?php

namespace App\Jobs;

use App\Notification;
use App\Organization;
use App\Server;
use App\StatusChange;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StatusChangeDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach (Organization::all() as $organization) {
            /* @var $organization \App\Organization */
            foreach ($organization->servers as $server) {
                $this->detectChangeForServer($server);
            }
        }
    }

    public function detectChangeForServer(Server $server)
    {
        $last_change = StatusChange::getLastChangeForServer($server->id);
        $current_status = $server->status();

        if ($last_change->status == $current_status->code()) {
            // no change
            return;
        }

        $change = new StatusChange();
        $change->server_id = $server->id;
        $change->time = time();
        $change->status = $current_status->code();
        $change->save();

        $this->sendNotificationIfRequired($change);
    }

    /**
     * Maximum number of notifications sent per day.
     */
    const NOTIFICATIONS_PER_DAY = 4;

    public function sendNotificationIfRequired(StatusChange $change)
    {
        // Only send a notification if server changes to status "error"
        if ($change->status < \App\Status::ERROR) {
            return;
        }
        
        $server = $change->server();
        $server_id = $server->id;

        $onedayago = time() - 24 * 3600;
        $sent_notifications_count = Notification::findForServer($server_id, $onedayago)->count();

        if ($sent_notifications_count < self::NOTIFICATIONS_PER_DAY) {
            $notification = new Notification();
            $notification->server()->associate($server);
            $notification->type = "change";
            $notification->change_id = $change->id;
            $notification->saveAndSend();

            return;
        }

        if ($sent_notifications_count == self::NOTIFICATIONS_PER_DAY) {
            $notification = new Notification();
            $notification->server()->associate($server);
            $notification->type = "bouncing";
            $notification->change_id = $change->id;
            $notification->saveAndSend();

            return;
        }

        // nothing to do if number of sent notifications > COUNT
    }
}
