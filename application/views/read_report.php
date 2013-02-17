<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$name?></title>

<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>css/sqlreports.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>css/ui-lightness/jquery-ui-1.8.19.custom.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>css/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>css/jquery-ui-timepicker-addon.css" />


<style type="text/css">
html, body {
    margin: 0;
    padding: 0;
}
</style>

<script src="<?=base_url()?>js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="<?=base_url()?>js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
<script src="<?=base_url()?>js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>


<script type="text/javascript">
$(function(){ 
    $("#list").jqGrid({
        url:'<?=site_url('/sqlreports/tabledata').'/'.$this->uri->segment(3)."?".$_SERVER['QUERY_STRING']?>',
        /*shrinkToFit: true,*/
        width: 960,
        height: 'auto',
        datatype: 'json',
        mtype: 'GET',
        colNames:[
            <?php foreach($fields as $field): ?>
                '<?=$field?>',           
            <?php endforeach; ?>
        ],
        colModel:[
            <?php foreach($fields as $field): ?>
                {name:'<?=$field?>', width: 100, index:'<?=$field?>     '},            
            <?php endforeach; ?>
        ],
        pager: '#pager',
        rowNum:<?=$numRows?>,
        /*rowList:[10, 100, 1000],*/
        /*sortname: '<?=$fields[0]?>',*/
        /*sortorder: 'asc',*/
        viewrecords: true,
        gridview: true,
        caption: '<?=$name?>',
    })
    
    /* $("#list").jqGrid('navGrid','#pager',
        { edit: false, del: false, search: false }, //options
        {}, // edit options
        { reloadAfterSubmit: true, closeAfterAdd: true }, // add options
        {}, // del options
        {}, // search options
        {}  // view options
    ); */
    
    $.getJSON('<?=site_url("/sqlreports/reports_json")?>', function(data) {
        console.log(data);
        for(i = 0; i < data.length; i++) {
            $('#reportsList').append('<option value="'+data[i].slug+'">'+data[i].name+'</option>');
        }
        $('#reportsList').change(function() {
            window.location.href = "<?=site_url("/sqlreports/viewreport")?>/" + $(this).val();
        })
    })
    
    $('#showsql').click(function() {
        if($('#sql').is(':visible')) {
            $('#sql').slideUp();
            $(this).html('Show SQL');
        } else {
            $('#sql').slideDown();
            $(this).html('Hide SQL');
        }
    })
    
    $('input[type=datetime]').datetimepicker({
        dateFormat: 'yy-mm-dd'
    });
    
});

</script>

<style>
    #sql {
        display: none;
    }
</style>

</head>

<body>
    <div class="container">
        <div class="header">
            <select id="reportsList">
                <option value="">Select Report...</option>
            </select>
        </div>
        <div class="main">
            <h1><?=$name?></h1>
            <div class="report-desc">
                <p><?=$desc?></p>    
            </div>
            <form action="<?php site_url($this->uri->uri_string())?>" >
                <?php sort($varFields); foreach($varFields as $varField): ?>
                <div class="field">
                    <label><?=$varField?></label>
                    <?php if(preg_match('/\d{4}-\d{1,2}-\d{1,2} \d{2}:\d{2}(:\d{2})?/', $varValues["$varField"]) === 1): ?>
                        <input name="<?=$varField?>" value="<?=$varValues["$varField"]?>" type="datetime" />
                    <?php else: ?>
                        <input name="<?=$varField?>" value="<?=$varValues["$varField"]?>" />
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php if(count($varFields) > 0): ?>
                <div class="field submit">
                    <button class="widebutton" type="submit">Update</button>
                </div>
                <?php endif; ?>
            </form>

            <table id="list">
                <tr><td/></tr>
            </table> 
            <!-- <div id="pager"></div> -->
            
            <button id="showsql">Show SQL</button>
            <div id="sql">
                <pre><?=$sql?></pre>
            </div>
        </div>
    </div>
</body>
</html>