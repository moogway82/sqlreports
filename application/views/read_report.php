<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
 
<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>css/ui-lightness/jquery-ui-1.8.19.custom.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>css/ui.jqgrid.css" />
 
<style type="text/css">
html, body {
    margin: 0;
    padding: 0;
}
</style>

<script src="<?=base_url()?>js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="<?=base_url()?>js/jquery.jqGrid.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(function(){ 
    $("#list").jqGrid({
        url:'<?=site_url('/sqlreports/tabledata').'/'.$this->uri->segment(3)?>',
        shrinkToFit: true,
        /* width: 300, */
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
                {name:'<?=$field?>', width: 100, index:'id'},            
            <?php endforeach; ?>
        ],
        pager: '#pager',
        rowNum:10,
        rowList:[10, 100, 1000],
        sortname: 'id',
        sortorder: 'asc',
        viewrecords: true,
        gridview: true,
        caption: 'Somfing',
    })
    
    $("#list").jqGrid('navGrid','#pager',
        { edit: false, del: false, search: false }, //options
        {}, // edit options
        { reloadAfterSubmit: true, closeAfterAdd: true }, // add options
        {}, // del options
        {}, // search options
        {}  // view options
    );
});

</script>

</head>

<body>
    <h1><?=$name?></h1>
    <div class="report-desc">
        <p><?=$desc?></p>
        <pre><?=$sql?></pre>
    </div>
    <table id="list">
        <tr><td/></tr>
    </table> 
    <div id="pager"></div>
</body>
</html>