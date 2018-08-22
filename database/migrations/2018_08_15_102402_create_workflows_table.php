<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('flow_name')->comment('名称');
            $table->integer('flow_type')->nullable()->comment('类型');
            $table->string('flow_desc')->nullable()->comment('描述');
            $table->text('flow_design')->nullable()->comment('表单设计');
            $table->text('flow_process')->nullable()->comment('流程设计');
            $table->integer('flow_sort')->default(50)->comment('排序');
            $table->tinyinteger('flow_status')->default(2)->comment('流程状态 1为启用,2为禁用');
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
        Schema::dropIfExists('workflows');
    }
}
