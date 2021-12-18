<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $name = ["folder1", "folder2", "folder2-1", "folder3", "folder3-1", "file1", "file2", "file2-1", "file3", "file3-1", "file3-1-1"];
        $path = ["/", "/folder1", "/folder1", "/folder1/folder2", "/folder1/folder2", "/folder1", "/folder1/folder2", "/folder1/folder2-1", "/folder1/folder2/folder3", "/folder1/folder2/folder3-1", "/folder1/folder2/folder3-1"];
        $isfolder = [true, true, true, true, true, false, false, false, false, false, false];

        for ($i = 0; $i < count($name); $i++) {
            DB::table('contents')->insert([
                'id' => $i,
                'name' => $name[$i],
                "size" => 10,
                "isfolder" => $isfolder[$i],
                "path" => $path[$i],
                "islocked" => false,
                "created_at" => null,
                "updated_at" => null,
            ]);
        }
    }
}
