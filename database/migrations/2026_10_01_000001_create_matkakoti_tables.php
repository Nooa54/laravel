<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $t) {
            $t->id();
            $t->string('number')->unique();
            $t->string('name');
            $t->text('description');
            $t->decimal('price_per_night', 8, 2);
            $t->unsignedTinyInteger('capacity')->default(2);
            $t->boolean('private_bathroom')->default(false);
            $t->string('image')->nullable();
            $t->timestamps();
        });

        Schema::create('gallery_images', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('caption')->nullable();
            $t->string('path');
            $t->string('season')->nullable(); // spring|summer|autumn|winter
            $t->timestamps();
        });

        Schema::create('price_items', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('description')->nullable();
            $t->decimal('price', 8, 2);
            $t->string('unit')->default('/ vrk');
            $t->unsignedSmallInteger('sort')->default(0);
            $t->timestamps();
        });

        Schema::create('bookings', function (Blueprint $t) {
            $t->id();
            $t->string('reference', 12)->unique();
            $t->foreignId('room_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('guest_name');
            $t->string('guest_email');
            $t->string('guest_phone')->nullable();
            $t->date('check_in');
            $t->date('check_out');
            $t->unsignedTinyInteger('guests')->default(1);
            $t->decimal('total_price', 9, 2);
            $t->string('status')->default('confirmed'); // confirmed|cancelled
            $t->text('notes')->nullable();
            $t->timestamps();
        });

        Schema::create('reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('room_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('author_name');
            $t->unsignedTinyInteger('rating');
            $t->text('comment');
            $t->timestamps();
        });

        Schema::create('feedback', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('category'); // sivusto|majoitus|ajankohtaista
            $t->unsignedTinyInteger('rating')->nullable();
            $t->text('message');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['feedback', 'reviews', 'bookings', 'price_items', 'gallery_images', 'rooms'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
