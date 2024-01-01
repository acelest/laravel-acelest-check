<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // clé étrangère liée à la table des utilisateurs
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->foreignId('user_sender')->constrained('users'); // clé étrangère liée à la table des utilisateurs (sender)
            $table->foreignId('user_receive')->constrained('users'); // clé étrangère liée à la  des utilisateurs (receiver)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}

