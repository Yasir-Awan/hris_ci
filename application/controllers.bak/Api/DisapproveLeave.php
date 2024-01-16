<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class DisapproveLeave extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ApiModel/DisapproveLeaveModel');
    }
    public function index_post()
    {
        $leaveId = $this->post('leave_id');
        $headers = apache_request_headers();
        $head = explode(" ", $headers['Authorization']);
        $token = $head[1];

        try {
            $this->load->helper('verifyAuthToken');
            $verifiedToken = verifyToken($token);
            if($verifiedToken){
                $shortHrs = null;
                $DisapproveLeave = new DisapproveLeaveModel;
                $leave = $DisapproveLeave->FetchLeave($leaveId);
                date_default_timezone_set('Asia/Karachi');
                if(!empty($leave)){
                    $startDate = $leave[0]['start_date'];
                    $lvStartTimeStamp = strtotime($startDate); // Convert the string to a timestamp
                    $leaveStart = date('Y-m-d H:i:s', $lvStartTimeStamp);
            
                    $endDate = $leave[0]['end_date'];
                    $lvEndTimeStamp = strtotime($endDate); // Convert the string to a timestamp
                    $leaveEnd = date('Y-m-d H:i:s', $lvEndTimeStamp); 
            
                    $leave_type = $leave[0]['leave_type'];
                    $bio_id = $leave[0]['bio_id'];

                    if($leave_type == 1){
                        // Format as 'HH:MM:SS'
                        $shortHrs = $leave[0]['short_hrs'];
                    }
                    // if($leave_type != 1){
                    //     $totalLeaveDays = $leave[0]['total_leave_days'];
                    //     $saturdays = $leave[0]['saturdays'];
                    //     $sundays = $leave[0]['sundays'];
                    // }
                    $employee_schedules_info = $DisapproveLeave->get_user_schedules($bio_id,$leaveStart,$leaveEnd);
                    if(empty($employee_schedules_info)){}
                    else{
                        // foreach($employee_schedules_info as $row){
                            $schedule_start = new DateTime($employee_schedules_info[0]['schedule_start']);
                            $schedule_end = new DateTime($employee_schedules_info[0]['schedule_end']);
                            $leave_start = new DateTime($leaveStart);
                            $leave_end = new DateTime($leaveEnd);
                            $total_leave_days_in_schedule = 0;
                            $leave_saturdays_in_schedule = 0;
                            $leave_sundays_in_schedule = 0;

                            if($employee_schedules_info[0]['schedule_start'] < $leaveStart && $leaveEnd < $employee_schedules_info[0]['schedule_end']){

                                if($leave_type==1){
                                    $time1 = new DateTime($shortHrs);
                                    $time2 = new DateTime($employee_schedules_info[0]['short_leave_hrs']);                                    
                                   // Calculate the interval between the two times
                                    $interval = $time1->diff($time2);
                                    // Format the result
                                    $updatedShortHrs = $interval->format('%H:%I:%S');

                                    if($updatedShortHrs == '00:00:00' && $employee_schedules_info[0]['total_leave_days'] == 0){
                                        $updating_data = [
                                            'short_leave_hrs' => null,
                                            'leave_status'=>0
                                        ];
                                    }elseif($employee_schedules_info[0]['short_leave_hrs']=='00:00:00'){
                                        $updating_data = [
                                            'short_leave_hrs' => null,
                                            'leave_status'=>0
                                        ];
                                    }else{
                                        $updating_data = [
                                            'short_leave_hrs' => $updatedShortHrs,
                                        ];
                                    }
                                    $DisapproveLeave->update_schedule($employee_schedules_info[0]['id'],$updating_data);
                                    // break; 
                                }
                                else{
                                    // Loop through each day and count the days and weekends
                                    while ($leave_start <= $leave_end) {
                                        // Increment the total days counter
                                        $total_leave_days_in_schedule++;
                                        // Check if the current day is a Saturday (6) or Sunday (0)
                                        $day_of_week = (int)$leave_start->format('w');
                                        if ($day_of_week === 6) {
                                            $leave_saturdays_in_schedule++;
                                        } elseif ($day_of_week === 0) {
                                            $leave_sundays_in_schedule++;
                                        }
                                        // Move to the next day
                                        $leave_start->modify('+1 day');
                                    }
                                    $current_weekends_of_leave_in_schedule = $leave_saturdays_in_schedule + $leave_sundays_in_schedule;
                                    $leave_days_only_saturdays_included = $total_leave_days_in_schedule - $leave_sundays_in_schedule;
                                    $leave_days_only_sundays_included = $total_leave_days_in_schedule - $leave_saturdays_in_schedule;
                                    $leave_days_weekends_excluded = $total_leave_days_in_schedule - $current_weekends_of_leave_in_schedule;

                                    $updated_total_leave_days_in_schedule = $employee_schedules_info[0]['total_leave_days'] - $total_leave_days_in_schedule;
                                    $updated_leave_days_only_saturdays_included = $employee_schedules_info[0]['leave_days_only_saturdays_included'] - $leave_days_only_saturdays_included;
                                    $updated_leave_days_only_sunday_included = $employee_schedules_info[0]['leave_days_only_sundays_included'] - $leave_days_only_sundays_included;
                                    $updated_leave_days_weekends_excluded = $employee_schedules_info[0]['leave_days_weekends_excluded'] - $leave_days_weekends_excluded;
                                    
                                    if($updated_total_leave_days_in_schedule == 0 && $employee_schedules_info[0]['short_leave_hrs']==null){
                                        $updating_data = [
                                            'total_leave_days' => null,
                                            'leave_days_only_saturdays_included' => null,
                                            'leave_days_only_sundays_included' => null,
                                            'leave_days_weekends_excluded'=>null,
                                            'leave_days_weekends_included'=>null,
                                            'leave_status'=>0
                                        ];
                                    }elseif($employee_schedules_info[0]['total_leave_days']==null){
                                        $updating_data = [
                                            'total_leave_days' => null,
                                            'leave_days_only_saturdays_included' => null,
                                            'leave_days_only_sundays_included' => null,
                                            'leave_days_weekends_excluded'=>null,
                                            'leave_days_weekends_included'=>null,
                                            'leave_status'=>0
                                        ];
                                    }else{
                                        $updating_data = [
                                            'total_leave_days' => $updated_total_leave_days_in_schedule,
                                            'leave_days_only_saturdays_included' => $updated_leave_days_only_saturdays_included,
                                            'leave_days_only_sundays_included' => $updated_leave_days_only_sunday_included,
                                            'leave_days_weekends_excluded'=>$updated_leave_days_weekends_excluded,
                                        ];
                                    }
                                    $DisapproveLeave->update_schedule($employee_schedules_info[0]['id'],$updating_data);
                                    // break;
                                }
                            }
                            if($leaveStart<$employee_schedules_info[0]['schedule_start'] && $employee_schedules_info[0]['schedule_end']<$leaveEnd){
                                 // Loop through each day and count the days and weekends
                                 while ($schedule_start <= $schedule_end) {
                                    // Increment the total days counter
                                    $total_leave_days_in_schedule++;
                                    // Check if the current day is a Saturday (6) or Sunday (0)
                                    $day_of_week = (int)$schedule_start->format('w');
                                    if ($day_of_week === 6) {
                                        $leave_saturdays_in_schedule++;
                                    } elseif ($day_of_week === 0) {
                                        $leave_sundays_in_schedule++;
                                    }
                                    // Move to the next day
                                    $schedule_start->modify('+1 day');
                                }
                                $current_weekends_of_leave_in_schedule = $leave_saturdays_in_schedule + $leave_sundays_in_schedule;
                                $leave_days_only_saturdays_included = $total_leave_days_in_schedule - $leave_sundays_in_schedule;
                                $leave_days_only_sundays_included = $total_leave_days_in_schedule - $leave_saturdays_in_schedule;
                                $leave_days_weekends_excluded = $total_leave_days_in_schedule - $current_weekends_of_leave_in_schedule;

                                $updated_total_leave_days_in_schedule = $employee_schedules_info[0]['total_leave_days'] - $total_leave_days_in_schedule;
                                $updated_leave_days_only_saturdays_included = $employee_schedules_info[0]['leave_days_only_saturdays_included'] - $leave_days_only_saturdays_included;
                                $updated_leave_days_only_sunday_included = $employee_schedules_info[0]['leave_days_only_sundays_included'] - $leave_days_only_sundays_included;
                                $updated_leave_days_weekends_excluded = $employee_schedules_info[0]['leave_days_weekends_excluded'] - $leave_days_weekends_excluded;
                                
                                if($updated_total_leave_days_in_schedule == 0 && $employee_schedules_info[0]['short_leave_hrs']==null){
                                    $updating_data = [
                                        'total_leave_days' => null,
                                        'leave_days_only_saturdays_included' => null,
                                        'leave_days_only_sundays_included' => null,
                                        'leave_days_weekends_excluded'=>null,
                                        'leave_days_weekends_included'=>null,
                                        'leave_status'=>0
                                    ];
                                }elseif($employee_schedules_info[0]['total_leave_days']==null){
                                    $updating_data = [
                                        'total_leave_days' => null,
                                        'leave_days_only_saturdays_included' => null,
                                        'leave_days_only_sundays_included' => null,
                                        'leave_days_weekends_excluded'=>null,
                                        'leave_days_weekends_included'=>null,
                                        'leave_status'=>0
                                    ];
                                }else{
                                    $updating_data = [
                                        'total_leave_days' => $updated_total_leave_days_in_schedule,
                                        'leave_days_only_saturdays_included' => $updated_leave_days_only_saturdays_included,
                                        'leave_days_only_sundays_included' => $updated_leave_days_only_sunday_included,
                                        'leave_days_weekends_excluded'=>$updated_leave_days_weekends_excluded,
                                    ];
                                }
                                $DisapproveLeave->update_schedule($employee_schedules_info[0]['id'],$updating_data);
                                // break;
                            }
                            if($leaveStart<$employee_schedules_info[0]['schedule_start'] && $leaveEnd<$employee_schedules_info[0]['schedule_end']){
                                // Loop through each day and count the days and weekends
                                while ($schedule_start <= $leave_end) {
                                    // Increment the total days counter
                                    $total_leave_days_in_schedule++;
                                    // Check if the current day is a Saturday (6) or Sunday (0)
                                    $day_of_week = (int)$schedule_start->format('w');
                                    if ($day_of_week === 6) {
                                        $leave_saturdays_in_schedule++;
                                    } elseif ($day_of_week === 0) {
                                        $leave_sundays_in_schedule++;
                                    }
                                    // Move to the next day
                                    $schedule_start->modify('+1 day');
                                }
                                $current_weekends_of_leave_in_schedule = $leave_saturdays_in_schedule + $leave_sundays_in_schedule;
                                $leave_days_only_saturdays_included = $total_leave_days_in_schedule - $leave_sundays_in_schedule;
                                $leave_days_only_sundays_included = $total_leave_days_in_schedule - $leave_saturdays_in_schedule;
                                $leave_days_weekends_excluded = $total_leave_days_in_schedule - $current_weekends_of_leave_in_schedule;

                                $updated_total_leave_days_in_schedule = $employee_schedules_info[0]['total_leave_days'] - $total_leave_days_in_schedule;
                                $updated_leave_days_only_saturdays_included = $employee_schedules_info[0]['leave_days_only_saturdays_included'] - $leave_days_only_saturdays_included;
                                $updated_leave_days_only_sunday_included = $employee_schedules_info[0]['leave_days_only_sundays_included'] - $leave_days_only_sundays_included;
                                $updated_leave_days_weekends_excluded = $employee_schedules_info[0]['leave_days_weekends_excluded'] - $leave_days_weekends_excluded;
                                
                                if($updated_total_leave_days_in_schedule == 0 && $employee_schedules_info[0]['short_leave_hrs']==null){
                                    $updating_data = [
                                        'total_leave_days' => null,
                                        'leave_days_only_saturdays_included' => null,
                                        'leave_days_only_sundays_included' => null,
                                        'leave_days_weekends_excluded'=>null,
                                        'leave_days_weekends_included'=>null,
                                        'leave_status'=>0
                                    ];
                                }elseif($employee_schedules_info[0]['total_leave_days']== null){
                                    $updating_data = [
                                        'total_leave_days' => null,
                                        'leave_days_only_saturdays_included' => null,
                                        'leave_days_only_sundays_included' => null,
                                        'leave_days_weekends_excluded'=>null,
                                        'leave_days_weekends_included'=>null,
                                        'leave_status'=>0
                                    ];
                                }else{
                                    $updating_data = [
                                        'total_leave_days' => $updated_total_leave_days_in_schedule,
                                        'leave_days_only_saturdays_included' => $updated_leave_days_only_saturdays_included,
                                        'leave_days_only_sundays_included' => $updated_leave_days_only_sunday_included,
                                        'leave_days_weekends_excluded'=>$updated_leave_days_weekends_excluded,
                                    ];
                                }
                                $DisapproveLeave->update_schedule($employee_schedules_info[0]['id'],$updating_data);
                                // break;
                            }
                            if($employee_schedules_info[0]['schedule_start']<$leaveStart && $employee_schedules_info[0]['schedule_end']<$leaveEnd){
                                 // Loop through each day and count the days and weekends
                                 while ($leave_start <= $schedule_end) {
                                    // Increment the total days counter
                                    $total_leave_days_in_schedule++;
                                    // Check if the current day is a Saturday (6) or Sunday (0)
                                    $day_of_week = (int)$leave_start->format('w');
                                    if ($day_of_week === 6) {
                                        $leave_saturdays_in_schedule++;
                                    } elseif ($day_of_week === 0) {
                                        $leave_sundays_in_schedule++;
                                    }
                                    // Move to the next day
                                    $leave_start->modify('+1 day');
                                }
                                $current_weekends_of_leave_in_schedule = $leave_saturdays_in_schedule + $leave_sundays_in_schedule;
                                $leave_days_only_saturdays_included = $total_leave_days_in_schedule - $leave_sundays_in_schedule;
                                $leave_days_only_sundays_included = $total_leave_days_in_schedule - $leave_saturdays_in_schedule;
                                $leave_days_weekends_excluded = $total_leave_days_in_schedule - $current_weekends_of_leave_in_schedule;

                                $updated_total_leave_days_in_schedule = $employee_schedules_info[0]['total_leave_days'] - $total_leave_days_in_schedule;
                                $updated_leave_days_only_saturdays_included = $employee_schedules_info[0]['leave_days_only_saturdays_included'] - $leave_days_only_saturdays_included;
                                $updated_leave_days_only_sunday_included = $employee_schedules_info[0]['leave_days_only_sundays_included'] - $leave_days_only_sundays_included;
                                $updated_leave_days_weekends_excluded = $employee_schedules_info[0]['leave_days_weekends_excluded'] - $leave_days_weekends_excluded;
                                
                                if($updated_total_leave_days_in_schedule == 0 && $employee_schedules_info[0]['short_leave_hrs']==null){
                                    $updating_data = [
                                        'total_leave_days' => null,
                                        'leave_days_only_saturdays_included' => null,
                                        'leave_days_only_sundays_included' => null,
                                        'leave_days_weekends_excluded'=>null,
                                        'leave_days_weekends_included'=>null,
                                        'leave_status'=>0
                                    ];
                                }elseif($employee_schedules_info[0]['total_leave_days']== null){
                                    $updating_data = [
                                        'total_leave_days' => null,
                                        'leave_days_only_saturdays_included' => null,
                                        'leave_days_only_sundays_included' => null,
                                        'leave_days_weekends_excluded'=>null,
                                        'leave_days_weekends_included'=>null,
                                        'leave_status'=>0
                                    ];
                                }else{
                                    $updating_data = [
                                        'total_leave_days' => $updated_total_leave_days_in_schedule,
                                        'leave_days_only_saturdays_included' => $updated_leave_days_only_saturdays_included,
                                        'leave_days_only_sundays_included' => $updated_leave_days_only_sunday_included,
                                        'leave_days_weekends_excluded'=>$updated_leave_days_weekends_excluded,
                                    ];
                                }
                                $DisapproveLeave->update_schedule($employee_schedules_info[0]['id'],$updating_data);
                                // break;
                            }
                        // }
                    }
                }
                    $leaveInfo = $DisapproveLeave->DisapproveLeave($leaveId);
                        $resp = array('msg'=>'leave Disapproved','status'=>'200');
                        $this->response($resp, 200);
            }
        }
        catch(Exception $e){
            $error = array("status"=>401,"message"=>"Invalid Token Provided","success"=>"false");
            $this->response($error);
        }
    }
}
