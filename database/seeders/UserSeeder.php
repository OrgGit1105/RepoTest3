<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!User::find(1)) {
            User::insert([
                'id' => 1,
                'email' => 'test@gmail.com',
                'phone' => '0123456789',
                'password' => Hash::make('12345678'),
                'name' => 'Headquarter',
                'username'=> 'Headquarter',
                'role_id' => 1,
                'created_at'=>now(),
                'updated_at'=>now()
            ]);
            if(!User::find(2)) {
            User::insert([
              'id' => 2,
              'email' => 'test2@gmail.com',
              'phone' => '0123456789',
              'password' => Hash::make('12345678'),
              'name' => 'Department',
              'role_id' => 2,
              'department_id'=>1,
              'username'=> 'Department',
              'created_at'=>now(),
              'updated_at'=>now()
            ]);
            if(!User::find(3)) {
            User::insert([
              'id' => 3,
              'email' => 'test3@gmail.com',
              'phone' => '0123456789',
              'password' => Hash::make('12345678'),
              'name' => 'Department',
              'role_id' => 2,
              'department_id'=> 2,
              'username'=> 'Department',
              'created_at'=>now(),
              'updated_at'=>now()
            ]);
            if(!User::find(4)) {
              User::insert([
                'id' => 4,
                'email' => 'test4@gmail.com',
                'phone' => '0123456789',
                'password' => Hash::make('12345678'),
                'name' => 'Department',
                'role_id' => 2,
                'department_id'=> 3,
                'username'=> 'Department',
                'created_at'=>now(),
                'updated_at'=>now()
              ]);
              if(!User::find(5)) {
                User::insert([
                  'id' => 5,
                  'email' => 'test5@gmail.com',
                  'phone' => '0123456789',
                  'password' => Hash::make('12345678'),
                  'name' => 'Department',
                  'role_id' => 2,
                  'department_id'=> 4,
                  'username'=> 'Department',
                  'created_at'=>now(),
                  'updated_at'=>now()
                ]);
                if(!User::find(6)) {
                  User::insert([
                    'id' => 6,
                    'email' => 'test6@gmail.com',
                    'phone' => '0123456789',
                    'password' => Hash::make('12345678'),
                    'name' => 'Department',
                    'role_id' => 2,
                    'department_id'=> 5,
                    'username'=> 'Department',
                    'created_at'=>now(),
                    'updated_at'=>now()
                  ]);
                  if(!User::find(7)) {
                    User::insert([
                      'id' => 7,
                      'email' => 'test7@gmail.com',
                      'phone' => '0123456789',
                      'password' => Hash::make('12345678'),
                      'name' => 'Department',
                      'role_id' => 2,
                      'department_id'=> 6,
                      'username'=> 'Department',
                      'created_at'=>now(),
                      'updated_at'=>now()
                    ]);
                    if(!User::find(8)) {
                      User::insert([
                        'id' => 8,
                        'email' => 'test8@gmail.com',
                        'phone' => '0123456789',
                        'password' => Hash::make('12345678'),
                        'name' => 'Department',
                        'role_id' => 2,
                        'department_id'=> 7,
                        'username'=> 'Department',
                        'created_at'=>now(),
                        'updated_at'=>now()
                      ]);
                      if(!User::find(9)) {
                        User::insert([
                          'id' => 9,
                          'email' => 'test9@gmail.com',
                          'phone' => '0123456789',
                          'password' => Hash::make('12345678'),
                          'name' => 'Department',
                          'role_id' => 2,
                          'department_id'=> 8,
                          'username'=> 'Department',
                          'created_at'=>now(),
                          'updated_at'=>now()
                        ]);
                        if(!User::find(10)) {
                          User::insert([
                            'id' => 10,
                            'email' => 'test10@gmail.com',
                            'phone' => '0123456789',
                            'password' => Hash::make('12345678'),
                            'name' => 'Department',
                            'role_id' => 2,
                            'department_id'=> 9,
                            'username'=> 'Department',
                            'created_at'=>now(),
                            'updated_at'=>now()
                          ]);
                          if(!User::find(11)) {
                            User::insert([
                              'id' => 11,
                              'email' => 'test11@gmail.com',
                              'phone' => '0123456789',
                              'password' => Hash::make('12345678'),
                              'name' => 'Department',
                              'role_id' => 2,
                              'department_id'=> 10,
                              'username'=> 'Department',
                              'created_at'=>now(),
                              'updated_at'=>now()
                            ]);
                            if(!User::find(12)) {
                              User::insert([
                                'id' => 12,
                                'email' => 'test12@gmail.com',
                                'phone' => '0123456789',
                                'password' => Hash::make('12345678'),
                                'name' => 'Department',
                                'role_id' => 2,
                                'department_id'=> 11,
                                'username'=> 'Department',
                                'created_at'=>now(),
                                'updated_at'=>now()
                              ]);
                              if(!User::find(13)) {
                                User::insert([
                                  'id' => 13,
                                  'email' => 'test13@gmail.com',
                                  'phone' => '0123456789',
                                  'password' => Hash::make('12345678'),
                                  'name' => 'Department',
                                  'role_id' => 2,
                                  'department_id'=> 12,
                                  'username'=> 'Department',
                                  'created_at'=>now(),
                                  'updated_at'=>now()
                                ]);
                                if(!User::find(14)) {
                                  User::insert([
                                    'id' => 14,
                                    'email' => 'test14@gmail.com',
                                    'phone' => '0123456789',
                                    'password' => Hash::make('12345678'),
                                    'name' => 'Department',
                                    'role_id' => 2,
                                    'department_id'=> 13,
                                    'username'=> 'Department',
                                    'created_at'=>now(),
                                    'updated_at'=>now()
                                  ]);
                                  if(!User::find(15)) {
                                    User::insert([
                                      'id' => 15,
                                      'email' => 'test15@gmail.com',
                                      'phone' => '0123456789',
                                      'password' => Hash::make('12345678'),
                                      'name' => 'Department',
                                      'role_id' => 2,
                                      'department_id'=> 14,
                                      'username'=> 'Department',
                                      'created_at'=>now(),
                                      'updated_at'=>now()
                                    ]);
                                    if(!User::find(16)) {
                                      User::insert([
                                        'id' => 16,
                                        'email' => 'test16@gmail.com',
                                        'phone' => '0123456789',
                                        'password' => Hash::make('12345678'),
                                        'name' => 'Department',
                                        'role_id' => 2,
                                        'department_id'=> 15,
                                        'username'=> 'Department',
                                        'created_at'=>now(),
                                        'updated_at'=>now()
                                      ]);
                                      if(!User::find(17)) {
                                        User::insert([
                                          'id' => 17,
                                          'email' => 'test17@gmail.com',
                                          'phone' => '0123456789',
                                          'password' => Hash::make('12345678'),
                                          'name' => 'Department',
                                          'role_id' => 2,
                                          'department_id'=> 16,
                                          'username'=> 'Department',
                                          'created_at'=>now(),
                                          'updated_at'=>now()
                                        ]);
                                        if(!User::find(18)) {
                                          User::insert([
                                            'id' => 18,
                                            'email' => 'test18@gmail.com',
                                            'phone' => '0123456789',
                                            'password' => Hash::make('12345678'),
                                            'name' => 'Department',
                                            'role_id' => 2,
                                            'department_id'=> 17,
                                            'username'=> 'Department',
                                            'created_at'=>now(),
                                            'updated_at'=>now()
                                          ]);
                                          if(!User::find(19)) {
                                            User::insert([
                                              'id' => 19,
                                              'email' => 'test19@gmail.com',
                                              'phone' => '0123456789',
                                              'password' => Hash::make('12345678'),
                                              'name' => 'Department',
                                              'role_id' => 2,
                                              'department_id'=> 18,
                                              'username'=> 'Department',
                                              'created_at'=>now(),
                                              'updated_at'=>now()
                                            ]);
                                            if(!User::find(20)) {
                                              User::insert([
                                                'id' => 20,
                                                'email' => 'test20@gmail.com',
                                                'phone' => '0123456789',
                                                'password' => Hash::make('12345678'),
                                                'name' => 'Department',
                                                'role_id' => 2,
                                                'department_id'=> 19,
                                                'username'=> 'Department',
                                                'created_at'=>now(),
                                                'updated_at'=>now()
                                              ]);
                                              if(!User::find(21)) {
                                                User::insert([
                                                  'id' => 21,
                                                  'email' => 'test21@gmail.com',
                                                  'phone' => '0123456789',
                                                  'password' => Hash::make('12345678'),
                                                  'name' => 'Department',
                                                  'role_id' => 2,
                                                  'department_id'=> 20,
                                                  'username'=> 'Department',
                                                  'created_at'=>now(),
                                                  'updated_at'=>now()
                                                ]);
                                                if(!User::find(22)) {
                                                  User::insert([
                                                    'id' => 22,
                                                    'email' => 'test22@gmail.com',
                                                    'phone' => '0123456789',
                                                    'password' => Hash::make('12345678'),
                                                    'name' => 'Department',
                                                    'role_id' => 2,
                                                    'department_id'=> 21,
                                                    'username'=> 'Department',
                                                    'created_at'=>now(),
                                                    'updated_at'=>now()
                                                  ]);
                                                  if(!User::find(23)) {
                                                    User::insert([
                                                      'id' => 23,
                                                      'email' => 'test23@gmail.com',
                                                      'phone' => '0123456789',
                                                      'password' => Hash::make('12345678'),
                                                      'name' => 'Department',
                                                      'role_id' => 2,
                                                      'department_id'=> 22,
                                                      'username'=> 'Department',
                                                      'created_at'=>now(),
                                                      'updated_at'=>now()
                                                    ]);
                                                    if(!User::find(24)) {
                                                      User::insert([
                                                        'id' => 24,
                                                        'email' => 'test24@gmail.com',
                                                        'phone' => '0123456789',
                                                        'password' => Hash::make('12345678'),
                                                        'name' => 'Department',
                                                        'role_id' => 2,
                                                        'department_id'=> 23,
                                                        'username'=> 'Department',
                                                        'created_at'=>now(),
                                                        'updated_at'=>now()
                                                      ]);
                                                      if(!User::find(25)) {
                                                        User::insert([
                                                          'id' => 25,
                                                          'email' => 'test25@gmail.com',
                                                          'phone' => '0123456789',
                                                          'password' => Hash::make('12345678'),
                                                          'name' => 'Department',
                                                          'role_id' => 2,
                                                          'department_id'=> 24,
                                                          'username'=> 'Department',
                                                          'created_at'=>now(),
                                                          'updated_at'=>now()
                                                        ]);
                                                        if(!User::find(26)) {
                                                          User::insert([
                                                            'id' => 26,
                                                            'email' => 'test26@gmail.com',
                                                            'phone' => '0123456789',
                                                            'password' => Hash::make('12345678'),
                                                            'name' => 'Department',
                                                            'role_id' => 2,
                                                            'department_id'=> 25,
                                                            'username'=> 'Department',
                                                            'created_at'=>now(),
                                                            'updated_at'=>now()
                                                          ]);
                                                          if(!User::find(27)) {
                                                            User::insert([
                                                              'id' => 27,
                                                              'email' => 'test27@gmail.com',
                                                              'phone' => '0123456789',
                                                              'password' => Hash::make('12345678'),
                                                              'name' => 'Department',
                                                              'role_id' => 2,
                                                              'department_id'=> 26,
                                                              'username'=> 'Department',
                                                              'created_at'=>now(),
                                                              'updated_at'=>now()
                                                            ]);
                                                            if(!User::find(28)) {
                                                              User::insert([
                                                                'id' => 28,
                                                                'email' => 'test28@gmail.com',
                                                                'phone' => '0123456789',
                                                                'password' => Hash::make('12345678'),
                                                                'name' => 'Department',
                                                                'role_id' => 2,
                                                                'department_id'=> 27,
                                                                'username'=> 'Department',
                                                                'created_at'=>now(),
                                                                'updated_at'=>now()
                                                              ]);
                                                              if(!User::find(29)) {
                                                                User::insert([
                                                                  'id' => 29,
                                                                  'email' => 'test29@gmail.com',
                                                                  'phone' => '0123456789',
                                                                  'password' => Hash::make('12345678'),
                                                                  'name' => 'Department',
                                                                  'role_id' => 2,
                                                                  'department_id'=> 28,
                                                                  'username'=> 'Department',
                                                                  'created_at'=>now(),
                                                                  'updated_at'=>now()
                                                                ]);
                                                                if(!User::find(30)) {
                                                                  User::insert([
                                                                    'id' => 30,
                                                                    'email' => 'test30@gmail.com',
                                                                    'phone' => '0123456789',
                                                                    'password' => Hash::make('12345678'),
                                                                    'name' => 'Department',
                                                                    'role_id' => 2,
                                                                    'department_id'=> 29,
                                                                    'username'=> 'Department',
                                                                    'created_at'=>now(),
                                                                    'updated_at'=>now()
                                                                  ]);

          }}}}}}}}}}}}}}}}}}}}}}}}}}}}}}
    }
}
