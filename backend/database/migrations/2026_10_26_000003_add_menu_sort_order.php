<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {if(!Schema::hasColumn('menu','sort_order'))Schema::table('menu',function(Blueprint $t){$t->unsignedInteger('sort_order')->default(0);});}
 public function down():void {Schema::table('menu',function(Blueprint $t){$t->dropColumn('sort_order');});}
};
