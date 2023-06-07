<?php


namespace Tests\Browser\Systems;


use App\Models\Employee;
use Carbon\Carbon;
use Tests\DuskTestCase;

class SystemTest extends DuskTestCase
{
//  public function test_user_role_head_quater()
//  {
//    $this->browse(function ($browser) {
//      $browser->maximize();
//      $login = new LoginTest();
//      $header = new HeaderTest();
//      $user = new UserTest();
//      $enrollment = new EnrollmentTest();
//      $management = new DataManagementTest();
//      $login->test_can_login($browser, 'test@gmail.com');
//      $header->test_can_link_to_user_management($browser);
//      $user->test_link_to_page_create_user($browser);
//      $user->test_can_create_user($browser);
//      $user->test_link_to_edit_user($browser);
//      $user->test_can_edit_user($browser);
//      $user->test_can_remove_user($browser);
//      $header->test_can_link_to_impost_csv($browser);
//      $management->test_can_import_csv($browser);
//      $header->test_can_link_to_data_management($browser);
//      $header->test_can_link_to_create_enrollment($browser);
//      $enrollment->test_can_create_enrollment($browser);
//      $enrollment->test_can_filter_result_by_company_branch($browser);
//      $enrollment->test_can_filter_result_by_user_code($browser);
//      $enrollment->test_can_filter_result_by_user_name($browser);
//      $header->test_can_link_to_list_enrollment($browser);
//      $enrollment->test_can_filter_list_enrollment_by_date_interview($browser);
//      $enrollment->test_can_filter_list_enrollment_by_interview_location($browser);
//      $enrollment->test_can_filter_list_enrollment_by_candidate_name($browser);
//      $enrollment->test_can_print_pdf($browser);
//      $header->test_can_link_to_create_enrollment($browser);
//      $enrollment->test_can_create_enrollment($browser);
//      $header->test_can_link_to_list_enrollment($browser);
//      $login->test_can_logout($browser);
//    });
//  }
//
//  public function test_user_role_department()
//  {
//    $this->browse(function ($browser) {
//      $browser->maximize();
//      $login = new LoginTest();
//      $header = new HeaderTest();
//      $user = new UserTest();
//      $management = new DataManagementTest();
//      $enrollment = new EnrollmentTest();
//      $login->test_can_login($browser, 'test2@gmail.com');
//      $header->test_can_link_to_user_management_by_role_department($browser);
//      $user->test_link_to_page_create_user($browser);
//      $user->test_can_create_user_department($browser);
//      $user->test_can_link_user_edit_role_department($browser);
//      $user->test_can_edit_user($browser);
//      $user->test_can_remove_user_role_department($browser);
//      $header->test_can_link_to_impost_csv($browser);
//      $management->test_can_import_csv($browser);
//      $header->test_can_link_to_data_management_by_role_department($browser);
//      $header->test_can_link_to_create_enrollment($browser);
//      $enrollment->test_can_create_enrollment_by_role_department($browser);
//      $enrollment->test_can_filter_result_by_user_code_role_department($browser);
//      $enrollment->test_can_filter_result_by_user_name($browser);
//      $header->test_can_link_to_list_enrollment_by_role_department($browser);
//      $enrollment->test_can_filter_list_enrollment_by_date_interview($browser);
//      $enrollment->test_can_filter_list_enrollment_by_candidate_name_role_department($browser);
//      $enrollment->test_can_print_pdf($browser);
//      $header->test_can_link_to_create_enrollment($browser);
//      $enrollment->test_can_create_enrollment_by_role_department($browser);
//      $header->test_can_link_to_list_enrollment_by_role_department($browser);
//      $login->test_can_logout($browser);
//    });
//  }
  public function test_system_risk_prediction_score_role_head_quater()
  {
    $this->browse(function ($browser) {
      $browser->maximize();
      $login = new LoginTest();
      $header = new HeaderTest();
      $graph = new GraphTest();
      $employee = new EmployeeTest();
      $digitaco = new DataManagementTest();
      $user = new UserTest();
      $data = Employee::join('risk_scores', 'employees.employee_code', 'risk_scores.employee_id')
        ->orderBy('risk_scores.retirement_score', 'desc');
      $data = $data->where(function ($query) {
        $now = Carbon::now();
        $date = Carbon::parse($now->year . '-' . $now->month . '-03 00:00:00');
        if ($date > $now) {
          $query->whereMonth('month_year', '=', $now->month - 2);
          $query->whereMonth('month_year', '=', $now->month - 2);
        } else {
          $query->whereMonth('month_year', '=', $now->month - 1);
        }
        $query->whereYear('month_year', '=', $now->year);
      });
      $data = $data->first();
      $login->test_can_login($browser, 'test@gmail.com');
      $header->test_can_link_to_retirement_risk_prediction_graph_role_head_quater($browser);
      $graph->test_can_view_preview_month_role_head_quater($browser);
      $header->test_can_link_to_employee_all_role_quater($browser);
      $employee->test_can_sort_employee_by_risk_score($browser);
      $employee->test_can_filter_employee_list_by_company_branch($browser);
      $employee->test_can_filter_employee_list_by_employee_code($browser);
      $employee->test_can_filter_employee_list_by_employee_name($browser);
      $employee->test_can_link_to_employee_detail($browser, $data);
      $employee->test_can_view_preview_year_of_employee_detail($browser, $data);
      $header->test_can_link_to_digitaco_list($browser);
      $digitaco->test_can_link_to_digitaco_point($browser);
      $header->test_can_link_to_digitaco_list($browser);
      $digitaco->test_can_link_to_digitaco_driving($browser);
      $header->test_can_link_to_user_management($browser);
      $user->test_link_to_page_create_user($browser);
      $user->test_can_create_user($browser);
      $login->test_can_logout($browser);
    });
  }

  public function test_system_risk_prediction_score_role_department()
  {
    $this->browse(function ($browser) {
      $browser->maximize();
      $login = new LoginTest();
      $header = new HeaderTest();
      $graph = new GraphTest();
      $employee = new EmployeeTest();
      $login->test_can_login($browser, 'test3@gmail.com');
      $header->test_can_link_to_retirement_risk_prediction_graph_role_department($browser);
      $graph->test_can_view_preview_month_role_department($browser);
      $header->test_can_link_to_employee_all_role_department($browser);
      $employee->test_can_filter_employee_list_by_employee_code($browser);
      $employee->test_can_filter_employee_list_by_employee_name($browser);
      $login->test_can_logout($browser);
    });
  }
}
