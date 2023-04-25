<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetTypeTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('asset_type')->delete();

        DB::table('asset_type')->insert([
            0 => [
                'id' => 1,
                'label' => 'Buildings',
                'name' => 'Buildings',
                'description' => 'Physical buildings and structures',
                'is_active' => 1,
                'parent_asset_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            1 => [
                'id' => 2,
                'label' => 'Flooring',
                'name' => 'Flooring',
                'description' => 'Carpets, tiles, hard flooring, etc.',
                'is_active' => 1,
                'parent_asset_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            2 => [
                'id' => 3,
                'label' => 'Roofing',
                'name' => 'Roofing',
                'description' => 'Roofs, ceiling tiles, etc.',
                'is_active' => 1,
                'parent_asset_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            3 => [
                'id' => 4,
                'label' => 'Furniture',
                'name' => 'Furniture',
                'description' => 'Chairs, tables, desks, beds, etc.',
                'is_active' => 1,
                'parent_asset_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            4 => [
                'id' => 5,
                'label' => 'Hydrolics',
                'name' => 'Hydrolics',
                'description' => 'Elevators and hydrolic lifts',
                'is_active' => 1,
                'parent_asset_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            5 => [
                'id' => 6,
                'label' => 'Plumbing',
                'name' => 'Plumbing',
                'description' => 'Water, sewer, grease traps, toilets, sinks and other plumbing',
                'is_active' => 1,
                'parent_asset_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            6 => [
                'id' => 7,
                'label' => 'Grounds',
                'name' => 'Grounds',
                'description' => 'Grounds and lawn equipment',
                'is_active' => 1,
                'parent_asset_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            7 => [
                'id' => 8,
                'label' => 'Safety',
                'name' => 'Safety',
                'description' => 'Fire/alarm system, fire extinguishers, first aid kits, smoke detectors, etc.',
                'is_active' => 1,
                'parent_asset_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            8 => [
                'id' => 9,
                'label' => 'Electrical',
                'name' => 'Electrical',
                'description' => 'Lamps, lighting, breakers, power supplies, etc.',
                'is_active' => 1,
                'parent_asset_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            9 => [
                'id' => 10,
                'label' => 'HVAC',
                'name' => 'HVAC',
                'description' => 'Heating, ventillation, air conditioners, etc.',
                'is_active' => 1,
                'parent_asset_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            10 => [
                'id' => 11,
                'label' => 'Communications',
                'name' => 'Communications',
                'description' => 'Phone, Internet, Networking, Copier, and other communications equipment',
                'is_active' => 1,
                'parent_asset_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            11 => [
                'id' => 12,
                'label' => 'Appliances',
                'name' => 'Appliances',
                'description' => 'Microwaves, heaters, passthrus, dishwashers, refrigerators, etc.',
                'is_active' => 1,
                'parent_asset_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            12 => [
                'id' => 13,
                'label' => 'Vehicles',
                'name' => 'Vehicles',
                'description' => 'Truck, golf cart, and other transportation vehicles',
                'is_active' => 1,
                'parent_asset_type_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
            13 => [
                'id' => 14,
                'label' => 'Linens',
                'name' => 'Linens',
                'description' => 'Mattresses, pillows, comforters, pillow cases, sheets, etc.',
                'is_active' => 1,
                'parent_asset_type_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'remember_token' => null,
            ],
        ]);
    }
}
