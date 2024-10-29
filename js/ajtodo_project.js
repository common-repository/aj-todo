jQuery(document).ready(function($){
	$("#btnCreateProject").click(function(e){
		e.preventDefault();
		
		if(ajax_project_info.type == "add"){
			if(!onlyAlphaNumber($("#ajtodo_pkey").val())){
				alert(ajtodo_lan.alert_onlyalpha);
				return;
			}
		}
		if($("#ajtodo_title").val() == ""){
			alert(ajtodo_lan.need_projectname);
			return;
		}
		var data = $('#ajtodo_form').serialize();
		data += "&action=ajtodo_project_ajax";
		data += "&nonce=" + ajax_project_info.nonce;
		data += "&type=" + ajax_project_info.type;
		data += "&pid=" + ajax_project_info.pid;
		$(this).addClass("disabled");
        $.ajax({
            url : ajax_project_info.ajax_url,
            type : 'post',
            data : data,
            success : function(response) {
				var res = $.parseJSON(response);
				if(res.result){
					if(res.projecttype == "team"){
						window.location = "?page=ajtodo_admin_project&ptype=team&ajtodo_type=todo&pid="+res.pid;
					}else{
						window.location = "?page=ajtodo_admin_project";
					}
				}else{
					$.notify({ message: res.msg },{ type: "danger" });
					$(this).removeClass("disabled");
				}
			}
		});
	});
	$("#ajtodo_projecttype_private").click(function(e){
		$("#aassign").hide();
	});
	$("#ajtodo_projecttype_team").click(function(e){
		$("#aassign").show();
	});
	$("#btnDoneProject").click(function(e){
		e.preventDefault();
		doneProject();
	});
	var doneProject = function(){
		var data = "&action=ajtodo_project_ajax";
		data += "&nonce=" + ajax_project_info.nonce;
		data += "&pid=" + ajax_project_info.pid;
		data += "&type=projectdone";
        $.ajax({
            url : ajax_project_info.ajax_url,
            type : 'post',
            data : data,
            success : function(response) {
				var res = $.parseJSON(response);
				if(res.result){
					window.location = "?page=ajtodo_admin_project";
				}else{
					$.notify({ message: res.msg },{ type: "danger" });
					$(this).removeClass("disabled");
				}
			}
		});
	}
	$("#btnActiveProject").click(function(e){
		e.preventDefault();
		var data = "&action=ajtodo_project_ajax";
		data += "&nonce=" + ajax_project_info.nonce;
		data += "&pid=" + ajax_project_info.pid;
		data += "&type=activedone";
        $.ajax({
            url : ajax_project_info.ajax_url,
            type : 'post',
            data : data,
            success : function(response) {
				var res = $.parseJSON(response);
				if(res.result){
					window.location = "?page=ajtodo_admin_project&ptype=team&ajtodo_type=todo&pid="+ajax_project_info.pid;
				}else{
					$.notify({ message: res.msg },{ type: "danger" });
					$(this).removeClass("disabled");
				}
			}
		});
	});
	$("#btnDelProject").click(function(e){
		e.preventDefault();
		delProject();
	});
	var delProject = function(){
		var data = "&action=ajtodo_project_ajax";
		data += "&nonce=" + ajax_project_info.nonce;
		data += "&pid=" + ajax_project_info.pid;
		data += "&type=delprojectreal";
        $.ajax({
            url : ajax_project_info.ajax_url,
            type : 'post',
            data : data,
            success : function(response) {
				var res = $.parseJSON(response);
				if(res.result){
					window.location = "?page=ajtodo_admin_project";
				}else{
					$.notify({ message: res.msg },{ type: "danger" });
					$(this).removeClass("disabled");
				}
			}
		});
	}
	var getProjectIssueCount = function(pid, suc, fail){
		var data = "&action=ajtodo_project_ajax";
		data += "&nonce=" + ajax_project_info.nonce;
		data += "&type=getissuecount";
		data += "&pid=" + ajax_project_info.pid;
        $.ajax({
            url : ajax_project_info.ajax_url,
            type : 'post',
            data : data,
            success : function(response) {
				var res = $.parseJSON(response);
				if(res.result){
					suc(res);
				}else{
					$.notify({ message: res.msg },{ type: "danger" });
					fail(res);
				}
			}
		});
	}
});
