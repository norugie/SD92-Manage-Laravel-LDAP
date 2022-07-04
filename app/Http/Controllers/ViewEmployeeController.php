<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Ldap\User;
use App\Ldap\Group;
use LdapRecord\Models\Attributes\AccountControl;

class ViewEmployeeController extends Controller
{
    protected $fpdf;

    public function __construct ()
    {
        // Fetch config setup for locations, roles, and sub-departments
        $json = file_get_contents('cms/config.json');
        $this->config = json_decode($json, true);

        // Initialize FPDF for cards
        $this->fpdf = new Fpdf('L','mm',array(85,54));
    }

    /**
     * Return data for /employees page
     * 
     * @return \Illuminate\View\View
     */
    public function enabledEmployeeAccountsIndex ()
    {
        $employees = Group::findBy('cn', 'activestaff')->members()->get();
        return view('cms.employee.employee', [
            'employees' => $employees,
            'config' => $this->config
        ]);
    }

    /**
     * Return data for /employees/create page
     * 
     * @return \Illuminate\View\View
     */
    public function createEmployeeForm ()
    {
        return view('cms.employee.create.employee', [
            'config' => $this->config
        ]);
    }

    /**
     * Return data for /employees/create page
     * 
     * @param Object #employee
     * @return $employee_info
     */
    public function getEmployeeInfo (Object $employee)
    {
        $employee_info = [];

        // Base path for profile images
        $url = '/cms/images/users/';

        // Check image directory if profile image for user exists
        $image_directory = glob(public_path($url) . $employee->getFirstAttribute('uidNumber') ."*.png");
        if($image_directory ? $employee_pic = $url . pathinfo($image_directory[0], PATHINFO_BASENAME) : $employee_pic = $url . "user-placeholder.png");

        // Fetch employee groups data
        $groups = $employee->groups()->get();
        $locations = [];
        $sub_departments = [];
        $check = [];

        // Set up $check to compare against config setup
        foreach($this->config['locations'] as $key => $value): 
            array_push($check, $key);
        endforeach;

        // Separate set grous to $locations and $sub_departments, based off of $check value compared against config setup
        foreach($groups as $group):
            $group = $group->getName();
            if(in_array($group, $check) ? array_push($locations, $group) : array_push($sub_departments, $group));
        endforeach;

        // array_push($employee_info, $employee_pic, $locations, $sub_departments);
        $employee_info['employee_pic'] = $employee_pic;
        $employee_info['locations'] = $locations;
        $employee_info['sub_departments'] = $sub_departments;

        return $employee_info;
    }

    /**
     * Return data for /employees/{username}/{action} page
     *
     * @param String $username
     * @param String $action
     * @return \Illuminate\View\View
     */
    public function viewEmployeeProfileUpdate (String $username, String $action)
    {
        // Fetch employee data
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        // Redirect to /employees page if {username} is NULL
        if($employee === NULL) {
            $message = 'The user you are looking for does not exist in our directory.';
            $this->alertDetails('error', $message);
            return redirect('/cms/employees');
        } else {
            // Redirect to /employees page if {username} has a disabled account
            $uac = new AccountControl($employee->getFirstAttribute('userAccountControl'));
            if($uac->has(AccountControl::ACCOUNTDISABLE)){
                $message = 'The user you are looking for no longer has an active account in our directory.';
                $this->alertDetails('error', $message);
                return redirect('/cms/employees');
            }
        }
        
        $employee_info = $this->getEmployeeInfo($employee);

        // Set up path based on {action}
        // Default {action} value = "view"
        // Redirect paths {action} value = "view" - /employees/{username}/view, {action} value = "update" - /employees/{username}/update
        if(isset($action) && !empty($action) && $action == 'update' ? $path = 'update.employee' : $path = 'profile');

        return view('cms.employee.' . $path, [
            'employee' => $employee,
            'employee_pic' => $employee_info['employee_pic'],
            'config' => $this->config,
            'locations' => $employee_info['locations'],
            'sub_departments' => $employee_info['sub_departments']
        ]);
    }

    /**
     * Return data for /inactive page
     */
    public function disabledEmployeeAccountsIndex ()
    {
        $employees = Group::findBy('cn', 'inactivestaff')->members()->get();
        return view ('cms.inactive.inactive', [
            'employees' => $employees,
            'config' => $this->config
        ]);
    }

    /**
     * Handle process for downloading employee profile ID image
     *
     * @param String $username
     */
    public function viewEmployeeProfileIDImageDownload (String $username)
    {
        // Set employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        $employee_info = $this->getEmployeeInfo($employee);

        $name = $employee->getFirstAttribute('displayname');
        $department = $this->config['locations'][$employee->getFirstAttribute('department')]['name'];
        $address = $this->config['locations'][$employee->getFirstAttribute('department')]['address'];
        $city_province_postal = $this->config['locations'][$employee->getFirstAttribute('department')]['city'] . " " . 
                                $this->config['locations'][$employee->getFirstAttribute('department')]['province'] . " " . 
                                $this->config['locations'][$employee->getFirstAttribute('department')]['postal_code'];
        $phone = $this->config['locations'][$employee->getFirstAttribute('department')]['phone'];

        $logo = public_path('/nisgaa-icon.png');
        $barcode = public_path('/cms/images/barcode.png');
        $employee_pic = public_path($employee_info['employee_pic']);

        $this->fpdf->SetMargins(0, 0, 0);
        $this->fpdf->AddPage();
        $this->fpdf->SetFont('Arial', 'B', 10);
        $this->fpdf->SetTextColor(255, 255, 255);
        $this->fpdf->SetFillColor(224, 19, 24);
        $this->fpdf->Rect(0, 38, 85, 16, 'F');
        $this->fpdf->SetFillColor(0, 0, 0);
        $this->fpdf->Rect(0, 0, 85, 16, 'F');
        $this->fpdf->Rect(3, 15, 33, 33, 'F');
        $this->fpdf->SetXY(5.75, 7);
        $this->fpdf->Cell(28, 2.5, 'EMPLOYEE', 0, 0, 'C', FALSE);
        $this->fpdf->SetFont('Arial', '', 6.1);
        $this->fpdf->SetXY(36, 1.8);
        $this->fpdf->Cell(37, 2, $department, 0, 0, 'R', FALSE);
        $this->fpdf->SetFont('Arial', '', 6);
        $this->fpdf->SetXY(36, 5.5);
        $this->fpdf->Cell(37, 1, 'School District 92 (Nisga\'a)', 0, 0, 'R', FALSE);
        $this->fpdf->SetXY(36, 8);
        $this->fpdf->Cell(37, 1, $address, 0, 0, 'R', FALSE);
        $this->fpdf->SetXY(36, 10.4);
        $this->fpdf->Cell(37, 1, $city_province_postal, 0, 0, 'R', FALSE);
        $this->fpdf->SetXY(36, 13);
        $this->fpdf->Cell(37, 1, $phone, 0, 0, 'R', FALSE);
        $this->fpdf->Image($logo, 73.5, 1.5, 8.5, 13.3, 'PNG');
        $this->fpdf->SetTextColor(0, 0, 0);
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->SetXY(36, 22);
        $this->fpdf->Cell(49, 4, $name, 0, 0, 'C', FALSE);
        $this->fpdf->SetXY(36, 23.25);
        $this->fpdf->Image($barcode, 43, 28, 34, 6, 'PNG');
        $this->fpdf->Image($employee_pic, 3.2, 12.5, 32.5, 35, 'PNG');

        $this->fpdf->Output('card_' . $username . '.pdf', 'D');
    }
}