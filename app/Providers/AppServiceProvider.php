<?php

namespace App\Providers;

use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionReply;
use App\Observers\ProjectDiscussionObserver;
use App\Observers\ProjectDiscussionReplyObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Observers\PermissionObserver;
use Spatie\Permission\Models\Permission;
use App\Observers\ProjectObserver;
use App\Models\Project;
use App\Observers\ReceiptObserver;
use App\Models\Receipt;
use App\Observers\RoleObserver;
use Spatie\Permission\Models\Role;
use App\Observers\TaskObserver;
use App\Models\Task;
use App\Observers\TicketObserver;
use App\Models\Ticket;
use App\Observers\UserObserver;
use App\Models\User;
use Mail;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Role::observe(RoleObserver::class);
        Permission::observe(PermissionObserver::class);
        Project::observe(ProjectObserver::class);
        Ticket::observe(TicketObserver::class);
        Task::observe(TaskObserver::class);
        Receipt::observe(ReceiptObserver::class);
        ProjectDiscussion::observe(ProjectDiscussionObserver::class);
        ProjectDiscussionReply::observe(ProjectDiscussionReplyObserver::class);

        $throttleRate = config('mail.throttleToMessagesPerMin');
        if ($throttleRate) {
            $throttlerPlugin = new \Swift_Plugins_ThrottlerPlugin($throttleRate, \Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE);
            Mail::getSwiftMailer()->registerPlugin($throttlerPlugin);
        }


    }
}
