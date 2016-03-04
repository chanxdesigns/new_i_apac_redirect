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
            return $this->redirect();
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
        $links = DB::table('projects_list')->select('C_Link','T_Link','Q_Link')->where('Project ID', '=', $this->projectid)->get();
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

        $count = ProjectsList::where('Project ID','=',$this->projectid)->value($status);
        ProjectsList::where('Project ID','=',$this->projectid)->update([$status => $count + 1]);
    }

    /**
     * Redirect to the redirect link
     */
    public function redirect () {
        if (!empty($this->c_link) || !empty($this->q_link) || !empty($this->t_link)) {
            //Store the links in URL Array
            $url = [$this->c_link, $this->q_link, $this->t_link];
            //Edit the links to accept User ID and Project ID
            for ($i = 0; $i < count($url); $i++) {
                //Get Individual URL form the array
                $link = $url[$i];
                //Explode the URL into Array indices with "$" as delimiter
                $ex = explode("$", $link);
                //Check for "respid" text in the URL indices Array
                if (in_array("respid", $ex)) {
                    for ($v = 0; $v < count($ex); $v++) {
                        if ($ex[$v] === "respid") {
                            //For every "respid"
                            //Replace it using the User ID in the URL
                            $ex[$v] = $this->respid;
                        }
                    }
                }
                //Join the URL indices array into a single URL link
                $ex = implode("", $ex);
                //Insert the Edited Link to the URL array
                $url[$i] = $ex;
            }
        }
        //Redirect to the set redirect links
        if ($this->status === "Complete")
        {

            if (empty($this->c_link)) {
                return redirect()->route('completed',[$this->respid]);
            } else {
                return redirect()->away($url[0]);
            }
        } elseif ($this->status === "Incomplete")
        {
            if (empty($this->t_link)) {
                return redirect()->route('terminated',[$this->respid]);
            } else {
                return redirect()->away($url[2]);
            }
        } elseif ($this->status === "Quotafull")
        {
            if (empty($this->q_link)) {
                return redirect()->route('quotafull',[$this->respid]);
            } else {
                return redirect()->away($url[1]);
            }
        }
    }
}
