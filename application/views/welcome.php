<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SQL Reports</title>
</head>

<body>
    <h1>Welcome to SQL Reports</h1>
    <p>Select a report:</p>
    <ol>
        <?php foreach ($query->result_array() as $row): ?>
        <li><a href="<?=site_url('/sqlreports/viewreport/'.$row['slug'])?>"><?php echo $row['name']; ?></a></li>
        <?php endforeach; ?>
    </ol>
</body>
</html>