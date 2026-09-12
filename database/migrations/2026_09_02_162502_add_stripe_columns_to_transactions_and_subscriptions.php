<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('stripe_session_id')->nullable()->after('status')->index();
            $table->string('stripe_payment_intent')->nullable()->after('stripe_session_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('stripe_session_id')->nullable()->after('status')->index();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['stripe_session_id', 'stripe_payment_intent']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('stripe_session_id');
        });
    }
};
