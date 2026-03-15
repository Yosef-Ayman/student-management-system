<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'teacher', 'student', 'parent'])->default('student');
            $table->string('phone', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->string('gender', 10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('users')->insert([
            [
                'id'                => 1,
                'name'              => 'Admin',
                'email'             => 'admin@example.com',
                'email_verified_at' => null,
                'password'          => '$2y$12$RbxTLL.IhVKMlSn4Q6jGOendnfS7gb0Ilpy8BkiNjxV4lJ/8zYLoe',
                'role'              => 'admin',
                'phone'             => null,
                'avatar'            => null,
                'gender'            => null,
                'date_of_birth'     => null,
                'address'           => null,
                'is_active'         => true,
                'remember_token'    => null,
                'created_at'        => null,
                'updated_at'        => null,
                'deleted_at'        => null,
            ],
            [
                'id'                => 2,
                'name'              => 'Teacher',
                'email'             => 'teacher@example.com',
                'email_verified_at' => null,
                'password'          => '$2y$12$k.0tDKq/G099klNoNsYJQu54efhKm7.6giHOm1MKJ6v9YZ1NRNUoi',
                'role'              => 'teacher',
                'phone'             => null,
                'avatar'            => null,
                'gender'            => null,
                'date_of_birth'     => null,
                'address'           => null,
                'is_active'         => true,
                'remember_token'    => null,
                'created_at'        => null,
                'updated_at'        => null,
                'deleted_at'        => null,
            ],
            [
                'id'                => 3,
                'name'              => 'Student',
                'email'             => 'student@example.com',
                'email_verified_at' => null,
                'password'          => '$2y$12$5B2SRHA/pklPhlApJyAH0..W68l3Fik8rjV6D8Z5ULh.PNv8YY6/O',
                'role'              => 'student',
                'phone'             => null,
                'avatar'            => null,
                'gender'            => null,
                'date_of_birth'     => null,
                'address'           => null,
                'is_active'         => true,
                'remember_token'    => null,
                'created_at'        => null,
                'updated_at'        => null,
                'deleted_at'        => null,
            ],
            [
                'id'                => 4,
                'name'              => 'Parent',
                'email'             => 'parent@example.com',
                'email_verified_at' => null,
                'password'          => '$2y$12$ITj7UE68IpB4e/VTtkPjhOE8zySoBJjR4MOvRvFIWOcibORySBoBC',
                'role'              => 'parent',
                'phone'             => null,
                'avatar'            => null,
                'gender'            => null,
                'date_of_birth'     => null,
                'address'           => null,
                'is_active'         => true,
                'remember_token'    => null,
                'created_at'        => null,
                'updated_at'        => null,
                'deleted_at'        => null,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
