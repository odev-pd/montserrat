<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipantRoleTypeTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run(): void
    {
        DB::table('participant_role_type')->delete();

        DB::table('participant_role_type')->insert([
            0 => [
                'id' => 1,
                'option_group_id' => 13,
                'label' => 'Attendee',
                'value' => '1',
                'name' => 'Attendee',
                'grouping' => null,
                'filter' => 1,
                'is_default' => null,
                'weight' => 1,
                'description' => null,
                'is_optgroup' => 0,
                'is_reserved' => 0,
                'is_active' => 1,
                'component_id' => null,
                'domain_id' => null,
                'visibility_id' => null,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'option_group_id' => 13,
                'label' => 'Volunteer',
                'value' => '2',
                'name' => 'Volunteer',
                'grouping' => null,
                'filter' => 1,
                'is_default' => null,
                'weight' => 2,
                'description' => null,
                'is_optgroup' => 0,
                'is_reserved' => 0,
                'is_active' => 1,
                'component_id' => null,
                'domain_id' => null,
                'visibility_id' => null,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            2 => [
                'id' => 3,
                'option_group_id' => 13,
                'label' => 'Host',
                'value' => '3',
                'name' => 'Host',
                'grouping' => null,
                'filter' => 1,
                'is_default' => null,
                'weight' => 3,
                'description' => null,
                'is_optgroup' => 0,
                'is_reserved' => 0,
                'is_active' => 1,
                'component_id' => null,
                'domain_id' => null,
                'visibility_id' => null,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            3 => [
                'id' => 4,
                'option_group_id' => 13,
                'label' => 'Speaker',
                'value' => '4',
                'name' => 'Speaker',
                'grouping' => null,
                'filter' => 1,
                'is_default' => null,
                'weight' => 4,
                'description' => null,
                'is_optgroup' => 0,
                'is_reserved' => 0,
                'is_active' => 1,
                'component_id' => null,
                'domain_id' => null,
                'visibility_id' => null,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            4 => [
                'id' => 5,
                'option_group_id' => 13,
                'label' => 'Retreatant',
                'value' => '5',
                'name' => 'Retreatant',
                'grouping' => null,
                'filter' => 1,
                'is_default' => null,
                'weight' => 5,
                'description' => '<p>a person making the SpEx</p>
',
                'is_optgroup' => 0,
                'is_reserved' => 0,
                'is_active' => 1,
                'component_id' => null,
                'domain_id' => null,
                'visibility_id' => null,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            5 => [
                'id' => 8,
                'option_group_id' => 13,
                'label' => 'Retreat Director',
                'value' => 'Retreat Director',
                'name' => 'Retreat Director',
                'grouping' => null,
                'filter' => 1,
                'is_default' => 0,
                'weight' => 6,
                'description' => 'Retreat Director',
                'is_optgroup' => 0,
                'is_reserved' => 0,
                'is_active' => 1,
                'component_id' => null,
                'domain_id' => null,
                'visibility_id' => null,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            6 => [
                'id' => 9,
                'option_group_id' => 13,
                'label' => 'Innkeeper',
                'value' => 'Innkeeper',
                'name' => 'Innkeeper',
                'grouping' => null,
                'filter' => 1,
                'is_default' => 0,
                'weight' => 7,
                'description' => 'Innkeeper',
                'is_optgroup' => 0,
                'is_reserved' => 0,
                'is_active' => 1,
                'component_id' => null,
                'domain_id' => null,
                'visibility_id' => null,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            7 => [
                'id' => 10,
                'option_group_id' => 13,
                'label' => 'Assistant',
                'value' => 'Assistant',
                'name' => 'Assistant',
                'grouping' => null,
                'filter' => 1,
                'is_default' => 0,
                'weight' => 8,
                'description' => 'Assistant',
                'is_optgroup' => 0,
                'is_reserved' => 0,
                'is_active' => 1,
                'component_id' => null,
                'domain_id' => null,
                'visibility_id' => null,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            8 => [
                'id' => 11,
                'option_group_id' => 13,
                'label' => 'Ambassador',
                'value' => 'Ambassador',
                'name' => 'Ambassador',
                'grouping' => null,
                'filter' => null,
                'is_default' => 0,
                'weight' => 9,
                'description' => 'Ambassador',
                'is_optgroup' => 0,
                'is_reserved' => 0,
                'is_active' => 1,
                'component_id' => null,
                'domain_id' => null,
                'visibility_id' => null,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
