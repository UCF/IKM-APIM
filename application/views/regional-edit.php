<?php
	$this->load->view("header");
?>
<style>
	.jqx-widget-content { font-size: 11px; }
	
	.jqx-tooltip-text { text-align: left; } 

	.green:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .green:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
            color: black;
            background-color: #b6ff00;
        }
        .yellow:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .yellow:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
            color: black;
            background-color: #FFE566;
        }
        .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
            color: black;
            background-color: #FF7979;
        }
		
	#csvExport {
		cursor: pointer;
	}
	#xlsExport {
		cursor: pointer;
	}
	#clearfilterbutton {
		cursor: pointer;
	}
	.jqx-fill-state-hover { background: #FAFAD2; } /*highlighter*/
</style>

<script type="text/javascript"> 
	
	function regedit(plan,subplan){
		var window = $("<div><div>Regional Campus Association</div><div>Regional Campus Association</div></div>");
        window.appendTo(document.body);
        //window.jqxWindow('setContent', 'Loading...');
		window.jqxWindow({
			isModal: true,
			width: 300,
			height: 300
		});
		
		$.ajax({
            datatype: "html",
		    url: "../main/regional_association",
			data: "plan="+plan+"&subplan="+subplan,
            success: function (data) {
                window.jqxWindow('setContent', data);
            },
            error: function () {
                window.jqxWindow('setContent', 'Error');
            }
        });
		
		window.on('close', function (closeEvent){ $("#mainData").jqxGrid('updatebounddata','cells'); });
		
		
	}
	$(document).ready(function () {
		
		var url = "../regional/data_main";
		var grad_hide = false;

		//setting the types for the column names
		var source =
		{
		    datatype: "json",
		    datafields: [
				{ name: 'College',type: 'string'},{ name: 'CIP', type:'string'},
				{ name: 'HEGIS',type: 'string'},{ name: 'Plan',type: 'string'},
				{ name: 'Subplan',type: 'string'},{ name: 'Plan Name',type: 'string'},
				{ name: 'Plan Type',type: 'string'},{ name: 'Degree',type: 'string'},
				{ name: 'Dept.',type: 'string'},{ name: 'Status',type: 'string'},
				{ name: 'Access',type: 'string'},{ name: 'PSM', type: 'boolean'},
				{ name: 'Online', type: 'boolean'},
				{ name: 'STEM', type: 'boolean'},{ name: 'info', type:'string' },
				{ name: 'Stratemph', type:'string' },{ name: 'TermStart', type: 'string' },
				{ name: 'Career', type: 'string' },{ name: 'check', type: 'number' },
				{ name: 'StatusChange', type: 'string' },
				{ name: 'ALTSPRNG', type: 'boolean'},
				{ name: 'COCOA', type: 'boolean'},
				{ name: 'DAYTONA', type: 'boolean'},
				{ name: 'LEESBURG', type: 'boolean'},
				{ name: 'OCALA', type: 'boolean'},
				{ name: 'PALMBAY', type: 'boolean'},
				{ name: 'LAKEMARY', type: 'boolean'},
				{ name: 'SOUTHLAKE', type: 'boolean'},
				{ name: 'OSCEOLA', type: 'boolean'},
				{ name: 'METROWEST', type: 'boolean'},
				{ name: 'VALENCIA', type: 'boolean'}
				
		    ],
		    
		    id: 'id',
		    url: url,
		    cache: false,
	        pagenum: 0,
		    updaterow: function (rowid, rowdata, commit) {
			
			var updata = "update=true&plan=" + rowdata.Plan + "&subplan=" + rowdata.Subplan;
			updata = updata + "&ALTSPRNG=" + rowdata.ALTSPRNG + "&COCOA=" + rowdata.COCOA + "&DAYTONA=" + rowdata.DAYTONA +"&LEESBURG=" + rowdata.LEESBURG;
			updata = updata + "&OCALA=" + rowdata.OCALA + "&PALMBAY=" + rowdata.PALMBAY + "&LAKEMARY=" + rowdata.LAKEMARY +"&SOUTHLAKE=" + rowdata.SOUTHLAKE;
			updata = updata + "&OSCEOLA=" + rowdata.OSCEOLA + "&METROWEST=" + rowdata.METROWEST + "&VALENCIA=" + rowdata.VALENCIA;
			
			
			$.ajax({
		                dataType: 'json',
		                url: '../regional/update_regional',
		                data: updata,
						cache: false,
		                success: function (updata, status, xhr) {
		                    // update command is executed.
						commit(true);
		                    
		                    //Label1.textContent = "ROW ID: " + rowid + " STATUS: Saved -> TRUE";
		                },
		                error: function (jqXHR, textStatus, errorThrown) {
		                    // cancel changes.
		                    alert(errorThrown);
						commit(false);
		                    
				    //Label1.textContent = "ROW ID: " + rowid + " STATUS: Saved -> FALSE";
		                }
		            });
		    },
		    data: {     
			//college: $('#pcoll').val(),
			//career: $('#pcareer').val()
		    }
		};
		
		var cellbeginedit = function (row, datafield, columntype, value) {
			var data = $('#mainData').jqxGrid('getrowdata', row);
			
			return false;
			/*if(data.avail == false || data.check == 4){ //check for college or grad -- all and admin can edit everyone
				return false;
			}*/
		
		};
		var regbeginedit = function (row, datafield, columntype, value,defaultHtml) {
			var data = $('#mainData').jqxGrid('getrowdata', row);
			
			return true;
		};
		var info = function(column){
		 	//$(element).jqxTooltip({ position: 'mouse', content: column });
		}
		var cellsrenderer = function (row, column, value, defaultHtml) {
			
			 var data = $('#mainData').jqxGrid('getrowdata', row);		       
			 if(data.avail == false) {
		            var element = $(defaultHtml);
		            element.css('color', '#999');
		            return element[0].outerHTML;
		        } else {
				if(column == 'recentchange'){
					
					return '<td align=\'center\'><a onClick="details(\'' + escape(data.Plan) + '\',\'' + escape(data.Subplan) + '\')">' + defaultHtml + '</a></td>';
				}
				if(column == 'info'){
					return '<div style="background: white;"><a onClick="details(\'' + escape(data.Plan) + '\',\'' + escape(data.Subplan) + '\')"><img style="margin:4px; margin-left: 6px;" src="../../img/information.png"><a></div>';;	
				}		
			}

		        return defaultHtml;
		}
		
		
		var regeditrender = function (row, column, value, defaultHtml){
			var data = $('#mainData').jqxGrid('getrowdata', row);
			return '<div style="text-align: center;"><a onClick="regedit(\'' + escape(data.Plan) + '\',\'' + escape(data.Subplan) + '\')">edit</a></div>';
			
		}
		var statusback = function (row, columnfield, value) {
		        if (value == 'I') {
		            return 'red';
		        }
		        else if (value == 'S') {
		            return 'red';
		        }
			else return '';
		    }
		var accessback = function (row, columnfield, value) {
		        if (value == 'Restricted') {
		            return 'orange';
		        }
			else if (value == 'Limited') {
		            return 'yellow';
		        }
		        else return '';
		    }
	
		var Adapter = new $.jqx.dataAdapter(source);
		var toolTip = function(element){
				$.ajax({	
				    datatype: "html",
				    url: '../main/tooltip',
				    data: "tip="+$(element).text(),
					cache: false,
				        success: function (data) {
				        	element.jqxTooltip({ 
							position: 'top',
							content: data,
							theme: 'metrodark',
							autoHideDelay: 0,
							width: 200
						})    
				        }
				});
			}
		var addfilter = function () {
		     var filtergroup = new $.jqx.filter();
		     var Careerfiltergroup = new $.jqx.filter();
		     
		     var filter_or_operator = 1;
		     var filtervalue = $('#pcoll').val();
		     var filtervalue2 = $('#pcareer').val();
		     var filtercondition = 'contains';
		     var filtercondition2 = 'contains';
		     var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
		     var filter2 = filtergroup.createfilter('stringfilter', filtervalue2, filtercondition2);
		     
			
		     filtergroup.addfilter(filter_or_operator, filter1);
		     Careerfiltergroup.addfilter(filter_or_operator, filter2);
		     
		     //alert(filtervalue2);
		     // add the filters.
		     $("#mainData").jqxGrid('addfilter', 'College', filtergroup);
		     $("#mainData").jqxGrid('addfilter', 'Career', Careerfiltergroup);
		     // apply the filters.
		     $("#mainData").jqxGrid('applyfilters');
			
		 }
		 
		$("#mainData").jqxGrid({
		    source: Adapter,
		    theme: 'energyblue',
		    pageable: true,
		    autoheight: true,
		    width: '100%',
		    columnsresize: true,
	        sortable: true,
		    editable: true,
            filterable: true,	
		    autoshowfiltericon: false,
		    enabletooltips: true,
		    enablehover: true,
		    selectionmode: 'singlerow',
		    columns: [
			//{ text: '', datafield: 'info',  width: 30, editable: false, cellsalign: 'center',filterable: false, cellbeginedit: cellbeginedit,  //cellsrenderer: cellsrenderer, pinned: true},
			{ text: 'College',   align: 'center', datafield: 'College', width: 60, editable: false,filterable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, pinned: true, rendered: toolTip},
			{ text: 'CIP', align: 'center', datafield: 'CIP', width: 62, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, pinned: true, rendered: toolTip},			
			{ text: 'Term Start', align: 'center', datafield: 'TermStart', width: 99, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer,pinned:true, rendered: toolTip},						
			{ text: 'Plan Name', align: 'center', datafield: 'Plan Name', width: 195, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer,pinned:true, rendered: toolTip},			
			{ text: 'Strat. Emphasis', columngroup: 'General', datafield: 'Stratemph', width: 97,filtertype: 'input',filterable: true, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip },
			
			{ text: 'Regional', columngroup: 'General', datafield: 'region', align: 'center', width: 93, filterable: true,rendered: toolTip},
			<!--{ text: 'Reg. Edit', columngroup: 'General', datafield: 'RegEdit',  width: 93, filterable: true,rendered: toolTip, hidden: true, editable:false,cellsrenderer: regeditrender},-->
				
			{ text: 'Plan', columngroup: 'General', datafield: 'Plan', width: 93, editable: false,filtertype: 'input',filterable: true, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip },
			{ text: 'Sub-plan', columngroup: 'General', datafield: 'Subplan', width: 93, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},
			{ text: 'Plan Type', columngroup: 'General', datafield: 'Plan Type', width: 75, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},
			{ text: 'Degree', columngroup: 'General', datafield: 'Degree', renderer: info,  width: 70, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},
			{ text: 'Career', columngroup: 'General', datafield: 'Career', renderer: info,  width: 70, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},			
			{ text: 'Status', columngroup: 'General', datafield: 'Status', width: 56, cellsalign: 'center', editable: false, cellclassname: statusback, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},
			{ text: 'Susp./Inact. Date', align: 'center', columngroup: 'General', datafield: 'StatusChange', width: 99, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},		
			{ text: 'Access', columngroup: 'General', datafield: 'Access', width: 80, editable: false, cellclassname: accessback, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},
			{ text: 'Online', columngroup: 'General', datafield: 'Online', columntype: 'checkbox', width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			/*{ text: 'PSM', columngroup: 'General', datafield: 'PSM', columntype: 'checkbox', width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'STEM', columngroup: 'General', datafield: 'STEM', columntype: 'checkbox', width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},*/
			
			{ text: 'Altamonte Springs', columngroup: 'RegionalCampus', datafield: 'ALTSPRNG', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: regbeginedit, rendered: toolTip, rendered: toolTip},
			{ text: 'Cocoa', columngroup: 'RegionalCampus', datafield: 'COCOA', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: regbeginedit, rendered: toolTip, rendered: toolTip},
			{ text: 'Daytona', columngroup: 'RegionalCampus', datafield: 'DAYTONA', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: regbeginedit, rendered: toolTip, rendered: toolTip},
			{ text: 'Leesburg', columngroup: 'RegionalCampus', datafield: 'LEESBURG', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: regbeginedit, rendered: toolTip, rendered: toolTip},
			{ text: 'Ocala', columngroup: 'RegionalCampus', datafield: 'OCALA', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: regbeginedit, rendered: toolTip, rendered: toolTip},
			{ text: 'Palm Bay', columngroup: 'RegionalCampus', datafield: 'PALMBAY', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: regbeginedit, rendered: toolTip, rendered: toolTip},
			{ text: 'Sanford/LM', columngroup: 'RegionalCampus', datafield: 'LAKEMARY', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: regbeginedit, rendered: toolTip, rendered: toolTip},
			{ text: 'South Lake', columngroup: 'RegionalCampus', datafield: 'SOUTHLAKE', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: regbeginedit, rendered: toolTip, rendered: toolTip},
			{ text: 'Valencia East', columngroup: 'RegionalCampus', datafield: 'VALENCIA', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: regbeginedit, rendered: toolTip, rendered: toolTip},
			{ text: 'Valencia Osceola', columngroup: 'RegionalCampus', datafield: 'OSCEOLA', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: regbeginedit, rendered: toolTip, rendered: toolTip},
			{ text: 'Valencia West', columngroup: 'RegionalCampus', datafield: 'METROWEST', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: regbeginedit, rendered: toolTip, rendered: toolTip},
			//{ text: 'Last Change Date', datafield: 'recentchange', cellsformat: 'MM.dd.yyyy HH:mm:ss tt', width: 125,filterable: false, //cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer},
			{ text: 'avail', datafield: 'avail', hidden: true, cellbeginedit: cellbeginedit}		
		    ],
		    columngroups: [
					{ text: '', align: 'center', name: 'General' },
                  	{ text: 'Regional Locations', align: 'center', name: 'RegionalCampus' }
		    ],
		    ready: function () {			
				addfilter();

				var rowscount = $("#mainData").jqxGrid('getdatainformation').rowscount;                   
				$('#mainData').jqxGrid({ pagesizeoptions: ['15', '30', '50', '100']});
				
				//unhide the regional edit column for regional users		
				/*if ($.inArray("Regional",groups) > -1) { 
		 			$("#mainData").jqxGrid('showcolumn','RegEdit');
				}*/ 
				//(".jqx-grid-column-header").each(function () {				
						//toolTip($(this),$(this).text());
				//});
			}
						
		}); 
		
		
		
		$("#mainData").on('cellbeginedit', function (event) {
		        var args = event.args;
		        $("#cellbegineditevent").text("Event Type: cellbeginedit, Column: " + args.datafield + ", Row: " + (1 + args.rowindex) + ", Value: " + args.value);
		    });

		$("#mainData").on('cellendedit', function (event) {
		        var args = event.args;
		        $("#cellendeditevent").text("Event Type: cellendedit, Column: " + args.datafield + ", Row: " + (1 + args.rowindex) + ", Value: " + args.value);
		});

		$("#csvExport").click(function () {
		        exportinfo = $("#mainData").jqxGrid('exportdata', 'csv');			
			download(exportinfo, 'Exported Data', 'text/plain');
			
		    });
		$("#xlsExport").click(function () {
		        exportinfo = $("#mainData").jqxGrid('exportdata', 'xls');			
			download(exportinfo, 'Exported Data', 'application/excel');
			
		    });
		$( "#pcoll" ).change(function() {	
			
			$("#mainData").jqxGrid('removefilter', 'College');
			
			var filtergroup = new $.jqx.filter();
			var filter_or_operator = 1;
			var filtervalue = $('#pcoll').val();
			var filtercondition = 'contains';
			var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
			
			filtergroup.addfilter(filter_or_operator, filter1);
			
			// add the filters.
			$("#mainData").jqxGrid('addfilter', 'College', filtergroup);
			
			// apply the filters.
			$("#mainData").jqxGrid('applyfilters');			
			
			//$('#mainData').jqxGrid('updatebounddata','cells');		
		});
		$( "#pcareer" ).change(function() {	
			
			$("#mainData").jqxGrid('removefilter', 'Career');
			
			var filtergroup = new $.jqx.filter();
			var filter_or_operator = 1;
			var filtervalue = $('#pcareer').val();
			var filtercondition = 'contains';
			var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
			
			filtergroup.addfilter(filter_or_operator, filter1);
			
			// add the filters.
			$("#mainData").jqxGrid('addfilter', 'Career', filtergroup);
			
			// apply the filters.
			$("#mainData").jqxGrid('applyfilters');			
			
			/*if ($('#pcareer').val() == 'GRAD'){ grad_set = true; } else { grad_set = false; }
			var dataAdapter = new $.jqx.dataAdapter(source,
			    {
				formatData: function (data) {
				    $.extend(data, {
					college: $('#pcoll').val(),
					career: $('#pcareer').val()
				    });

				    return data;
				}
			    }
			);
			
			$("#mainData").jqxGrid({ source: dataAdapter });*/			
		});
		/*$('#filtericons').jqxCheckBox({ checked: false, height: 25 });
		$('#filtericons').on('change', function (event) {
		        $("#mainData").jqxGrid({ autoshowfiltericon: !event.args.checked });
		    });*/
		$('#clearfilterbutton').click(function () {
		        $("#mainData").jqxGrid('clearfilters');
		    });
		
			
	});

</script>	

	<div id="page_title">Program.......</div>
	
	<div id="page_summary">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
	Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
	</div>
	<fieldset>   
	<legend>Optional Selections</legend>
	<ol>
		<li>
			<label>College:</label>
			<select id="pcoll" name="coll" class="styled-select">
			<option value="" >All Colleges:</option>
			<?php
				$s = '';
				foreach ($colleges->result() as $college_key => $college_row){
					if ($college_row->College == $default){ $s = 'selected'; }					
						echo "\t<option value=\"$college_row->College\" $s>$college_row->College_Name </option>\n";
				$s = '';
				}
				
			?>
			</select>
		</li>
		
		<li>
			<label>Career:</label>
			<select id="pcareer" name="career" class="styled-select">
				<option value="" <?php echo $s; ?>>All</option>
				<option value="UGRD" >Undergraduate</option>
				<option value="GRAD" >Graduate</option>
				
			</select> 
		</li>
	</fieldset>
		

	<div >
		Export table: 
		<!-- <a title="Excel" id="xlsExport"><img align="top" src="../img/page_excel.png"></a> -->
		<a title="CSV" id="csvExport"><img align="top" src="../img/page_white_text.png"></a>
		|
		<a title="Remove Filters" id="clearfilterbutton">Remove Filters</a>
		
	</div>
<br />
        <!--<div id='jqxtabs' >
            <ul style="margin-left: 30px;" >
                <li>Initial Grid 1</li>
                <li>Initial Grid 2</li>
               
            </ul>
            <div> 
				<div id="mainData"></div>
				<div id="mainGridPager"></div>
			</div>
			
            <div>
               help
            </div>
            
        </div>-->


<div id="mainData"></div>
<div id="mainGridPager"></div>
<?php

	$this->load->view("footer");
?>
