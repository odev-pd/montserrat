<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipantStatusTypeTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run(): void
    {
        DB::table('participant_status_type')->delete();

        DB::table('participant_status_type')->insert([
            0 => [
                'id' => 1,
                'name' => 'Registered',
                'label' => 'Registered',
                'value' => null,
                'class' => 'Positive',
                'is_reserved' => 1,
                'is_active' => 1,
                'is_counted' => 1,
                'weight' => 1,
                'visibility_id' => 1,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'name' => 'Attended',
                'label' => 'Attended',
                'value' => null,
                'class' => 'Positive',
                'is_reserved' => 0,
                'is_active' => 0,
                'is_counted' => 1,
                'weight' => 2,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            2 => [
                'id' => 3,
                'name' => 'No-show',
                'label' => 'No-show',
                'value' => null,
                'class' => 'Negative',
                'is_reserved' => 0,
                'is_active' => 1,
                'is_counted' => 0,
                'weight' => 3,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            3 => [
                'id' => 4,
                'name' => 'Canceled',
                'label' => 'Canceled',
                'value' => null,
                'class' => 'Negative',
                'is_reserved' => 1,
                'is_active' => 1,
                'is_counted' => 0,
                'weight' => 4,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            4 => [
                'id' => 5,
                'name' => 'Pending from pay later',
                'label' => 'Pending from pay later',
                'value' => null,
                'class' => 'Pending',
                'is_reserved' => 1,
                'is_active' => 0,
                'is_counted' => 1,
                'weight' => 5,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            5 => [
                'id' => 6,
                'name' => 'Pending from incomplete transaction',
                'label' => 'Pending from incomplete transaction',
                'value' => null,
                'class' => 'Pending',
                'is_reserved' => 1,
                'is_active' => 0,
                'is_counted' => 0,
                'weight' => 6,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            6 => [
                'id' => 7,
                'name' => 'On waitlist',
                'label' => 'Waitlist',
                'value' => null,
                'class' => 'Waiting',
                'is_reserved' => 1,
                'is_active' => 1,
                'is_counted' => 0,
                'weight' => 7,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            7 => [
                'id' => 8,
                'name' => 'Awaiting approval',
                'label' => 'Awaiting approval',
                'value' => null,
                'class' => 'Waiting',
                'is_reserved' => 1,
                'is_active' => 0,
                'is_counted' => 1,
                'weight' => 8,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            8 => [
                'id' => 9,
                'name' => 'Pending from waitlist',
                'label' => 'Pending from waitlist',
                'value' => null,
                'class' => 'Pending',
                'is_reserved' => 1,
                'is_active' => 0,
                'is_counted' => 1,
                'weight' => 9,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            9 => [
                'id' => 10,
                'name' => 'Pending from approval',
                'label' => 'Pending from approval',
                'value' => null,
                'class' => 'Pending',
                'is_reserved' => 1,
                'is_active' => 0,
                'is_counted' => 1,
                'weight' => 10,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            10 => [
                'id' => 11,
                'name' => 'Rejected',
                'label' => 'Rejected',
                'value' => null,
                'class' => 'Negative',
                'is_reserved' => 1,
                'is_active' => 0,
                'is_counted' => 0,
                'weight' => 11,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            11 => [
                'id' => 12,
                'name' => 'Expired',
                'label' => 'Expired',
                'value' => null,
                'class' => 'Negative',
                'is_reserved' => 1,
                'is_active' => 0,
                'is_counted' => 0,
                'weight' => 12,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            12 => [
                'id' => 13,
                'name' => 'Pending in cart',
                'label' => 'Pending in cart',
                'value' => null,
                'class' => 'Pending',
                'is_reserved' => 1,
                'is_active' => 0,
                'is_counted' => 0,
                'weight' => 13,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            13 => [
                'id' => 14,
                'name' => 'Course Completed',
                'label' => 'Course Completed',
                'value' => null,
                'class' => 'Positive',
                'is_reserved' => null,
                'is_active' => 0,
                'is_counted' => 0,
                'weight' => 14,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            14 => [
                'id' => 15,
                'name' => 'Withdrew',
                'label' => 'Withdrew',
                'value' => null,
                'class' => 'Positive',
                'is_reserved' => null,
                'is_active' => 0,
                'is_counted' => 0,
                'weight' => 15,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            15 => [
                'id' => 16,
                'name' => 'Ongoing Retreat',
                'label' => 'Ongoing Retreatant',
                'value' => null,
                'class' => 'Positive',
                'is_reserved' => null,
                'is_active' => 0,
                'is_counted' => 0,
                'weight' => 16,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            16 => [
                'id' => 17,
                'name' => 'Nonparticipating',
                'label' => 'Nonparticipating',
                'value' => null,
                'class' => 'Negative',
                'is_reserved' => 1,
                'is_active' => 1,
                'is_counted' => 0,
                'weight' => 17,
                'visibility_id' => 2,
                'deleted_at' => null,
                'remember_token' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
