<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DashboardNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('dashboard_notification')) {
            Schema::create('dashboard_notification', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('workspace_id');
                $table->bigInteger('user_id');
                $table->bigInteger('staff_id')->nullable();
                $table->bigInteger('order_id')->nullable()->index();
                $table->string('notification_title', 100);
                $table->mediumText('notification_description');
                $table->mediumText('notification_details');
                $table->string('notification_type', 100);
                $table->integer('is_read');
                $table->integer('notified_user')->nullable();
                $table->mediumText('notification_url')->nullable();
                $table->integer('notification_status');
                $table->string('notify_status_code',50)->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['company_id','workspace_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
