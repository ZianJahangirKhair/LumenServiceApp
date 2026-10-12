<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    private $users = [
        [
            "id" => 1,
            "name" => "Sumatrana",
            "email" => "sumatrana@gmail.com",
            "address" => "Padang",
            "gender" => "Laki-laki"
        ],
        [
            "id" => 2,
            "name" => "Jawarianto",
            "email" => "jawarianto@gmail.com",
            "address" => "Cimahi",
            "gender" => "Laki-laki"
        ],
        [
            "id" => 3,
            "name" => "Kalimantanio",
            "email" => "kalimantanio@gmail.com",
            "address" => "Samarinda",
            "gender" => "Laki-laki"
        ],
        [
            "id" => 4,
            "name" => "Sulawesiani",
            "email" => "sulawesiani@gmail.com",
            "address" => "Makasar",
            "gender" => "Perempuan"
        ],
        [
            "id" => 5,
            "name" => "Papuani",
            "email" => "papuani@gmail.com",
            "address" => "Jayapura",
            "gender" => "Perempuan"
        ]
    ];

    public function status()
    {
        return response()->json([
            "service_name" => "PHP Service App",
            "status" => "Running"
        ]);
    }

    public function getAllUsers()
    {
        return response()->json($this->users);
    }

    public function getUserById($userId)
    {
        foreach ($this->users as $user) {
            if ($user['id'] == $userId) {
                return response()->json($user);
            }
        }

        return response()->json(["message" => "User not found"], 404);
    }
}

