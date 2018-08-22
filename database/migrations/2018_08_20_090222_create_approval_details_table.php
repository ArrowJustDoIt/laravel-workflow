<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('flow_id')->comment('审批流id');
            $table->integer('process_id')->default(0)->comment('节点id');
            $table->integer('process_userid')->comment('审核节点用户id');
            $table->string('process_name')->comment('节点名');
            $table->string('process_user_name')->comment('审批节点用户名');
            $table->integer('approval_id')->comment('审批申请id');
            $table->string('approval_detail_comment')->nullable()->comment('审批意见');
            $table->tinyInteger('approval_detail_status')->default(1)->comment('当前审批状态 待处理1 驳回2 拒绝3 同意4');
            $table->index('flow_id');
            $table->index('process_id');
            $table->index('process_userid');
            $table->index('approval_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_details');
    }
}
