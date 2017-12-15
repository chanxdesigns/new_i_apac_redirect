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

    /**
     * Main Function
     * To run all functions sequentially
     *
     * @param $status string
     * @param $projectid string
     * @param $respid string
     * @param $country string
     *
     * @return RedirectResponse
     */
    public function main($status, $projectid, $respid, $country)
    {

        // Send notification for response status
        if ($status == "Quotafull") {
            $client = new \Maknz\Slack\Client('https://hooks.slack.com/services/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');
            $client->attach([
                'fallback' => 'Current Quotafull Details',
                'text' => 'Current Quotafull Details',
                'color' => 'warning',
                'fields' => [
                    [
                        'title' => 'Project ID',
                        'value' => $projectid,
                        'short' => true // whether the field is short enough to sit side-by-side other fields, defaults to false
                    ],
                    [
                        'title' => 'Resp ID',
                        'value' => $respid,
                        'short' => true
                    ],
                    [
                        'title' => 'Country',
                        'value' => $this->makeCountry($country),
                        'short' => true
                    ]
                ]
            ])->send("*".$projectid."* - Quotafull Alerts");
        }

        //Store the passed-in URL parameters to private properties
        $this->status = $status;
        $this->projectid = $projectid;
        $this->respid = $respid;
        $this->country = $country;

        // If External ID is present
        if (!empty($_GET['extid'])) {

            // Store Ext ID as respid
            $this->respid = $_GET['extid'];

            // Get the Pre-start UID data
            $uid = DB::table('survey_prestart')
                ->where('project_id', $this->projectid)
                ->where('user_id', $this->respid)
                ->where('country', $this->country)
                ->first();

            // If Pre-start UID data stored
            // Then get Vendor ID from the row
            if (count($uid)) {
                $this->vendor = $uid->vendor;
            }
        } else {
            // Get the Pre-start UID data
            $uid = DB::table('survey_prestart')
                ->where('project_id', $this->projectid)
                ->where('user_id', $this->respid)
                ->where('country', $this->country)
                ->first();

            // If Pre-start UID data stored
            // Then get Vendor ID from the row
            if (count($uid)) {
                $this->vendor = $uid->vendor;
            }
        }

        // Bootstrap and register the storing
        // and verification mechanism
        if ($this->verifyId()) {
            $this->storeData();
            $this->getLinksAndAbout();
            return $this->redirect();
        }
    }

    /**
     * Verify Passed UID in database
     **/
    public function verifyId()
    {
        //Verify UID from Database
        $status = DB::table('resp_counters')->where('respid', '=', $this->respid)->where('projectid', '=', $this->projectid)->get();
        return count($status) > 0 ? false : true;
    }

    /**
     * Get Redirect Links and Project About from Database
     **/
    public function getLinksAndAbout()
    {
        //Get Links From DB
        $links = DB::table('projects_list')->select('C_Link', 'T_Link', 'Q_Link', 'About')->where('Project ID', '=', $this->projectid)->where('Vendor', '=', $this->vendor)->where('Country', '=', $this->country)->get();
        if (count($links)) {
            $this->t_link = $links[0]->T_Link;
            $this->c_link = $links[0]->C_Link;
            $this->q_link = $links[0]->Q_Link;
            $this->about = $links[0]->About;
        } else {
            $this->t_link = "";
            $this->c_link = "";
            $this->q_link = "";
            $this->about = "";
        }
    }

    public function makeCountry ($country) {
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

        return $this->country;
    }

    /**
     * Get City
     */
    protected function getCity($ip = '') {
        $new_ip = explode(",", $ip)[0];
        return unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$new_ip));
    }

    /**
     * Store Data into the Server
     **/
    public function storeData()
    {
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
        $start_time = DB::table('survey_prestart')->where('user_id', $this->respid)->latest("started_on")->first();
        if (count($start_time)) {
            RespCounter::create(
                [
                    "respid" => $this->respid,
                    "projectid" => $this->projectid,
                    "Languageid" => $this->country,
                    "city" => $this->getCity($ip)["geoplugin_city"],
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
     * Redirect to the redirect link
     */
    public function redirect()
    {
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
                $ex[0] = $ex[0] . $this->respid;
                //Join the URL indices array into a single URL link
                $ex = implode("", $ex);
                //Insert the Edited Link to the URL array
                $url[$i] = $ex;
            }
            //var_dump($url);
        }
        //Redirect to the set redirect links
        //dd($url);
        if ($this->status === "Complete") {
            if (empty($this->c_link)) {
                return redirect()->route('completed', [$this->respid]);
            } else {
                return redirect()->away($url[0]);
            }
        } elseif ($this->status === "Incomplete") {
            if (empty($this->t_link)) {
                return redirect()->route('terminated', [$this->respid]);
            } else {
                return redirect()->away($url[2]);
            }
        } elseif ($this->status === "Quotafull") {
            if (empty($this->q_link)) {
                return redirect()->route('quotafull', [$this->respid]);
            } else {
                return redirect()->away($url[1]);
            }
        } elseif ($this->status === "Drop_Off") {
            if (empty($this->d_link)) {
                return redirect()->away($url[2]);
            } else {
                return redirect()->away($url[2]);
            }
        } elseif ($this->status === "Mobile_Term") {
            if (empty($this->d_link)) {
                return redirect()->away($url[2]);
            } else {
                return redirect()->away($url[2]);
            }
        } elseif ($this->status === "Security_Term") {
            if (empty($this->d_link)) {
                return redirect()->away($url[2]);
            } else {
                return redirect()->away($url[2]);
            }
        }
    }
}
