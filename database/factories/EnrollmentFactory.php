<?php

namespace Database\Factories;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;


class EnrollmentFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Enrollment::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'interview_date'=> '2020-10-10',
//      'interview_branch'=> 1,
      'candidate_name'=> 'hakata',
      'joining_age'=> 25,
      'spouse'=> 1,
      'dependents'=> 1,
      'worked_years'=> 5,
      'final_education'=> 5,
      'shortest_service'=> 1,
//      'company_branch_id'=> 1,
//      'created_by'=> User::first()->id,
      'company_branch_id'=> 1,
      'created_by'=> 1,
      'updated_by'=> 1,
    ];
  }
}
