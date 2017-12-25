<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $fillable = array('name', 'gender', 'dob', 'mother_language', 'secondary_language', 'blood_group', 'blood_group_proof', 'birth_certificate', 'aadhar_card_proof', 'aadhar_card', 'academic_year', 'class', 'father_name', 'father_qualification', 'father_occupation', 'father_official_designation', 'father_office_no', 'father_residential_no', 'father_mobile_no', 'father_email_id', 'mother_name', 'mother_qualification', 'mother_occupation', 'mother_official_designation', 'mother_office_no', 'mother_residential_no', 'mother_mobile_no', 'mother_email_id', 'present_plot_house_no', 'present_at', 'present_post', 'present_ps', 'present_dist', 'present_state', 'present_pin', 'permanent_plot_house_no', 'permanent_at', 'permanent_post', 'permanent_ps', 'permanent_dist', 'permanent_state', 'permanent_pin', 'transport', 'dayboarding', 'hostel', 'localised', 'serious_illness', 'identified_allergies', 'previous_edication', 'special_intrest', 'two_person_allowed', 'mode_of_transport', 'caste', 'whether_child_of_staff', 'application_fee_receipt_no', 'photo_copy_front_side', 'photo_copy_back_side');
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
