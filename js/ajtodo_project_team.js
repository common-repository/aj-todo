jQuery(document).ready(function($){
	var makeMember = function(id, name){
		var userbox = "<div class='btn-group' role='group' val="+id+">";
			userbox += "<button type='button' class='disabled btn btn-secondary'>"+name+"</button>";
			userbox += "<button type='button' class='btn btn-danger'><i class='far fa-minus-square'></i></button>";
			userbox += "</div>";
		return $(userbox);
	}
	var hasin = function(id){
		var ret = false;
		$.each($("#ajtodo_addmember_ing .btn-group"), function(k, v){
			if($(v).attr("val") == id)
				ret = true;
		});
		return ret;
	}
	var getsetuser = function(){
		var ret = [];
		$.each($("#ajtodo_addmember_ing .btn-group"), function(k, v){
			ret.push($(v).attr("val"));
		});
		return ret;
	}
	$("#ajtodo_update_addthismember").click(function(e){
		e.preventDefault();
		var id = $("#ajtodo_sel_member option:selected").val();
		var username = $("#ajtodo_sel_member option:selected").text();
		if(!hasin(id)){
			var item = makeMember(id, username);
			$("#ajtodo_addmember_ing").append($(item));
			$(item).find(".btn-danger").click(function(e){
				e.preventDefault();
				$(this).parent().remove();
			});
		}
	});
	$("#ajtodo_del_role").click(function(e){
		e.preventDefault();
		$("#dotype").val("del");
		$("#ajtodo_form").submit();
	});
	$("#ajtodo_update_role").click(function(e){
		e.preventDefault();
		if($("#dotype").val() == "add"){
			if(!onlyAlphaNumber($("#ajtodo_status_key").val())){
				alert(ajtodo_lan.alert_onlyalpha);
				return;
			}
		}
		$("#ajtodo_form").submit();
	});
	$("#ajtodo_update_memberrole").click(function(e){
		e.preventDefault();
		$("#ajtodo_users").val(getsetuser());
		$("#ajtodo_role").val($("#ajtodo_sel_member option:selected").val());
		$("#ajtodo_form").submit();
	});
	$("#ajtodo_addmember_ing .btn-danger").click(function(e){
		e.preventDefault();
		$(this).parent().remove();
	});
	$("#ajtodo_icon_box button").click(function(e){
		e.preventDefault();
		$.each($("#ajtodo_icon_box button"), function(k, v){
			$(this).removeClass("btn-primary");
		});
		$("#ajtodo_status_icon").val($(this).attr("val"));
		$(this).addClass("btn-primary");
	});
	var makeRole = function(key, name){
		var rolebox = "<div class='btn-group' role='group' val="+key+">";
			rolebox += "<button type='button' class='disabled btn btn-secondary'>"+name+"</button>";
			rolebox += "<button type='button' class='btn btn-danger'><i class='far fa-minus-square'></i></button>";
			rolebox += "</div>";
		return $(rolebox);
	}
	var hasinrole = function(id, keyname){
		var ret = false;
		$.each($(".statusrolebox[val='"+keyname+"'] .btn-group"), function(k, v){
			if($(v).attr("val") == id)
				ret = true;
		});
		return ret;
	}
	$("#ajtodo_rule_table .btnaddroletostatus").click(function(e){
		e.preventDefault();
		var keyname = $(this).attr("val");
		var rolekey = $(".ajtodo_sel_role[val='"+keyname+"'] option:selected").val();
		var rolename = $(".ajtodo_sel_role[val='"+keyname+"'] option:selected").text();
		if(!hasinrole(rolekey, keyname)){
			var item = makeRole(rolekey, rolename);
			$(".statusrolebox[val='"+keyname+"']").append($(item));
			$(item).find(".btn-danger").click(function(e){
				e.preventDefault();
				$(this).parent().remove();
			});
		}
	});
	$("#ajtodo_update_statusrole").click(function(e){
		e.preventDefault();
		var data = [];
		$.each($(".statusrolebox"), function(k, v){
			var to_status = $(this).attr("val");
			var roles = [];
			$.each($(".statusrolebox[val='"+to_status+"'] .btn-group"), function(k1, v1){
				roles.push($(v1).attr("val"));
			});
			if(roles.length > 0){
				var actionrole = {
					to : to_status,
					roles : roles
				};
				data.push(actionrole);
			}
		});
		$("#actionrole").val(JSON.stringify(data));
		$("#ajtodo_form").submit();
	});
	$("#btnSavePlan").click(function(e){
		e.preventDefault();
		if($("#ajtodo_plan_title").val().trim() == ""){
			alert(ajtodo_lan.alert_plantitle);
			$("#ajtodo_plan_title").focus();
			return false;
		}
		$("#ajtodo_form").submit();
	});
	$(".statusrolebox .btn-danger").click(function(e){
		e.preventDefault();
		$(this).parent().remove();
	});
	$("#btnDelTodoType").click(function(e){
		e.preventDefault();
		$("#dotype").val("del");
		$("#ajtodo_form").submit();
	});
	$("#btnDelCategory").click(function(e){
		e.preventDefault();
		$("#dotype").val("del");
		$("#ajtodo_form").submit();
	});
	$("#btnFinPlan").click(function(e){
		e.preventDefault();
		$("#dotype").val("finish");
		$("#ajtodo_form").submit();
	});
	$("#btnDelPlan").click(function(e){
		e.preventDefault();
		$("#dotype").val("del");
		$("#ajtodo_form").submit();
	});
	$("#ajtodo_color_box button").click(function(e){
		e.preventDefault();
		var val = $(this).attr("val");
		$.each($("#ajtodo_color_box button"), function(k, v){
			$(v).empty();
			if(val != $(this).attr("val"))
				$(v).append("<i class='far fa-circle'></i>");
		});
		$("#ajtodo_todotype_color").val($(this).attr("val"));
		$(this).append("<i class='far fa-check-circle'></i>");
	});
	$("#ajtodobtn_save_category").click(function(e){
		e.preventDefault();
		if($("#dotype").val() == "add"){
			if(!onlyAlphaNumber($("#ajtodo_category_key").val())){
				alert(ajtodo_lan.alert_onlyalpha);
				$("#ajtodo_category_key").focus();
				return;
			}
		}
		if($("#ajtodo_category_name").val() == ""){
			alert(ajtodo_lan.alert_pleaseentervalue);
			$("#ajtodo_category_name").focus();
			return;
		}
		$("#ajtodo_form").submit();
	});
	$("#ajtodobtn_save_todotype").click(function(e){
		e.preventDefault();
		if($("#dotype").val() == "add"){
			if(!onlyAlphaNumber($("#ajtodo_todotype_key").val())){
				alert(ajtodo_lan.alert_onlyalpha);
				$("#ajtodo_todotype_key").focus();
				return;
			}
		}
		if($("#ajtodo_todotype_name").val() == ""){
			alert(ajtodo_lan.alert_pleaseentervalue);
			$("#ajtodo_todotype_name").focus();
			return;
		}
		$("#ajtodo_form").submit();
	});
	$("#ajtodobtn_cancel_todotype").click(function(e){
		e.preventDefault();
		var pid = $("#d_p_id").val();
		window.location = '?page=ajtodo_admin_project&ptype=team&ajtodo_type=todotype&pid='+pid;
	});
	$("#ajtodobtn_cancel_category").click(function(e){
		e.preventDefault();
		var pid = $("#d_p_id").val();
		window.location = '?page=ajtodo_admin_project&ptype=team&ajtodo_type=category&pid='+pid;
	});
	var setInit = function(){
		$.each($(".ajtodo_remain"), function(k, v){
			var fdate = $(v).attr("value");
			if(fdate != ""){
				$(this).text(moment(fdate).fromNow());
			}
		});
	}
	setInit();
});
