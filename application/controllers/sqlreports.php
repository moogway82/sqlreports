<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SQLReports extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
    
    public  function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }
    
	public function index() {
        $data['query'] = $this->db->get('report');
        $this->load->view('welcome', $data);
	}
    
    public function reports_json() {
        $reportsQuery = $this->db->get('report');
        $reports = array();
        foreach($reportsQuery->result_array() as $row) {
            $reports[] = array('name' => $row['name'], 'slug' => $row['slug']);
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($reports));
        
    }
    
    /**
     *  This is the controller to just show the report
     *
     *  Plan:
     *   - Get the SQL
     *   - Run the SQL
     *   - Get the fields from the result to setup the colModel
     **/
    
    public function viewreport($report = FALSE) {
        if(!$report) { exit(0); }
        $pageData = $this->runSQL($report, true);
        $this->load->view('read_report', $pageData);
    }
    
    /**
     *  To avoid redundancy, this function returns the data for the SQL.  It can return just the fields or all the data.
     *
     **/
    
    private function runSQL($report, $justFields = FALSE) {
        $this->db->select('report.notes, report.name AS rname, report.slug, report.sql, database.connection, database.database');
        $this->db->from('report');
        $this->db->where('report.slug', $report);
        $this->db->join('database', 'database.id = report.database', 'left');
        $reportData = $this->db->get();
        foreach($reportData->result_array() as $row) {
            //log_message('debug', print_r($row, true));
            $sql = $row['sql'];
            $pageData['desc'] = $row['notes'];
            $pageData['name'] = $row['rname'];
            $pageData['slug'] = $row['slug'];
            
            $connection = $row['connection'];
            $database = $row['database'];
        }
        $dsn = "mysql://$connection/$database";
        $otherDB = $this->load->database($dsn, TRUE);
        $parsedResults = $this->parseVars($sql);
        $sql = $parsedResults[0];
        $pageData['sql'] = $sql;
        if(count($parsedResults) > 1) {
            $pageData['varFields'] = $parsedResults[1];
            $pageData['varValues'] = $parsedResults[2];
        } else {
            $pageData['varFields'] = array();
            $pageData['varValues'] = array();
        }
        if($justFields) {
            /* Should check the SQL for a LIMIT statement, if not there, then add one to return only 1 row and not waste a query. */
            //echo("sql:"); print_r($sql); exit(0);
            $pageData['queryResult'] = $otherDB->query($sql);
            //echo("queryResult:"); print_r($pageData['queryResult']); exit(0);
            if(!$pageData['queryResult']) {
                echo("No results from SQL:\n".$sql."\n"); print_r($pageData['queryResult']); exit(0);
            }
            $pageData['fields'] = $pageData['queryResult']->list_fields();
            
            log_message('debug', print_r($pageData['fields'], true));
            $pageData['numRows'] = $pageData['queryResult']->num_rows;
        } else {
            if(isset($_REQUEST['sidx'])) {
                $sidx = $_REQUEST['sidx'];   
            } else {
                $sidx = false;
            }
            if(isset($_REQUEST['sord'])) {
                $sord = $_REQUEST['sord'];
            } else {
                $sord = false;
            }
            if($sord && $sidx) {
                $sql = $this->addSorting($sql, $sidx, $sord);
            }
            $pageData['queryResult'] = $otherDB->query($sql);
        }
        return $pageData;
    }
    
    /**
     *  Return the data for displaying in the cells of the table.
     *
     *  Plan:
     *      - Get the report row in the DB
     *      - Run the SQL in that row
     *      - Return the data for that SQL
     *      - Also get the description
     *      - DONE!
     **/
    
    public function tabledata($report = FALSE) {
        if(!$report) { exit(0); }
        
        $runQuery = $this->runSQL($report);
        $sqlQuery = $runQuery['queryResult'];
        
        log_message('debug', print_r($sqlQuery, true));
        $rows = array();
        foreach($sqlQuery->result_array() as $sqlRow) {
            $sqlRowVals = array_values($sqlRow);
            $rows[] = array(
                'id' => $sqlRowVals[0],
                'cell' => $sqlRowVals
            );
        }
        
        $jsonObj = array(
            'total' => 1,
            'page' => 1,
            'records' => $this->db->count_all_results(),
            'rows' => $rows
        );
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($jsonObj));
    
$testData = <<<EOT
{
"total":"1",
"page":null,
"records":"3",
"rows":
    [
        {
            "id":"1",
            "cell": ["1","MP"]
        },
        {
            "id":"2",
            "cell": ["2","Barbarian"]
        },
        {
            "id":"3",
            "cell":["3","Ninja"]
        }
    ]
}
EOT;
        
        // echo $testData;
    }
    
    /**
     *  Helper function to replace all the moustached placeholders in the SQL query.  Everything gets surrounded by quotes in the SQL, hope
     *  that's ok?
     *  
     */
    
    private function parseVars($sql) {
        $result = preg_match_all('/{{([a-zA-Z0-9_\-]+)\|("?[^"}]+"?)}}/', $sql, $matches);
        if($result > 0) {
            //print_r($matches); exit(0);
            $placeholders = $matches[0];
            //$varNames = $matches[1];
            //$varValues = $matches[2];

            for($i = 0; $i < count($placeholders); $i++) {
                //print_r($_REQUEST); exit(0);
                preg_match('/{{([a-zA-Z0-9_\-]+)\|("?[^"}]+"?)}}/', $placeholders[$i], $matches);
                //print_r($matches);
                $varName = $matches[1];
                $varValue = $matches[2];
                //print_r($varName);
                //print_r($varValue);
                //exit(0);
                if(array_key_exists($varName, $_REQUEST)) {
                    $sql = str_replace($placeholders[$i], $_REQUEST["$varName"], $sql);
                    $varValues["$varName"] = $_REQUEST["$varName"];
                } else {
                    $sql = str_replace($placeholders[$i], $varValue, $sql);
                    $varValues["$varName"] = $varValue;
                }
                $varNames[] = $varName;
            }
            //print_r($sql); exit(0);
            return array($sql, array_unique($varNames), array_unique($varValues));
        } else {
            return array($sql);    
        }
    }
    
    private function addSorting($sql, $sidx, $sord) {
        $sql = str_replace(';', '', $sql);
        if(stripos($sql, "ORDER") === FALSE) {
            $orderByLine = " ORDER BY ".$sidx." ".$sord;
            $limitByStart = stripos($sql, "LIMIT");
            if($limitByStart !== FALSE) {
                $sql = substr($sql, 0, $limitByStart - 1).$orderByLine." ".substr($sql, $limitByStart, strlen($sql));
            } else {
                $sql = $sql.$orderByLine;
            }
        }
        //print_r($sql); exit(0);
        return $sql;
    }
    
}

/* End of file sqlreports.php */
/* Location: ./application/controllers/sqlreports.php */