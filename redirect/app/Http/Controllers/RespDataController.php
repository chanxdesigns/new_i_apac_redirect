<?php

namespace App\Http\Controllers;

use App\RespCounter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RespDataController extends Controller
{
    private $status;
    private $respid;
    private $country;
    private $projectid;
    private $about;
    private $vendor;
    private $t_link;
    private $c_link;
    private $q_link;
    private $d_link;

    //Main Function
    public function main ($status,$projectid,$respid,$country) {
        //Store the passed-in URL parameters to private properties
        $this->status = $status;
        $this->projectid = $projectid;
        $this->respid = $respid;
        $this->country = $country;

        // If External ID is present
        if (!empty($_GET['extid'])) {
            /**
            if (preg_match('/^([A-Za-z0-9\-\_]){15}$/i', $_GET['extid'])) {
                $this->vendor = 'IPO';
            }
            elseif (preg_match('/^[A-Z0-9]{17}$/i', $_GET['extid'])) {
                $this->vendor = 'RICKIE';
            }
            else {
                $this->vendor = substr($_GET['extid'], 13);
            }
            **/
            
            // Store Ext ID as respid
            $this->respid = $_GET['extid'];
            
            $uid = DB::table('survey_prestart')
                ->where('project_id', $this->projectid)
                ->where('user_id', $this->respid)
                ->where('country', $this->country)
                ->first();

            if (count($uid)) {
                $this->vendor = $uid->vendor;
            }
        }
        else {
            //Check For Hard-Coded Route Vendor ID Presence
//            if (preg_match('/^[A-Z0-9]{17}$/i', $respid)) {
//                $this->vendor = 'RICKIE';
//            } elseif (preg_match('/\d{5}\-[A-Z0-9]{16,20}$/i', $respid)) {
//                $this->vendor = 'PL';
//            } elseif (preg_match('/^([A-Za-z0-9\-\_]){15}$/i', $respid)) {
//                $this->vendor = 'IPO';
//            } elseif (preg_match('/^\d{8}[A-Za-z]{2}\d{10}$/i', $respid)) {
//                $this->vendor = 'SL';
//            }
//            else {
//                $this->vendor = substr($respid,13);
//            }
            $uid = DB::table('survey_prestart')
                ->where('project_id', $this->projectid)
                ->where('user_id', $this->respid)
                ->where('country', $this->country)
                ->first();

            if (count($uid)) {
                $this->vendor = $uid->vendor;
            }
        }

        //Run the starting function
        if ($this->verifyId()) {
            $this->storeData();
            $this->getLinksAndAbout();
            //$this->prjUpdate();
            return $this->redirect();
        }
    }

    /**
     * Verify Passed UID in database
     **/
    public function verifyId () {
        //Verify UID from Database
        $status = DB::table('resp_counters')->where('respid', '=', $this->respid)->where('projectid', '=', $this->projectid)->get();
        return count($status) > 0 ? false : true;
    }
    /**
     * Get Redirect Links and Project About from Database
     **/
    public function getLinksAndAbout () {
        //Get Links From DB
        $links = DB::table('projects_list')->select('C_Link','T_Link','Q_Link','About')->where('Project ID', '=', $this->projectid)->where('Vendor', '=', $this->vendor)->where('Country','=', $this->country)->get();
        if (count($links)) {
        $this->t_link = $links[0]->T_Link;
        $this->c_link = $links[0]->C_Link;
        $this->q_link = $links[0]->Q_Link;
        $this->about  = $links[0]->About;
        }
        else {
            $this->t_link = "";
            $this->c_link = "";
            $this->q_link = "";
            $this->about = "";
        }
    }

    /**
     * Store Data into the Server
     **/
    public function storeData () {
        //Translate Country Code to Country Name
        $country = $this->country;
        switch ($country) {
            case "ZH":
                $this->country = "China";
                break;
            case "JP":
                $this->country = "Japan";
                break;
            case "ROK":
                $this->country = "South Korea";
                break;
            case "PH":
                $this->country = "Philippines";
                break;
            case "ID":
                $this->country = "Indonesia";
                break;
            case "MY":
                $this->country = "Malaysia";
                break;
            case "VN":
                $this->country = "Vietnam";
                break;
            case "IN":
                $this->country = "India";
                break;
            case "TH":
                $this->country = "Thailand";
                break;
            case "HK":
                $this->country = "Hong Kong";
                break;
            case "SG":
                $this->country = "Singapore";
                break;
            case "UAE":
                $this->country = "UAE";
                break;
            case "KSA":
                $this->country = "Saudi Arabia";
                break;
            case "JO":
                $this->country = "Jordan";
                break;
            case "SA":
                $this->country = "South Africa";
                break;
            case "AUS":
                $this->country = "Australia";
                break;
            case "TW":
                $this->country = "Taiwan";
                break;
        }
        //IP Address Of the respondent
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

        //Store the respondent project details data to the Resp Counter table
        DB::table('survey_prestart')->where('user_id', $this->respid)->latest("started_on")->first()
        if (count($start_time)) {
            RespCounter::create(
                [
                    "respid" => $this->respid,
                    "projectid" => $this->projectid,
                    "Languageid" => $this->country,
                    "status" => $this->status,
                    "IP" => $ip,
                    "starttime" => $start_time->started_on,
                    "enddate" => Carbon::now()->timezone('Asia/Kolkata'),
                    "created_at" => Carbon::now()->timezone('Asia/Kolkata'),
                    "updated_at" => Carbon::now()->timezone('Asia/Kolkata')
                ]
            );
        }
    }

    /**
     * Project related sheet update
     */
//    public function prjUpdate () {
//        $status = "";
//
//        switch($this->status) {
//            case "Complete":
//                $status = "Completes";
//                break;
//            case "Incomplete":
//                $status = "Terminates";
//                break;
//            case "Quotafull":
//                $status = "Quotafull";
//                break;
//        }
//
//        $count = ProjectsList::where('Project ID','=',$this->projectid)->value($status);
//        ProjectsList::where('Project ID','=',$this->projectid)->update([$status => $count + 1]);
//    }

    /**
     * Redirect to the redirect link
     */
    public function redirect () {
        if (!empty($this->c_link) || !empty($this->q_link) || !empty($this->t_link) || !empty($this->d_link)) {
            //Store the links in URL Array
            $url = [$this->c_link, $this->q_link, $this->t_link, $this->d_link];

            //Edit the links to accept User ID and Project ID
            for ($i = 0; $i < count($url); $i++) {
                //Get Individual URL form the array
                $link = $url[$i];
                //Explode the URL into Array indices with "$" as delimiter
                $ex = explode('$respid', $link);
                //Append the UID to the link
                $ex[0] = $ex[0].$this->respid;
                //Join the URL indices array into a single URL link
                $ex = implode("", $ex);
                //Insert the Edited Link to the URL array
                $url[$i] = $ex;
            }
            //var_dump($url);
        }
        //Redirect to the set redirect links
        //dd($url);
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
        } elseif ($this->status === "Drop_Off")
        {
            if (empty($this->d_link)) {
                return redirect()->away($url[2]);
            } else {
                return redirect()->away($url[2]);
            }
        } elseif ($this->status === "Mobile_Term")
        {
            if (empty($this->d_link)) {
                return redirect()->away($url[2]);
            } else {
                return redirect()->away($url[2]);
            }
        } elseif ($this->status === "Security_Term")
        {
            if (empty($this->d_link)) {
                return redirect()->away($url[2]);
            } else {
                return redirect()->away($url[2]);
            }
        }
    }
}
