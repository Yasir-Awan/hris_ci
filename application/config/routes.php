<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// $route['attendance_list/(:any)/(:any)'] = 'Api/AttendanceList/index/$1/$2';

$route['login'] = 'Api/LoginController/index';
$route['user_list'] = 'Api/UsersList/index';

$route['sites_list'] = 'Api/SitesList/index';

$route['designations_list'] = 'Api/DesignationsList/index';

$route['sites_list_for_schedule'] = 'Api/SitesListForSchedules/index';

$route['modules_list'] = 'Api/Forms/FilterLists/GetModules/index';

$route['employees_list_for_roles_form'] = 'Api/Forms/FilterLists/GetEmployees/index';

$route['sites_list_for_roles_form'] = 'Api/Forms/FilterLists/GetSites/index';


$route['shift_list'] = 'Api/GetShifts/index';
$route['attendance_list'] = 'Api/AttendanceList/index';
$route['mobile_attendance'] = 'Api/MobileAttendanceList/index';
$route['add_user'] = 'Api/AddUser/index';

$route['startdate_leavestatus'] = 'Api/LeaveStatusForStartDate/index';

$route['check_leave_start_date'] = 'Api/CheckLeaveStartDate/index';

$route['check_leave_end_date'] = 'Api/CheckLeaveEndDate/index';

$route['enddate_leavestatus'] = 'Api/LeaveStatusForEndDate/index';

$route['add_schedule'] = 'Api/AddSchedule/index';

$route['schedule_list'] = 'Api/ScheduleList/index';

$route['start_schedule_blocked_dates'] = 'Api/StartScheduleBlockedDates/index';

$route['add_shift'] = 'Api/AddShift/index';

$route['add_goal'] = 'Api/AddShift/index';

$route['add_leave'] = 'Api/Forms/AddLeave/index';

$route['add_role'] = 'Api/Forms/AddRole/index';

$route['update_leave'] = 'Api/UpdateLeave/index';

$route['delete_leave'] = 'Api/DeleteLeave/index';

$route['approve_leave'] = 'Api/ApproveLeave/index';

$route['disapprove_leave'] = 'Api/DisapproveLeave/index';

$route['leaves_list'] = 'Api/LeavesList/index';

$route['monthly_summary'] = 'Api/MonthlySummary/index';

$route['mobile_summary'] = 'Api/MobileSummary/index';

$route['site_employees/(:any)'] = 'Api/SiteEmployees/index/$1';

$route['site_roles/(:any)'] = 'Api/SiteRoles/index/$1';

$route['site_role_employees/(:any)/(:any)'] = 'Api/SiteRoleEmployees/index/$1/$2';

$route['employees_list_for_filters'] = 'Api/EmployeesListForFilters/index';

$route['api/(:any)']['OPTIONS'] = 'api/$1';

$route['roles_list_dropdown'] = 'Api/RolesListDropdown/index';

$route['toggle_write_permission'] = 'Api/Toggle/WritePermission/index';

$route['toggle_edit_permission'] = 'Api/Toggle/EditPermission/index';

$route['toggle_approve_permission'] = 'Api/Toggle/ApprovePermission/index';

$route['toggle_delete_permission'] = 'Api/Toggle/DeletePermission/index';

$route['update_employee_role'] = 'Api/UpdateEmployeeRole/index';
$route['roles_list'] = 'Api/GetRoles/index';
$route['delete_employee_from_pivot'] = 'Api/Pivot/DeleteEmployee/index';
$route['delete_module_from_pivot'] = 'Api/Pivot/DeleteModule/index';
$route['delete_site_from_pivot'] = 'Api/Pivot/DeleteSite/index';

$route['leave_detail'] = 'Api/LeaveDetails/index';

$route['schedule_detail'] = 'Api/ScheduleDetails/index';

$route['attendance_detail'] = 'Api/AttendanceDetails/index';

$route['summary_detail'] = 'Api/SummaryDetails/index';

$route['report_list'] = 'Api/Reporting/ReportList/index';

// $route['mobileApi/filtered_site_inventory/(:any)/(:any)'] = 'mobileApi/MobileApiFilteredSiteInventoryController/index/$1/$2';

