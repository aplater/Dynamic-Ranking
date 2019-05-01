<!DOCTYPE html>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">

<?php
$content   = "";
$rows_pp   = 30;

    include("config.php");

    $query = mssql_query("Select * from Character");
	   while ($rows = mssql_fetch_array($query)) {
		   if($rows['IsVip'] == 0){ $vip = "false";} else{$vip = "true";}
           if (mssql_num_rows(mssql_query("Select GameIDC from AccountCharacter where ID in (Select memb___id from MEMB_STAT where ConnectStat=1 and ID='".$rows['AccountID']."') and ID='".$rows['AccountID']."'")) == 1){$status = "Online";}else{$status = "Offline";}		
		   $content .= "
			[
			 '" . str_replace("\\","",$rows['Name']) . "',
			 '" . char_class($rows['Class']) . "',
			 "  . $rows['cLevel'] . ",
			 "  . $rows['Resets'] . ",
			 "  . $rows['GrandResets'] . ",
			 "  . $rows['Strength'] . ",
			 "  . $rows['Dexterity'] . ",
			 "  . $rows['Vitality'] . ",  
			 "  . $rows['Energy'] . ",
			 '" . de_map($rows['MapNumber']) . "',
			 "  . $rows['Money'] . ",
			 "  . $vip . ",
			 '" . pk_level($rows['PkLevel']) . "',
			 "  . $rows['SkyEventWins'] . ",  
			 '" . $status . "'	
			],";		
           }
?>


<script>
    google.charts.load('current', {'packages':['table']});
    google.charts.setOnLoadCallback(drawTable);
    function drawTable() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Class');
        data.addColumn('number', 'Level');
        data.addColumn('number', 'Resets');
        data.addColumn('number', 'GR Resets');
        data.addColumn('number', 'Strength');
        data.addColumn('number', 'Dexterity');
        data.addColumn('number', 'Vitality');
        data.addColumn('number', 'Energy');
        data.addColumn('string', 'Location');
        data.addColumn('number', 'Zen');
        data.addColumn('boolean', 'VIP');
        data.addColumn('string', 'PK Level');
        data.addColumn('number', 'Sky Wins');
        data.addColumn('string', 'Status');
        data.addRows([ <?php  echo $content;  ?> ]);
        var dashboard = new google.visualization.Dashboard(document.querySelector('#dashboard'));

        var charclass = new google.visualization.ControlWrapper({
            'controlType': 'CategoryFilter',
            'containerId': 'charclass',
            'options': {
                'filterColumnLabel': 'Class',
                'ui': {
					'label' : '',
					'caption' : 'By Class',
                    'labelStacking': 'horizontal',
                    'allowTyping': false,
                    'allowMultiple': true
                }
            }
        });
		var zen_range_picker = new google.visualization.ControlWrapper({
            'controlType': 'NumberRangeFilter',
            'containerId': 'zen_range_picker',
            'options': {
                'filterColumnLabel': 'Zen',
				'minValue': 0,
				'maxValue': 2000000000,
				'ui':{
					'cssClass': 'zen_range_picker'
				}
            }
        });
		var levels = new google.visualization.ControlWrapper({
            'controlType': 'NumberRangeFilter',
            'containerId': 'levels',
            'options': {
                'filterColumnLabel': 'Level',
				'minValue': 0,
				'maxValue': 400
            }
        });
        var status = new google.visualization.ControlWrapper({
            'controlType': 'CategoryFilter',
            'containerId': 'status',
            'options': {
                'filterColumnLabel': 'Status',
                'ui': {
					'label' : '',
					'caption' : 'By Status',
                    'labelStacking': 'horizontal',
                    'allowTyping': false,
                    'allowMultiple': false,
                }
            }
        });
        var location = new google.visualization.ControlWrapper({
            'controlType': 'CategoryFilter',
            'containerId': 'location',
            'options': {
                'filterColumnLabel': 'Location',
                'ui': {
					'label' : '',
					'caption' : 'By Location',
                    'labelStacking': 'horizontal',
                    'allowTyping': false,
                    'allowMultiple': true,
                }
            }
        });
        var character = new google.visualization.ControlWrapper({
            controlType: 'StringFilter',
            containerId: 'character',
            options: {
                allowHtml: true,
                filterColumnLabel: 'Name',
				'ui': {
					'label' : 'By Character',
					'cssClass': 'character'
				}
            }
        });

        var table = new google.visualization.ChartWrapper({
            chartType: 'Table',
            containerId: 'table',
            options: {
                showRowNumber: true,
                allowHtml: true,
                page: 'enable',
                pageSize: <?php echo $rows_pp; ?>,
                width:'100%',
                pagingSymbols: {
                    prev: 'prev',
                    next: 'next'
                },
                pagingButtonsConfiguration: 'auto'
            }
        });

        dashboard.bind([charclass,status ,location,character,zen_range_picker,levels], [table]);
        dashboard.draw(data,{'allowHtml':true});
    }
    google.load('visualization', '1.1', {packages:['controls'], callback: drawTable});

</script>

<div id="show_selected"></div>
<div id="dashboard">
	    <fieldset>
          <legend>Filters:</legend>
		    <div class="control">
			    <div id="character"></div>
				<div id="status"></div>
			    <div id="levels"></div>
			    <div id="zen_range_picker"></div>			
			</div>
			<div id="charclass"></div>
			<div id="location"></div>
		</fieldset>
        <div id="table"></div>
</div>


