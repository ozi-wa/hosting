<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('whmcs_client_id')->nullable()->unique()->after('status');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('whmcs_gid')->nullable()->unique()->after('type');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('whmcs_product_id')->nullable()->unique()->after('sku');
            $table->unsignedBigInteger('whmcs_gid')->nullable()->index()->after('whmcs_product_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('whmcs_order_id')->nullable()->unique()->after('number');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('whmcs_invoice_id')->nullable()->unique()->after('number');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('whmcs_service_id')->nullable()->unique()->after('number');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('whmcs_ticket_id')->nullable()->unique()->after('number');
            $table->string('whmcs_tid')->nullable()->index()->after('whmcs_ticket_id');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['whmcs_ticket_id', 'whmcs_tid']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('whmcs_service_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('whmcs_invoice_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('whmcs_order_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['whmcs_product_id', 'whmcs_gid']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('whmcs_gid');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('whmcs_client_id');
        });
    }
};
