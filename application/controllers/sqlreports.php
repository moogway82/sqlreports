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
        
        $reportData = $this->db->get_where('report', array('name' => $report));
        foreach($reportData->result_array() as $row) {
            $sql = $row['sql'];
            $pageData['desc'] = $row['notes'];
            $pageData['name'] = $row['name'];
            $pageData['sql'] = $sql;
        }
        $pageData['queryResult'] = $this->db->query($sql);
        $pageData['fields'] = $pageData['queryResult']->list_fields();
        $this->load->view('read_report', $pageData);
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
            
        $reportData = $this->db->get_where('report', array('name' => $report));
        foreach($reportData->result_array() as $row) {
            $sql = $row['sql'];
        }   
        $data['queryResult'] = $this->db->query($sql);
        $sqlQuery = $this->db->query($sql);
        
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
}

/* End of file sqlreports.php */
/* Location: ./application/controllers/sqlreports.php */