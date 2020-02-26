<?php

use Illuminate\Database\Seeder;
use Shelter\Guard\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where('email', '=', 'o.polishchuk@ideil.com')->first()) {
            User::insert([
                'id' => 'e5cbce48-12b1-11ea-88fb-0242ac120002',
                'email' => 'o.polishchuk@ideil.com',
                'password' => '$2y$10$V16I.5F/EaNvJ369GrvcNOx8uQasyN4H1T.JoKbgw1zOBiDcuaPP.',
                'first_name' => 'Oleksandr',
                'last_name' => 'Po',
                'login' => 'o.polishchuk',
                'is_active' => 1,
                'is_admin' => 1,
                'is_banned' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        if (!User::where('email', '=', 'o.kostiuk@ideil.com')->first()) {
            User::insert([
                'id' => 'fd455fb4-1679-11ea-9615-0242ac130006',
                'email' => 'o.kostiuk@ideil.com',
                'password' => '$2y$10$QXZCHI5AwUaeYtr7ooWlAeS50kT69aWP/MiHjGv0iQsgvNI6mJYte',
                'first_name' => 'Олег',
                'last_name' => 'Ko',
                'login' => 'o.kostiuk@ideilk',
                'is_active' => 1,
                'is_admin' => 1,
                'is_banned' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        if (!User::where('email', '=', 'www@farid.technology')->first()) {
            User::insert([
                'id' => '0a012f76-1774-11ea-99b4-000000000000',
                'email' => 'www@farid.technology',
                'password' => '$2y$10$bi1ogoZz2Q612MtBcD3/C.NVReb8qGoRUAywus6buUfZlRJMhgthC',
                'first_name' => 'Farid',
                'last_name' => NULL,
                'login' => 'farid',
                'is_active' => 1,
                'is_admin' => 1,
                'is_banned' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        if (!User::where('email', '=', 'o.polishchuk+italy@ideil.com')->first()) {
            User::insert([
                'id' => '35cbce48-12b1-11ea-88fb-0242ac120002',
                'email' => 'o.polishchuk+italy@ideil.com',
                'password' => '$2y$10$V16I.5F/EaNvJ369GrvcNOx8uQasyN4H1T.JoKbgw1zOBiDcuaPP.',
                'first_name' => 'Oleksandr',
                'last_name' => 'Italy',
                'login' => 'o.polishchuk+italy',
                'is_active' => 1,
                'is_admin' => 1,
                'is_banned' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        if (!User::where('email', '=', 'o.polishchuk+estonia@ideil.com')->first()) {
            User::insert([
                'id' => '45cbce48-12b1-11ea-88fb-0242ac120002',
                'email' => 'o.polishchuk+estonia@ideil.com',
                'password' => '$2y$10$V16I.5F/EaNvJ369GrvcNOx8uQasyN4H1T.JoKbgw1zOBiDcuaPP.',
                'first_name' => 'Oleksandr',
                'last_name' => 'Estonia',
                'login' => 'o.polishchuk+estonia',
                'is_active' => 1,
                'is_admin' => 1,
                'is_banned' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
