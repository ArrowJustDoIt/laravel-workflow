<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('flow_id')->comment('工作流id');
            $table->integer('user_id')->comment('申请用户id');
            $table->string('title')->comment('申请标题');
            $table->string('desc')->nullable()->comment('申请备注');
            $table->text('design')->nullable()->comment('表单数据');
            $table->tinyInteger('status')->default(1)->comment('审批流状态,流程中1,完成2');
            $table->index('flow_id');
            $table->index('user_id');
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
        Schema::dropIfExists('approvals');
    }
}
