<?php


namespace Database\Factories;

use App\Models\DataManagement;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Employee::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      ['employee_code' => '1111-1111-2227',
        'employee_name' => 'aaaaa',
        'joining_age_company' => 25,
        'date_joining_company' => '2020/10/10',
        'date_out_company' => '2021/10/10',
        'spouse' => 1,
        'dependents' => 5,
        'worked_year' => 1,
        'final_education' => 5,
        'shortest_service' => 1,
        'total_worked' => 1,
        'company_branch' => '管理本部',
        //      'created_by'=>  Enrollment::first()->id,
        'created_by' => 1,
        'updated_by' => 1,
      ],
      ['employee_code' => '1111-1111-2228',
        'employee_name' => 'aaaaa',
        'joining_age_company' => 25,
        'date_joining_company' => '2020/10/10',
        'date_out_company' => '2021/10/10',
        'spouse' => 1,
        'dependents' => 5,
        'worked_year' => 1,
        'final_education' => 5,
        'shortest_service' => 1,
        'total_worked' => 1,
        'company_branch' => '管理本部',
        //      'created_by'=>  Enrollment::first()->id,
        'created_by' => 1,
        'updated_by' => 1,
      ]
    ];
  }
}
