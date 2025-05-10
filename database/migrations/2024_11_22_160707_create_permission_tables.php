<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $tableNames = [
        'permissions' => 'permissions',
        'roles' => 'roles',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ];

    protected $columnNames = [
        'model_morph_key' => 'model_id',
        'team_foreign_key' => 'team_id',
        'role_pivot_key' => 'role_id',
        'permission_pivot_key' => 'permission_id'
    ];

    public function up(): void
    {
        // Create permissions table
        Schema::create($this->tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // Create roles table
        Schema::create($this->tableNames['roles'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // Create model_has_permissions table
        Schema::create($this->tableNames['model_has_permissions'], function (Blueprint $table) {
            $table->unsignedBigInteger($this->columnNames['permission_pivot_key'] ?? 'permission_id');
            $table->string('model_type');
            $table->uuid($this->columnNames['model_morph_key']);

            $table->index([$this->columnNames['model_morph_key'], 'model_type']);

            $table->foreign($this->columnNames['permission_pivot_key'] ?? 'permission_id')
                ->references('id')
                ->on($this->tableNames['permissions'])
                ->onDelete('cascade');

            $table->primary(
                [$this->columnNames['permission_pivot_key'] ?? 'permission_id', $this->columnNames['model_morph_key'], 'model_type'],
                'model_has_permissions_permission_model_type_primary'
            );
        });

        // Create model_has_roles table
        Schema::create($this->tableNames['model_has_roles'], function (Blueprint $table) {
            $table->unsignedBigInteger($this->columnNames['role_pivot_key'] ?? 'role_id');
            $table->string('model_type');
            $table->uuid($this->columnNames['model_morph_key']);

            $table->index([$this->columnNames['model_morph_key'], 'model_type']);

            $table->foreign($this->columnNames['role_pivot_key'] ?? 'role_id')
                ->references('id')
                ->on($this->tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(
                [$this->columnNames['role_pivot_key'] ?? 'role_id', $this->columnNames['model_morph_key'], 'model_type'],
                'model_has_roles_role_model_type_primary'
            );
        });

        // Create role_has_permissions table
        Schema::create($this->tableNames['role_has_permissions'], function (Blueprint $table) {
            $table->unsignedBigInteger($this->columnNames['permission_pivot_key'] ?? 'permission_id');
            $table->unsignedBigInteger($this->columnNames['role_pivot_key'] ?? 'role_id');

            $table->foreign($this->columnNames['permission_pivot_key'] ?? 'permission_id')
                ->references('id')
                ->on($this->tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign($this->columnNames['role_pivot_key'] ?? 'role_id')
                ->references('id')
                ->on($this->tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(
                [$this->columnNames['permission_pivot_key'] ?? 'permission_id', $this->columnNames['role_pivot_key'] ?? 'role_id'],
                'role_has_permissions_permission_id_role_id_primary'
            );
        });
    }

    public function down(): void
    {
        Schema::drop($this->tableNames['role_has_permissions']);
        Schema::drop($this->tableNames['model_has_roles']);
        Schema::drop($this->tableNames['model_has_permissions']);
        Schema::drop($this->tableNames['roles']);
        Schema::drop($this->tableNames['permissions']);
    }
};
