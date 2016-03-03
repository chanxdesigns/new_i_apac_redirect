<?php

namespace App\Http\Controllers;

use App\ProjectsList;
use App\RespCounter;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class RespDataController extends Controller
{
    private $status;
    private $respid;
    private $country;
    private $projectid;
    private $t_link;
    private $c_link;
    private $q_link;

    //Main Function
    public function main ($status,$projectid,$respid,$country) {
        //Store the passed-in URL parameters to private properties
        $this->status = $status;
        $this->projectid = $projectid;
        $this->respid = $respid;
        $this->country = $country;

        //Run the starting function
        if ($this->verifyId()) {
            $this->getLinks();
            $this->storeData();
            $this->prjUpdate();
            $this->redirect();
        }
    }

    /**
     * Verify Passed UID in database
     **/
    public function verifyId () {
        $status = DB::table('resp_counters')->where('respid', '=', $this->respid)->where('projectid', '=', $this->projectid)->get();
        return count($status) > 0 ? false : true;
    }
    /**
     * Get Redirect Links from Database
     **/
    public function getLinks () {
        $links = DB::table('projects_list')->select('C_Link','T_Link','Q_Link')->where('Project ID', '=', 'ADBNM')->get();
        $this->t_link = $links[0]->T_Link;
        $this->c_link = $links[0]->C_Link;
        $this->q_link = $links[0]->Q_Link;
    }

    /**
     * Store Data into the Server
     **/
    public function storeData () {
        $ip = $_SERVER['REMOTE_ADDR'];
        //Store the respondent project details data to the Resp Counter table
        RespCounter::create(
            [
                "respid" => $this->respid,
                "projectid" => $this->projectid,
                "Languageid" => $this->country,
                "status" => $this->status,
                "IP" => $ip,
                "enddate" => Carbon::now()->timezone('Asia/Kolkata')
            ]
        );
    }

    /**
     * Project related sheet updation
     */
    public function prjUpdate () {
        $status = "";

        switch($this->status) {
            case "Complete":
                $status = "Completes";
                break;
            case "Incomplete":
                $status = "Terminates";
                break;
            case "Quotafull":
                $status = "Quotafull";
                break;
        }

        $db = DB::table('projects_list')->select($status)->where('Project ID','=',$this->projectid)->get();
        $count = $db[0]->{$status};

        ProjectsList::where('Project ID','=',$this->projectid)->update([$status => $count + 1]);
    }

    /**
     * Redirect to the redirect link
     */
    public function redirect () {
        //Redirect to the set redirect links
        if ($this->status === "Complete")
        {
            return redirect($this->c_link);
        } elseif ($this->status === "Incomplete")
        {
            redirect($this->t_link);
        } elseif ($this->status === "Quotafull")
        {
            redirect($this->q_link);
        }
    }
}
