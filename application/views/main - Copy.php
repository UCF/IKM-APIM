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
	/*function details(plan,subplan) {
		$('#window').jqxWindow('setContent', 'Loading...');
		$.ajax({
		    datatype: "html",
		    url: "../main/extra_data",
		    data: "plan="+plan+"&subplan="+subplan,
		    success: function (data) {
		        $('#window').jqxWindow('setContent', data);
		    },
		    error: function () {
		        $('#window').jqxWindow('setContent', 'Error');
		    }
		});
		$('#window').jqxWindow('show');
	    }*/
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
		//get the tooltips for later
	    var tips = <?php echo json_encode($tips, JSON_PRETTY_PRINT); ?>;

		//get the groups for later
		var groups = <?php echo json_encode($user_groups); ?>;
				
		//variable to hold cell name value before edit; need that for checking if a change occured
		var prev_cellData = '';
		var prev_cellName = '';
		var curr_cellName = '';
		var curr_cellData = '';
		
		var url = "../main/data_main"; //location for the first data pull
		var grad_hide = false; //set the default hide - will be reset if necessary by role

		//setting the types for the column names
		var source =
		{
		    datatype: "json",
		    datafields: [
				{ name: 'College',type: 'string'},{ name: 'CIP', type:'string'},
				{ name: 'HEGIS',type: 'string'},{ name: 'Plan',type: 'string'},
				{ name: 'Subplan',type: 'string'},{ name: 'PlanName',type: 'string'},
				{ name: 'Plan Type',type: 'string'},{ name: 'Degree',type: 'string'},
				{ name: 'Dept.',type: 'string'},{ name: 'Status',type: 'string'},
				{ name: 'Access',type: 'string'},{ name: 'Admission', type: 'boolean'},
				{ name: 'ReAdmit', type: 'boolean'},{ name: 'FLVC', type: 'boolean'},
				{ name: 'PSM', type: 'boolean'},{ name: 'STEM', type: 'boolean'},
				{ name: 'Professional', type: 'boolean'},{ name: 'MTR', type: 'boolean'},
				{ name: 'recentchange', type: 'date'},{ name: 'Regional',type: 'string'},
				{ name: 'Online', type: 'boolean' },{ name:'avail', type: 'boolean' },
				{ name: 'TotThesis', type: 'string' },{ name:'TotNonThesis', type: 'string' },
				{ name: 'TotCert', type: 'string' },{ name:'TotDoc', type: 'string' },
				{ name: 'TotDissert', type: 'string' },{ name:'Tot6971', type: 'string' },
				{ name: 'info', type:'string' }, { name: 'Stratemph', type:'string' },
				{ name: 'TermStart', type: 'string' },{ name: 'Career', type: 'string' },{ name: 'Level', type: 'string' },
				{ name: 'check', type: 'number' }, { name:'StatusChange', type: 'string' },
				{ name: 'PlanLongName', type: 'string' },{ name: 'SubPlanLongName', type: 'string' },{ name: 'DeptLongName', type: 'string' },
				{ name: 'CR', type: 'boolean'},
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

			//set this to false which wi
			var updateAll = false;

			//possible future optimization
			if(prev_cellName == 'PlanLongName' || prev_cellName == 'DeptLongName'){
					if(prev_cellData != rowdata.PlanLongName || prev_cellData != rowdata.DeptLongName){
							updateAll = true;
						}
				}
			if(prev_cellName == 'DeptLongName'){
				if(prev_cellData != rowdata.DeptLongName){
						updateAll = true;
					}
			}

			//stuff for updating rows 151-185
			var updata = "update=true&cellchange=" + prev_cellName + "&plan=" + rowdata.Plan + "&adm=" + rowdata.Admission + "&subplan=" + rowdata.Subplan + "&planlongname=" + rowdata.PlanLongName + "&subplanlongname=" + rowdata.SubPlanLongName;
			updata = updata + "&flvc=" + rowdata.FLVC + "&readmit=" + rowdata.ReAdmit + "&online=" + rowdata.Online +"&orient=" + rowdata.Orientation + "&deptlongname=" + rowdata.DeptLongName;
			updata = updata + "&psm=" + rowdata.PSM + "&mtr=" + rowdata.MTR + "&cr=" + rowdata.CR + "&stem=" + rowdata.STEM +"&professional=" + rowdata.Professional + "&totThesis=" + rowdata.TotThesis + "&totNonThesis=" + rowdata.TotNonThesis;
			updata = updata + "&tot6971=" + rowdata.Tot6971 + "&totCert=" + rowdata.TotCert + "&totDoc=" + rowdata.TotDoc +"&totDissert=" + rowdata.TotDissert;
			
			$.ajax({
		                dataType: 'json',
		                url: '../main/update_main',
		                data: updata,
						cache: false,
		                success: function (updata, status, xhr) {
		                					
		                    // update command is executed.
							commit(true);
							
							//refresh the table if the plan name has changed
							if(updateAll){ 
									$("#mainData").jqxGrid('updatebounddata','data');
								}

							var now = new Date();
							var time = dateFormat(now, "m/d/yyyy h:MM:ss TT");
		                    
		                    $('#session_log').append("<p>" + time + " - " + rowdata.PlanName+ " - " + prev_cellName + " -> " + curr_cellData+ "</p>");
		                    $('#session_log').animate({scrollTop: $('#session_log').prop("scrollHeight")}, 500);
		                    
		                },
		                error: function (jqXHR, textStatus, errorThrown) {
		                    // cancel changes on frontend
		                    alert(errorThrown);
							commit(false);
		                }
		            });
		    }
		};

		//checks if the cell can be changed based on role
		var cellbeginedit = function (row, datafield, columntype, value) {
			
			var data = $('#mainData').jqxGrid('getrowdata', row);

						
			if(data.avail == false || data.check == 4){ //check for Regional, read-only, or orientation - they only get to see data
				return false;
			}

			//stop undergrad from editing anything grad
			if(data.Career == 'GRAD' && data.check == 0){
				return false;				
			}

			//stop grad from editing anything undergrad
			if(data.Career == 'UGRD' && data.check == 2){
				return false;
			}

			//stop grad from editing certain fields
			var gradRestricted = ["FLVC","Online","Admission","ReAdmit","Mrkt. Rate Tuition","Cost Recovery"];

			//stop ugrad college folks from editing certain fields
			var ugradRestricted = ["FLVC","Online","Mrkt. Rate Tuition","Cost Recovery"];
			
			if(data.check == 2 && gradRestricted.indexOf(datafield) != -1){
					return false;
				}
			if(data.check == 0 && ugradRestricted.indexOf(datafield) != -1){
					return false;
				}

			//for online
			if(data.check == 5 && datafield == 'Online'){
					return true;
				}
			if(data.check == 5 && datafield != 'Online'){
					return false;
				}

			//for flvc
			if(data.check == 6 && datafield == 'FLVC'){
					return true;
				}
			if(data.check == 6 && datafield != 'FLVC'){
					return false;
				}

			//for registrar
			if(data.check == 7 && datafield == 'ReAdmit'){
					return true;
				}
			if(data.check == 7 && datafield != 'ReAdmit'){
					return false;
				}
		};
		var gradbeginedit = function (row, datafield, columntype, value,defaultHtml) {
			var data = $('#mainData').jqxGrid('getrowdata', row);
			
			if(data.check == 4){
				return false;				
			} 
			//no undergrad records can have the grad parts edited
			if(data.Career == 'UGRD'){
				return false;				
			} 			
			if(data.Career == 'GRAD' && data.check == 0){
				return false;				
			}
		

			if(data.check == 5 || data.check == 6){
				return false;
			}
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
					var ele = $(element).text();
			        element.jqxTooltip({ 
						position: 'top',
						content: tips[ele],
						theme: 'metrodark',
						autoHideDelay: 0,
						width: 200
					})   			
			
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
		     
			 //filter for grad vs ungrad first entry
			 if (filtervalue2 == 'UGRD'){
									
				$("#mainData").jqxGrid('hidecolumn','Professional');
				$("#mainData").jqxGrid('hidecolumn','PSM');
				$("#mainData").jqxGrid('hidecolumn','MTR');
				$("#mainData").jqxGrid('hidecolumn','CR');
				$("#mainData").jqxGrid('hidecolumn','TotThesis');
				$("#mainData").jqxGrid('hidecolumn','Tot6971');
				$("#mainData").jqxGrid('hidecolumn','TotNonThesis');
				$("#mainData").jqxGrid('hidecolumn','TotCert');
				$("#mainData").jqxGrid('hidecolumn','TotDoc');
				$("#mainData").jqxGrid('hidecolumn','TotDissert');
				$("#mainData").jqxGrid('hidecolumn','PlanLongName');
				$("#mainData").jqxGrid('hidecolumn','SubPlanLongName');
				$("#mainData").jqxGrid('hidecolumn','DeptLongName');
			}
			if (filtervalue2 == 'GRAD' || filtervalue == 'PROF') {
				//for removing ugrad columms if set
				
				$("#mainData").jqxGrid('hidecolumn','Admission');
				$("#mainData").jqxGrid('hidecolumn','ReAdmit');
				$("#mainData").jqxGrid('hidecolumn','Access');
				$("#mainData").jqxGrid('hidecolumn','FLVC');
				
				$("#mainData").jqxGrid('showcolumn','Professional');
				$("#mainData").jqxGrid('showcolumn','PSM');
				$("#mainData").jqxGrid('showcolumn','MTR');
				$("#mainData").jqxGrid('showcolumn','CR');
				$("#mainData").jqxGrid('showcolumn','TotThesis');
				$("#mainData").jqxGrid('showcolumn','Tot6971');
				$("#mainData").jqxGrid('showcolumn','TotNonThesis');
				$("#mainData").jqxGrid('showcolumn','TotCert');
				$("#mainData").jqxGrid('showcolumn','TotDoc');
				$("#mainData").jqxGrid('showcolumn','TotDissert');
				$("#mainData").jqxGrid('showcolumn','PlanLongName');
				$("#mainData").jqxGrid('showcolumn','SubPlanLongName');
				$("#mainData").jqxGrid('showcolumn','DeptLongName');	
			}

			//specific for orientation - HEGIS and regions
			//no grad will be set because they will have their career hard set on entry
			if ($.inArray("Orientation",groups) != -1) { 
					$("#mainData").jqxGrid('showcolumn','HEGIS');

					$("#mainData").jqxGrid('showcolumn','ALTSPRNG');
					$("#mainData").jqxGrid('showcolumn','COCOA');
					$("#mainData").jqxGrid('showcolumn','DAYTONA');
					$("#mainData").jqxGrid('showcolumn','LEESBURG');
					$("#mainData").jqxGrid('showcolumn','OCALA');
					$("#mainData").jqxGrid('showcolumn','PALMBAY');
					$("#mainData").jqxGrid('showcolumn','LAKEMARY');
					$("#mainData").jqxGrid('showcolumn','SOUTHLAKE');
					$("#mainData").jqxGrid('showcolumn','OSCEOLA');
					$("#mainData").jqxGrid('showcolumn','METROWEST');
					$("#mainData").jqxGrid('showcolumn','VALENCIA');		

				} 
			
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
			{ text: 'Dept.', align: 'center', datafield: 'Dept.', width: 95, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, pinned: true, rendered: toolTip},			
			
			{ text: 'CIP', align: 'center', datafield: 'CIP', width: 62, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, pinned: true, rendered: toolTip},			
			{ text: 'HEGIS', align: 'center', datafield: 'HEGIS', width: 68, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, pinned: true, rendered: toolTip, hidden: true},			
			{ text: 'Term Start', align: 'center', datafield: 'TermStart', width: 99, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer,pinned:true, rendered: toolTip},						
			{ text: 'Plan Name', align: 'center', datafield: 'PlanName', width: 195, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer,pinned:true, rendered: toolTip},			
			{ text: 'Strat. Emphasis', columngroup: 'General', datafield: 'Stratemph', width: 97,filtertype: 'input',filterable: true, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip },
			
			{ text: 'Regional?', columngroup: 'General', datafield: 'Regional', align: 'center', width: 98, filterable: true,rendered: toolTip, editable: false},
			<!--{ text: 'Reg. Edit', columngroup: 'General', datafield: 'RegEdit',  width: 93, filterable: true,rendered: toolTip, hidden: true, editable:false,cellsrenderer: regeditrender},-->
				
			{ text: 'Plan', columngroup: 'General', datafield: 'Plan', width: 93, editable: false,filtertype: 'input',filterable: true, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip },
			{ text: 'Sub-plan', columngroup: 'General', datafield: 'Subplan', width: 93, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},
			{ text: 'Plan Type', columngroup: 'General', datafield: 'Plan Type', width: 75, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},
			{ text: 'Degree', columngroup: 'General', datafield: 'Degree', renderer: info,  width: 70, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},
			{ text: 'Career', columngroup: 'General', datafield: 'Career', renderer: info,  width: 70, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},
			{ text: 'Level', columngroup: 'General', datafield: 'Level', renderer: info,  width: 70, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},			
			{ text: 'Status', columngroup: 'General', datafield: 'Status', width: 56, cellsalign: 'center', editable: false, cellclassname: statusback, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},
			{ text: 'Susp./Inact. Date', align: 'center', columngroup: 'General', datafield: 'StatusChange', width: 99, editable: false, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},		
			{ text: 'Access', columngroup: 'General', datafield: 'Access', width: 80, editable: false, cellclassname: accessback, cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer, rendered: toolTip},
			{ text: 'Admission', columngroup: 'General', datafield: 'Admission', columntype: 'checkbox', width: 70,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'Re-Admit', columngroup: 'General', datafield: 'ReAdmit', columntype: 'checkbox', width: 67,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'FLVC Transient', columngroup: 'General', datafield: 'FLVC', columntype: 'checkbox', width: 55,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'UCF Online', columngroup: 'General', datafield: 'Online', columntype: 'checkbox', width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'STEM', columngroup: 'General', datafield: 'STEM', columntype: 'checkbox', width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			
			//alert(grad_set);
			{ text: 'Mrkt. Rate Tuition', columngroup: 'GraduateStudies', datafield: 'MTR', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},			
			{ text: 'Cost Recovery', columngroup: 'GraduateStudies', datafield: 'CR', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},			
			{ text: 'Plan Name Extra', columngroup: 'GraduateStudies', datafield: 'PlanLongName', columntype: 'input', hidden: grad_hide, width: 140,filterable: true, cellbeginedit: gradbeginedit, rendered: toolTip},
			{ text: 'SubPlan Name Extra', columngroup: 'GraduateStudies', datafield: 'SubPlanLongName', columntype: 'input', hidden: grad_hide, width: 95,filterable: true, cellbeginedit: gradbeginedit, rendered: toolTip},			
			{ text: 'Dept. Name Extra', columngroup: 'GraduateStudies', datafield: 'DeptLongName', columntype: 'input', hidden: grad_hide, width: 95,filterable: true, cellbeginedit: gradbeginedit, rendered: toolTip},			
			{ text: 'Tot. Dissertation Hrs.', columngroup: 'GraduateStudies', datafield: 'TotDissert', cellsalign: 'center', columntype: 'input', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: gradbeginedit, rendered: toolTip},
			{ text: 'Tot. Thesis Hrs.', columngroup: 'GraduateStudies', datafield: 'TotThesis', cellsalign: 'center', columntype: 'input', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: gradbeginedit, rendered: toolTip},
			{ text: 'Tot. Non-Thesis Hrs.', columngroup: 'GraduateStudies', datafield: 'TotNonThesis', cellsalign: 'center', columntype: 'input', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: gradbeginedit, rendered: toolTip},
			{ text: 'Tot. Cert. Hrs.', columngroup: 'GraduateStudies', datafield: 'TotCert', cellsalign: 'center', columntype: 'input', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: gradbeginedit, rendered: toolTip},			
			{ text: 'Thesis 6971 Hrs.', columngroup: 'GraduateStudies', datafield: 'Tot6971', cellsalign: 'center', columntype: 'input', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: gradbeginedit, rendered: toolTip},			
			{ text: 'Tot. Doctoral Hrs.', columngroup: 'GraduateStudies', datafield: 'TotDoc', cellsalign: 'center', columntype: 'input', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: gradbeginedit, rendered: toolTip},
			{ text: 'Professional', columngroup: 'GraduateStudies', datafield: 'Professional', columntype: 'checkbox', hidden: grad_hide, width: 75,filterable: false, cellbeginedit: gradbeginedit, rendered: toolTip, rendered: toolTip},
			{ text: 'PSM', columngroup: 'GraduateStudies', datafield: 'PSM', columntype: 'checkbox', width: 75,filterable: false, cellbeginedit: gradbeginedit, rendered: toolTip},
				
			//REgional fields
			{ text: 'Altamonte Springs', columngroup: 'RegionalCampus', datafield: 'ALTSPRNG', columntype: 'checkbox', hidden: true, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'Cocoa', columngroup: 'RegionalCampus', datafield: 'COCOA', columntype: 'checkbox', hidden: true, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'Daytona', columngroup: 'RegionalCampus', datafield: 'DAYTONA', columntype: 'checkbox', hidden:true, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'Leesburg', columngroup: 'RegionalCampus', datafield: 'LEESBURG', columntype: 'checkbox', hidden: true, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'Ocala', columngroup: 'RegionalCampus', datafield: 'OCALA', columntype: 'checkbox', hidden: true, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'Palm Bay', columngroup: 'RegionalCampus', datafield: 'PALMBAY', columntype: 'checkbox', hidden: true, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'Sanford/LM', columngroup: 'RegionalCampus', datafield: 'LAKEMARY', columntype: 'checkbox', hidden: true, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'South Lake', columngroup: 'RegionalCampus', datafield: 'SOUTHLAKE', columntype: 'checkbox', hidden: true, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'Valencia East', columngroup: 'RegionalCampus', datafield: 'VALENCIA', columntype: 'checkbox', hidden: true, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'Valencia Osceola', columngroup: 'RegionalCampus', datafield: 'OSCEOLA', columntype: 'checkbox', hidden: true, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
			{ text: 'Valencia West', columngroup: 'RegionalCampus', datafield: 'METROWEST', columntype: 'checkbox', hidden: true, width: 75,filterable: false, cellbeginedit: cellbeginedit, rendered: toolTip},
				
			//{ text: 'Last Change Date', datafield: 'recentchange', cellsformat: 'MM.dd.yyyy HH:mm:ss tt', width: 125,filterable: false, //cellbeginedit: cellbeginedit,  cellsrenderer: cellsrenderer},
			{ text: 'avail', datafield: 'avail', hidden: true, cellbeginedit: cellbeginedit}		
		    ],
		    columngroups: [
					{ text: '', align: 'center', name: 'General' },
                  	{ text: 'Graduate Studies', align: 'center', name: 'GraduateStudies' },
                  	{ text: 'Regional Campus', align: 'center', name: 'RegionalCampus' }
		    ],
		    ready: function () {			
				addfilter();

				var rowscount = $("#mainData").jqxGrid('getdatainformation').rowscount;                   
				$('#mainData').jqxGrid({ pagesizeoptions: ['15', '30', '50', '100']});
				
				//unhide the regional edit column for regional users		
				if ($.inArray("Regional",groups) > -1) { 
		 			$("#mainData").jqxGrid('showcolumn','RegEdit');
				} 
			}				
		}); 
	
		$("#mainData").on('cellbeginedit', function (event) {
		        var args = event.args;
		        var column = args.datafield;
		        var value = args.value;

		        prev_cellData =  value;
		        prev_cellName =  column;
		        
		    });

		$("#mainData").on('cellendedit', function (event) {
				var args = event.args;
		        var column = args.datafield;
		        var value = args.value;
	
		        curr_cellData =  value;
		        curr_cellName =  column;
	  					
		        //$("#cellendeditevent").text("Event Type: cellendedit, Column: " + args.datafield + ", Row: " + (1 + args.rowindex) + ", Value: " + args.value);

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
			//filter the table based on college choice
			$("#mainData").jqxGrid('removefilter', 'College'); //clear the filter first

			var filtergroup = new $.jqx.filter();
			var filter_or_operator = 1;
			var filtervalue = $('#pcoll').val(); //get the college form the dropdown to set the new filter
			var filtercondition = 'contains';
			var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
			
			filtergroup.addfilter(filter_or_operator, filter1);
			
			// add the filters.
			$("#mainData").jqxGrid('addfilter', 'College', filtergroup);
			
			// apply the filters.
			$("#mainData").jqxGrid('applyfilters');			
				
		});
		
		$('#ponline-check').change(function(){
			var filtergroup = new $.jqx.filter();
			var filter_or_operator = 1;
			var filtervalue = '';
			var filtercondition = 'contains';
			
			if($('#ponline-check').is(':checked') === true){
				filtercondition = 'starts_with';
				filtervalue = 'Z';

				var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
				
				filtergroup.addfilter(filter_or_operator, filter1);
				
				// add the filters.
				$("#mainData").jqxGrid('addfilter', 'Subplan', filtergroup);
				
				// apply the filters.
				$("#mainData").jqxGrid('applyfilters');		
			} else {
				// add the filters.
				$("#mainData").jqxGrid('removefilter', 'Subplan');
			}	

				
		});
		
		$( "#pcareer" ).change(function() {	
			
			$("#mainData").jqxGrid('removefilter', 'Career');
			
			var filtergroup = new $.jqx.filter();
			var filter_or_operator = 1;
			var filtervalue = $('#pcareer').val();
			var filtercondition = 'contains';
			var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
			
			if (filtervalue == 'UGRD'){
				$("#mainData").jqxGrid('showcolumn','Admission');
				$("#mainData").jqxGrid('showcolumn','ReAdmit');
				$("#mainData").jqxGrid('showcolumn','Access');
				$("#mainData").jqxGrid('showcolumn','FLVC');
				
				$("#mainData").jqxGrid('hidecolumn','Professional');
				$("#mainData").jqxGrid('hidecolumn','PSM');
				$("#mainData").jqxGrid('hidecolumn','MTR');
				$("#mainData").jqxGrid('hidecolumn','CR');
				$("#mainData").jqxGrid('hidecolumn','TotThesis');
				$("#mainData").jqxGrid('hidecolumn','Tot6971');
				$("#mainData").jqxGrid('hidecolumn','TotNonThesis');
				$("#mainData").jqxGrid('hidecolumn','TotCert');
				$("#mainData").jqxGrid('hidecolumn','TotDoc');
				$("#mainData").jqxGrid('hidecolumn','TotDissert');
				$("#mainData").jqxGrid('hidecolumn','PlanLongName');
				$("#mainData").jqxGrid('hidecolumn','SubPlanLongName');
				$("#mainData").jqxGrid('hidecolumn','DeptLongName');
			}

			if (filtervalue == 'GRAD' || filtervalue == 'PROF' ) {
				
				//for removing ugrad columms if set
				$("#mainData").jqxGrid('hidecolumn','Admission');
				$("#mainData").jqxGrid('hidecolumn','ReAdmit');
				$("#mainData").jqxGrid('hidecolumn','Access');
				$("#mainData").jqxGrid('hidecolumn','FLVC');

				$("#mainData").jqxGrid('showcolumn','Professional');
				$("#mainData").jqxGrid('showcolumn','PSM');
				$("#mainData").jqxGrid('showcolumn','MTR');
				$("#mainData").jqxGrid('showcolumn','CR');
				$("#mainData").jqxGrid('showcolumn','TotThesis');
				$("#mainData").jqxGrid('showcolumn','Tot6971');
				$("#mainData").jqxGrid('showcolumn','TotNonThesis');
				$("#mainData").jqxGrid('showcolumn','TotCert');
				$("#mainData").jqxGrid('showcolumn','TotDoc');
				$("#mainData").jqxGrid('showcolumn','TotDissert');
				$("#mainData").jqxGrid('showcolumn','PlanLongName');
				$("#mainData").jqxGrid('showcolumn','SubPlanLongName');
				$("#mainData").jqxGrid('showcolumn','DeptLongName');
				
			}	
					
			if ( filtervalue == '') {
				//alert("all");
				$("#mainData").jqxGrid('showcolumn','Admission');
				$("#mainData").jqxGrid('showcolumn','ReAdmit');
				$("#mainData").jqxGrid('showcolumn','Access');
				$("#mainData").jqxGrid('showcolumn','FLVC');
				
				$("#mainData").jqxGrid('showcolumn','Professional');
				$("#mainData").jqxGrid('showcolumn','MTR');
				$("#mainData").jqxGrid('showcolumn','CR');
				$("#mainData").jqxGrid('showcolumn','TotThesis');
				$("#mainData").jqxGrid('showcolumn','Tot6971');
				$("#mainData").jqxGrid('showcolumn','TotNonThesis');
				$("#mainData").jqxGrid('showcolumn','TotCert');
				$("#mainData").jqxGrid('showcolumn','TotDoc');
				$("#mainData").jqxGrid('showcolumn','TotDissert');
				$("#mainData").jqxGrid('showcolumn','PlanLongName');
				$("#mainData").jqxGrid('showcolumn','SubPlanLongName');	
				$("#mainData").jqxGrid('showcolumn','DeptLongName');	
			};
			
			filtergroup.addfilter(filter_or_operator, filter1);
			
			// add the filters.
			$("#mainData").jqxGrid('addfilter', 'Career', filtergroup);
			
			// apply the filters.
			$("#mainData").jqxGrid('applyfilters');			
			
		});
		
		$('#clearfilterbutton').click(function () {
		  		//remove only the column filters.
				
				//filters not to touch if initiated
				var Restricted = ["College","Career","Subplan"];

				//get the applied filters
				var myStringArray = $("#mainData").jqxGrid('getfilterinformation');
				var arrayLength = myStringArray.length;
				for (var i = 0; i < arrayLength; i++) {
					//loop thru and remove the filters NOT in the Restricted array
					var datafield = myStringArray[i]['datafield'];
					if(Restricted.indexOf(datafield) == -1){
						$("#mainData").jqxGrid('removefilter', datafield);
					}
				    
				}
        	
		    });
		
	});

</script>	

	<div id="page_title">Program.......</div>
	
	<div id="page_summary">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
	Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
	</div>
<div id="option_area">	
	<div id="area1">
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
					<?php 
						if(!in_array('Orientation',$user_groups)){
					?>
						<option value="" <?php echo $s; ?>>All</option>
					<?php } ?>
					
					<option value="UGRD" <?php echo $ugs; ?>>Undergraduate</option>
					
					<?php 
						if(!in_array('Orientation',$user_groups)){
					?>
						<option value="GRAD" <?php echo $grads; ?>>Graduate</option>
						<option value="PROF" <?php echo $prof; ?>>Professional</option>
					<?php } ?>
					
				</select> 
			</li>
			<li>
				<label>UCF Online:</label>
				<input id="ponline-check" type="checkbox" name="ucfonlinecheck" class="styled-check"><br>
			</li>
		</fieldset>
	</div>
	<div id="area2">
		<fieldset>   
		<legend>Session Log</legend>
		<div id="session_log"></div>
		</fieldset>
	</div>
</div>		

	<div >
		Export table: 
		<!-- <a title="Excel" id="xlsExport"><img align="top" src="../img/page_excel.png"></a> -->
		<a title="CSV" id="csvExport"><img align="top" src="../img/page_white_text.png"></a>
		|
		<a title="Remove Column Filters" id="clearfilterbutton">Remove Filters</a>
		
	</div>
<br />
 
<div class="container">	
	<div id="data-section">
		<div id="mainData"></div>
		<div id="mainGridPager"></div>
	</div>
</div>
<?php

	$this->load->view("footer");
?>
