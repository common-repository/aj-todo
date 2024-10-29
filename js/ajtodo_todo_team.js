jQuery(document).ready(function($){
	var todo_status_changed = false;
	var todo_search = "";
	var todo_filter_project = ajax_todo_info.filter_project;
	var todo_allstatus = $.parseJSON(ajax_todo_info.statuses);
	var todo_alltodotype = $.parseJSON(ajax_todo_info.todotypes);
	var todo_permstatus = $.parseJSON(ajax_todo_info.currentuserstatus);
	var todo_members = $.parseJSON(ajax_todo_info.members);
	var todo_category = $.parseJSON(ajax_todo_info.category);
	var todo_option = $(todo_option_html);
	var todo_view = $(todo_view_html);
	var todo_status_s = "";
	var todo_status_d = "";
	var todo_type = "";
	var todo_status_when_s = [];
	var todo_status_when_d = [];
	var todo_status_when_i = [];
	var todo_projects = [];
	var todo_list = [];
	var todo_status_info = [];
	var todo_todotypes = [];
	var todo_nowtodotypekey = "";
	var isView = false;
	var selectedplan = {};
	var nowtodoid = "";
	var nowtodoplanid = "";
	var to_planid = "";
	var show_doneplan = "N";
	var total_todo_count = 0;
	
	var ajtodoisEmpty = function(value){ if( value == "" || value == null || value == undefined || ( value != null && typeof value == "object" && !Object.keys(value).length ) ){ return true }else{ return false } };
	$.notifyDefaults({ delay : ajax_todo_info.noti_delay });
	//$.notifyDefaults({ delay : 0 });
	$("#sjtodo_search").keydown(function(key){
		if(key.keyCode == 13){
			todo_search = $(this).val().trim();
			getList();
		}
	});
	$("#btnQuickTodo").click(function(key){
		if($("#ajtodo_title").val().length > 0){
			var data = "action=ajtodo_team_todo_ajax";
			data += "&nonce=" + ajax_todo_info.nonce;
			data += "&type=createtodo";
			data += "&pid=" + $("#ajtodo_projectid").val();
			data += "&title=" + $("#ajtodo_title").val();
			ajaxCall(data, "post", 
				function(res){
					$("#ajtodo_title").val("");
					//$.notify({ message: res.msg });
				}, 
				function(msg){
					console.log(1);
					$.notify({ message: res.msg },{ type: "danger" });
				});
		}
	});
	$("#ajtodo_todotitle").keydown(function(key){
		if(key.keyCode == 13 && $(this).val().length > 0){
			var data = "action=ajtodo_team_todo_ajax";
			data += "&nonce=" + ajax_todo_info.nonce;
			data += "&type=createtodo";
			data += "&pid=" + todo_filter_project;
			data += "&todotype=" + todo_nowtodotypekey;
			data += "&planid=" + ajax_todo_info.planid;
			data += "&title=" + $(this).val();
			ajaxCall(data, "post", 
				function(res){
					$("#ajtodo_todotitle").val("");
					getPlanTodoList();
					getProgress();
					getTodoTypes();
					getSingleTodo(res.todoid, "add",
						function(response){
							if((todo_filter_project != "no" && todo_filter_project != "" && response.data.projectid != todo_filter_project) ||
								(todo_filter_project == "no" && response.data.projectid != undefined)){
								return;
							}
							todo_list.push(response.data);
							$("#todo_list").prepend(gettodoitem(response.data));
						},
						function(msg){
							console.log(msg);
						}
					);
				}, 
				function(msg){
					console.log(msg);
				});
		}
	});
	var ajaxCall = function(data, type, suc, error){
        $.ajax({
            url : ajax_todo_info.ajax_url,
            type : type,
            data : data,
            success : function(response) {
				var res = $.parseJSON(response);
				if(res.result){
					if(res.msg != undefined){
						$.notify({ message: res.msg },{ type: "success" });
					}
					suc(res);
				}else{
					$.notify({ message: res.msg },{ type: "danger" });
					error(res.msg);
				}
			}
		});
	}
	var setLoading = function(){
		$("#todo_list").empty();
		var loading = "<div class='text-center loadingbar'>";
		loading += "<div class='spinner-border' role='status'>"+todo_lan.loadingtext+"</div> ";
		loading += "</div>";
		$("#todo_list").append(loading);
	}
	var getList = function(){
		setLoading();
		todo_status_when_s = [];
		todo_status_when_d = [];
		todo_status_when_i = [];
		var data = "action=ajtodo_team_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&type=gettodolist";
		data += "&uid="+(ajax_todo_info.uid == undefined ? "" : ajax_todo_info.uid);
		data += "&pid="+todo_filter_project;
		data += "&planid="+ajax_todo_info.planid;
		data += "&filter_user="+(ajax_todo_info.user_filter == undefined ? "" : ajax_todo_info.user_filter);
		data += "&statuskey="+(ajax_todo_info.status_filter == undefined ? "" : ajax_todo_info.status_filter);
		data += "&search="+todo_search;
		data += "&todotype="+todo_type;
		ajaxCall(data, "post", 
			function(res){
				setShortcode();
				$("#todo_list").empty();
				todo_status = res.statuses;
				$.each(todo_status, function(k, v){
					if(v.statustype == 'S'){
						todo_status_when_d.push(v);
						todo_status_when_i.push(v);
					}
					if(v.statustype == 'I'){
						todo_status_when_s.push(v);
						todo_status_when_i.push(v);
						todo_status_when_d.push(v);
					}
					if(v.statustype == 'D'){
						todo_status_when_s.push(v);
						todo_status_when_i.push(v);
					}
					if(v.statustype == "S")
						todo_status_s = v.id;
					if(v.statustype == "D")
						todo_status_d = v.id;
				});
				todo_list = res.todolist;
				$.each(res.todolist, function(k, v){
					$("#todo_list").append(gettodoitem(v));
				});
			}, 
			function(msg){
				console.log(msg);
			});
	};
	$(document).on("click", "#todo_list li a.todo_title", function(e){
		e.preventDefault();
		var tid = $(this).parent().attr("var");
		console.log(tid);
		var todo;
		$.each(todo_list, function(k, v){
			if(v.id == tid){
				todo = v;
			}
		});
		viewDetail(todo);
	});
	var setActionBox = function(isDetailView){
		if(isDetailView){
			$(".todo_option_inline").removeClass("float-right");
			$(".todo_option_inline_box").removeClass("float-right");
		}else{
			$(".todo_option_inline").addClass("float-right");
			$(".todo_option_inline_box").addClass("float-right");
		}
	}
	var viewDetail = function(todo){
		isView = true;
		//$(todo_option).find("#todo_status").empty();
		//$(todo_option).find("#todo_actions").empty();
		$("#todoview").empty();
		setActionBox(true);
		
		setTodoBox(todo);
		
		var status = getstatusinfo(todo.statuskey);
		if(ajax_todo_info.projecttype == "private"){
			$(todo_view).find(".isonlyteam").hide();
		}else{
			$(todo_view).find(".isonlyteam").show();
		}
		$(todo_view).find("#ajtodo_sv_tkey").html(todo.tkey);
		$(todo_view).find("#ajtodo_sv_status").text(status.name);

		$(todo_view).find("#ajtodo_sv_regdate").text(todo_lan.regdate + " : " + todo.regdate);
		if(todo.donedated != null){
			$(todo_view).find("#ajtodo_sv_donedate").text(todo_lan.donedate + " : " + todo.donedated);
		}

		$(todo_view).find("#ajtodo_sv_status").addClass("badge-"+getactionstatuscss(status.statustype));
		$(todo_view).find("#ajtodo_sv_title").html(ajtodo_setText(todo, "title", true, 
			(!ajax_todo_info.todoperms.includes("tp_todo_updateinfo") || ajax_todo_info.freeze != "")));
		$(todo_view).find("#ajtodo_content").html(ajtodo_setTextArea(todo, "comment", false, 
			(!ajax_todo_info.todoperms.includes("tp_todo_updateinfo") || ajax_todo_info.freeze != "")));
		$(todo_view).find("#ajtodo_sv_author").html(setAssignee(todo.id, todo.authorid, true, false));
		$(todo_view).find("#ajtodo_sv_assignee").html(setAssignee(todo.id, todo.assignid, ajax_todo_info.freeze != "", false));
		$(todo_view).find("#ajtodo_sv_category").html(setCategory(todo, ajax_todo_info.freeze != ""));
		$(todo_view).find("#ajtodo_sv_todotype").html(setTodoType(todo, ajax_todo_info.freeze != ""));
		$("#ajtodo_todoview").append($(todo_view));

		$("#ajtodo_sv_action").empty();
		if(ajax_todo_info.freeze == ""){
			$("#ajtodo_vd_actionbox").show();
			$("#ajtodo_sv_action").append($(todo_option));
		}else{
			$("#ajtodo_vd_actionbox").hide();
		}
		//$("#ajtodo_todoview").css("width","0%");
		//$("#ajtodo_listview").css("width","100%");
		if(todo.statuskey == "closed"){
			$("#ajtodo_todoview").css("background","inherit");
		}else{
			$("#ajtodo_todoview").css("background","#fff");
		}
		$("#ajtodo_todoview").css("border","1px solid lightgray");
		
		$("#ajtodo_listview").hide();
		$("#ajtodo_todoview").show();
	}
	$(todo_view).find("#ajtodo_sv_close").click(function(e){
		e.preventDefault();
		isView = false;
		setActionBox(false);
		//$("#ajtodo_listview").css("width","100%");
		//$("#ajtodo_todoview").css("width","0%");
		//$("#ajtodo_todoview").css("border","0px");

		$("#ajtodo_todoview").hide();
		$("#ajtodo_listview").show();
	});
	var getSingleTodo = function(todo_id, isadd,  suc, err){
		var data = "action=ajtodo_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&type=getsingletodo";
		data += "&pid="+todo_filter_project;
		data += "&todo_id="+todo_id;
		ajaxCall(data, "post", 
			function(res){
				if(isadd == "up"){
					$.each(todo_list, function(k, v){
						if(v.id == res.data.id){
							todo_list[k] = res.data;
						}
					});
				}
				suc(res);
			}, 
			function(res){
				console.log(msg);
			});
	}
	var updateTodoSuc = function(v){
		getSingleTodo(v.todoid, "", 
			function(response){
				getPlanTodoList();
				getProgress();
				var newli = gettodoitem(response.data);
				if(isView)
					viewDetail(response.data);

				$("li.todo_list_item[var="+v.todoid+"]").replaceWith(newli);
				var ishide = false;
				if(ajax_todo_info.auto_done_hidden == "Y"){
					if(ajax_todo_info.status_filter != "" && (ajax_todo_info.status_filter != response.data.statuskey)){
						ishide = true;
						$(newli).fadeOut(1000, function(){
							$(this).remove();
						});
					}
				}
				if(!ishide && todo_status_changed){
					ajtodo_setItemOrder(newli, response.data);
					todo_status_changed = false;
				}
			},
			function(response){
				$("li.todo_list_item[var="+v.todoid+"]").css("background", "inherit");
			}
		);
	}
	var ajtodo_setItemOrder = function(newli, data){
		if(ajax_todo_info.status_filter == ""){
			if(data.status_type == "I"){
				$.each($("li.todo_list_item"), function(k, v){
					if($(v).attr("var") != data.id){
						$(v).before(newli);
						return false;
					}
				});
			}else if(data.status_type == "D"){
				$.each($("li.todo_list_item"), function(k, v){
					if($(v).attr("var") != data.id && $(v).attr("status") == "D"){
						$(v).before(newli);
						return false;
					}
				});
			}else if(data.status_type == "S"){
				$.each($("li.todo_list_item"), function(k, v){
					if($(v).attr("var") != data.id && $(v).attr("status") == "S"){
						$(v).before(newli);
						return false;
					}
				});
			}
		}
	}
	var updateTodoErr = function(v){
		console.log(v);
	}
	var getcolorstatuscss = function(statuskey){
		switch(statuskey){
			case "open" : return "primary";
			case "closed" : return "success";
		}
		return "warning";
	}
	var getactionstatuscss = function(statustype){
		switch(statustype){
			case "S" : return "primary";
			case "I" : return "warning";
			case "D" : return "success";
		}
	}
	var updateCols = function(colval, tid, suc, err){
		var data = "action=ajtodo_team_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&type=updatetodo";
		data += "&colval="+JSON.stringify(colval);
		data += "&pid="+todo_filter_project;
		data += "&todo_id="+tid;
		ajaxCall(data, "post", suc, updateTodoErr);
	}
	var updateCol = function(col, val, tid, suc, err){
		var data = "action=ajtodo_team_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&todo_id="+tid;
		data += "&pid="+todo_filter_project;
		data += "&type=updatetodo";
		data += "&col="+col;
		data += "&val="+val;
		data += "&todo_id="+tid;
		ajaxCall(data, "post", suc, updateTodoErr);
	}
	var showDate = function(dt){
		if(moment(dt) > moment().add(-3, 'days')){
			return moment(dt).fromNow();
		}else{
			return moment(dt).format("YYYY-MM-DD");
		}
	}
	var gettodotile = function(v){
		//return v.title;
		return "<span class='badge'>"+v.tkey+"</span>"+v.title;
	}
	var getstatusicon = function(todo){
		var icon = "";
		$.each(todo_allstatus, function(k, v){
			if(v.key == todo.statuskey){
				icon = v.icon;
			}
		});
		return "<i class='quickdo far "+icon+"'></i>";
	}
	var getcategoryinfo = function(action){
		var category;
		$.each(todo_category, function(k, v){
			if(v.key == action){
				category = v;
			}
		});
		return category;
	}
	var gettodotypeinfo = function(action){
		var todotype;
		$.each(todo_alltodotype, function(k, v){
			if(v.key == action){
				todotype = v;
			}
		});
		return todotype;
	}
	var getstatusinfo = function(action){
		var status;
		$.each(todo_allstatus, function(k, v){
			if(v.key == action){
				status = v;
			}
		});
		return status;
	}
	var setstatusaction = function(todo, actiondiv){
		$.each(todo_permstatus, function(k, v){
			if(v.key == todo.statuskey){
				$.each(v.action, function(k1, action){
					var status = getstatusinfo(action);
					var btn = "<button type='button' class='btn btn-info btn_status' val='"+action+"'><i class='far "+status.icon+"'></i> "+status.name+"</button>";
					$(actiondiv).find("#todo_status").append(btn);
				});
			}
		});
	}
	var setTodoBox = function(v){
		$(todo_option).attr("val", v.id);				
		$(todo_option).find("#todo_status").empty();
		$(todo_option).find("#todo_actions").empty();
		if(ajax_todo_info.todoperms.includes("tp_todo_del")){
			$(todo_option).find("#todo_actions").append("<button type='button' class='btn btn-danger float-right ajtodo_del'>"+todo_lan.del+"</button>");
		}
		if(ajax_todo_info.todoperms.includes("tp_todo_status")){
			setstatusaction(v, $(todo_option));
		}
	}
	$(document).on("click", "#ajtodo_sv_action button.btn, #ajtodo_sv_inline_action button.btn", function(e){
		if($(this).hasClass("ajtodo_del")){
			if($(this).hasClass("ajtodo_del_now")){
				$(todo_view).find("#ajtodo_sv_close").click();
				delTodo($(todo_option).attr("val"));
			}else{
				delTodoCheck($(this), $(todo_option).attr("val"));
			}
		}
		if($(this).hasClass("btn_status")){
			todo_status_changed = true;
			var data = {};
			if($(this).attr("val") == "open"){
				data = { "statuskey" : $(this).attr("val"), "donedated" : ":_NULL_:" }
			}else if($(this).attr("val") == "closed"){
				data = { "statuskey" : $(this).attr("val"), "donedated" : ":_NOW_:" }
			}else {
				data = { "statuskey" : $(this).attr("val") }
			}
			updateCols(data, $(todo_option).attr("val"), updateTodoSuc, updateTodoErr);
		}
	});
	var gettodoitem = function(v){
		var todo;
		var thistodotype = gettodotypeinfo(v.todotype);
		var li = "<li class='todo_list_item list-group-item-action list-group-item' ";
		if(ajax_todo_info.isshortcode == ""){
			li += " status='"+v.statuskey+"' ";
		}
		li += " var='"+v.id+"' planid='"+v.planid+"'></li>";
		todo = $(li);
		todo.append("<span class='todotypepipe' style='border-left : 3px solid "+thistodotype.color + ";'></span>");
		//todo.css("border-left", "4px solid "+thistodotype.color);
		if(ajax_todo_info.isshortcode == ""){
			var title = "<a href='#' class='todo_title "+(v.donedated != null ? "done" : "")+"'>"+getstatusicon(v)+gettodotile(v);
			if(v.comment != ""){
				title += "<i class='far fa-file-alt' style='margin-left:4px;'></i>";
			}
			if(v.categorykey != null){
				var thiscategory = getcategoryinfo(v.categorykey);
				title += "<kbd style='margin-left:8px;font-size:12px;background:#61a4cc'>"+thiscategory.name+"</kbd>";
			}
			title += "</a>";
			todo.append(title);
		}else{
			var title = "<span class='todo_title'>"+getstatusicon(v)+gettodotile(v);
			title += "</span>";
			if(v.categorykey != null){
				var thiscategory = getcategoryinfo(v.categorykey);
				title += "<kbd style='margin-left:8px;font-size:12px;background:#61a4cc'>"+thiscategory.name+"</kbd>";
			}
			todo.append(title);
		}
		if(v.assignid != null)
			todo.append("<div class='clearfix float-right inline_assignee'>"+setAssignee(v.id, v.assignid, true, true)+"</div>");
		if(v.donedated != undefined){
			todo.append("<kbd class='clearfix float-right' style='padding:3px 4px;margin-right:4px;'>"+showDate(v.donedated)+"</kbd>");
		}
		todo.mouseenter(function(){
			if(isView)
				return;

			if(ajax_todo_info.freeze == ""){
				setTodoBox(v);
				$(this).find('kbd.float-right').hide();
				$(this).append($(todo_option));
			}

			//$(todo_option).find("a.todo_project").click(function(e){
			//	e.preventDefault();
			//	var tid = $(this).attr("tid");
			//	var pid = $(this).attr("val");
			//	todo_status_changed = false;
			//	updateCol("projectid", pid, tid, updateTodoSuc, updateTodoErr);
			//});
		});
		todo.mouseleave(function(){
			if(ajax_todo_info.freeze == ""){
				$(this).find('kbd').show();
				$(this).find('.todo_option_inline').remove();
			}
		});
		var ismovable = false;
		if((ajax_todo_info.planview == "Y" && selectedplan.donedate == "") || ajax_todo_info.planid == "0"){
			ismovable = true;
		}
		if(ismovable && ajax_todo_info.planperms.includes("tp_plan_manage")){
			todo.css('cursor', 'pointer');
			todo.attr('draggable', true);
			todo.on("dragstart", function(e){ $(this).addClass("moving"); });
			todo.on("dragend", function(e){ $(this).removeClass("moving"); });
			todo.mousedown(function(){
				nowtodoid = $(this).attr("var");
				nowtodoplanid = $(this).attr("planid");
			});
		}
		return todo;
	}
	var delTodoCheck = function(btn, tid){
		if(ajax_todo_info.del_noconfirm == "Y"){
			delTodo(tid);
		}else{
			$(btn).text(todo_lan.delconfirm);
			$(btn).addClass("ajtodo_del_now");
		}
	}
	var delTodo = function(tid){
		var data = "action=ajtodo_team_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&type=deltodo";
		data += "&pid="+todo_filter_project;
		data += "&todo_id="+$(todo_option).attr("val");
		ajaxCall(data, "post", 
			function(response){
				getPlanTodoList();
				getProgress();
				$("#todo_list li[var="+response.todoid+"]")
					.addClass("list-group-item-danger")
					.attr("disabled", "");
				$("#todo_list li[var="+response.todoid+"]").fadeOut(1000, function(){
					$(this).remove();
				});
			}, 
			function(msg){
				err(msg);
			});
	}
	var setTodo = function(id, s_id, suc, err){
		var data = "action=ajtodo_team_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&type=updatestatus";
		data += "&todo_id="+id;
		data += "&status_id="+s_id;
		ajaxCall(data, "post", 
			function(response){
				getProgress();
				suc(response);
			}, 
			function(msg){
				err(msg);
			});
	}
	$("#ajtodo_todolist_user_filter button").click(function(e){
		e.preventDefault();
		ajax_todo_info.user_filter = $(this).attr("val");
		getList();
	});
	var getProgress = function(){
		if(ajax_todo_info.progressview == "N")
			return;
		todo_status_info = [];
		var data = "action=ajtodo_team_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&uid="+ajax_todo_info.uid;
		data += "&pid="+todo_filter_project;
		data += "&planid="+ajax_todo_info.planid;
		data += "&type=getprogress";
		ajaxCall(data, "post", 
			function(res){
				total_todo_count = 0;
				var opencount = 0;var donecount = 0;var ingcount = 0;
				$.each(res.statuses, function(k, v){
					var status = getstatusinfo(v.statuskey);
					switch(status.statustype){
						case "S" : opencount += parseInt(v.cnt); break;
						case "I" : ingcount += parseInt(v.cnt); break;
						case "D" : donecount += parseInt(v.cnt); break;
					}
					todo_status_info.push({
						statuskey : v.statuskey,
						name : status.name,
						statustype : status.statustype,
						count : parseInt(v.cnt)
					 });
					 total_todo_count += parseInt(v.cnt);
				});
				setProjectEmpty(total_todo_count == 0);
				if(total_todo_count > 0){
					var s = opencount * 100 / total_todo_count;
					var d = donecount * 100 / total_todo_count;
					var i = 100 - s - d;
					$("#ajtodo_userprogress .bg-primary").css("width", s+"%");
					$("#ajtodo_userprogress .bg-warning").css("width", i+"%");
					$("#ajtodo_userprogress .bg-success").css("width", d+"%");
					updateStatuInfo();
				}
			}, 
			function(res){
			});
	}
	var setProjectEmpty = function(isempty){
		if(isempty){
			$("#ajtodo_top_filter_box_empty").show();
			$("#ajtodo_top_filter_box").hide();
			$("#ajtodo_userprogress").hide();
		}else{
			$("#ajtodo_top_filter_box_empty").hide();
			$("#ajtodo_top_filter_box").show();
			$("#ajtodo_userprogress").show();
		}
	}
	$(document).on("click", "#sjtodo_nowstatusinfo button", function(e){
		e.preventDefault();
		statusfilterinit();
		if(ajax_todo_info.status_filter == $(this).attr("val")){
			ajax_todo_info.status_filter = "";
		}else{
			$(this).addClass("active");
			ajax_todo_info.status_filter = $(this).attr("val");
		}
		getList();
	});
	var statusfilterinit = function(){
		$.each($("#sjtodo_nowstatusinfo button"), function(k, v){
			$(v).removeClass("active");
		});
	}
	var updateStatuInfo = function(){
		$("#sjtodo_nowstatusinfo").empty();
		$.each(todo_status_info, function(k, v){
			if(v.statustype == "S"){
				var btnS = "<button type='button' id='ajtodo_todolist_status_open' val='"+v.statuskey+"'";
				btnS += " class='btn btn-primary "+(ajax_todo_info.status_filter == v.statuskey ? "active" : "")+"'>";
				btnS += v.name + " <span class='badge badge-light'>"+v.count+"</span></button>";
				$("#sjtodo_nowstatusinfo").append(btnS);
			}
		});
		$.each(todo_status_info, function(k, v){
			if(v.statustype == "I"){
				var btnS = "<button type='button' id='ajtodo_todolist_status_open' val='"+v.statuskey+"'";
				btnS += " class='btn btn-warning "+(ajax_todo_info.status_filter == v.statuskey ? "active" : "")+"'>";
				btnS += v.name + " <span class='badge badge-light'>"+v.count+"</span></button>";
				$("#sjtodo_nowstatusinfo").append(btnS);
			}
		});
		$.each(todo_status_info, function(k, v){
			if(v.statustype == "D"){
				var btnS = "<button type='button' id='ajtodo_todolist_status_open' val='"+v.statuskey+"'";
				btnS += " class='btn btn-success "+(ajax_todo_info.status_filter == v.statuskey ? "active" : "")+"'>";
				btnS += v.name + " <span class='badge badge-light'>"+v.count+"</span></button>";
				$("#sjtodo_nowstatusinfo").append(btnS);
			}
		});
	}
	var getprojectnamebyid = function(pid){
		var pname = "";
		$.each(todo_projects, function(k, p){
			if(p.id == pid){
				pname = p.title;
			}
		});
		return pname;
	}
	var setProjectList = function(v){
		var pname = v.projectid ?  getprojectnamebyid(v.projectid) : todo_lan.noproject;
		var plist = "<button type='button' class='btn btn-info disabled' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>"+pname+"</button>";
		//plist += "<div class='dropdown-menu'>";
		//$.each(todo_projects, function(k, p){
		//	if(v.projectid != p.id){
		//		plist += "	<a class='dropdown-item todo_project' href='' val='"+p.id+"' tid='"+v.id+"'>"+p.title+"</a>";
		//	}
		//});
		//if(v.projectid != null){
		//	plist += "	<div class='dropdown-divider'></div>";
		//	plist += "	<a class='dropdown-item todo_project' href='' val='' tid='"+v.id+"'>"+todo_lan.noproject+"</a>";
		//}
		//plist += "</div>";
		return plist;
	}
	var setProjectFilter = function(){
		$("#todo_filter_project").empty();
		var pname = "";
		if(todo_filter_project == "no"){
			pname = todo_lan.noproject;
		}else{
			pname = todo_filter_project ? getprojectnamebyid(todo_filter_project) : todo_lan.showall;
		}
		var plist = "<button type='button' class='btn btn-danger dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>"+pname+"</button>";
		plist += "<div class='dropdown-menu'>";
		$.each(todo_projects, function(k, p){
			if(todo_filter_project != p.id){
				plist += "	<a class='dropdown-item todo_project' href='' val='"+p.id+"'>"+p.title+"</a>";
			}
		});
		plist += "	<div class='dropdown-divider'></div>";
		if(todo_filter_project != "no"){
			plist += "	<a class='dropdown-item todo_project' href='' val='no'>"+todo_lan.noproject+"</a>";
		}
		if(todo_filter_project != ""){
			plist += "	<a class='dropdown-item todo_project' href='' val=''>"+todo_lan.showall+"</a>";
		}
		plist += "</div>";
		$("#todo_filter_project").append(plist);
		$("#todo_filter_project a").click(function(e){
			e.preventDefault();
			todo_filter_project = $(this).attr("val");
			setProjectFilter();
			getList();
			getProgress();
		});
	}
	var getProjects = function(){
		var data = "action=ajtodo_team_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&pid="+todo_filter_project;
		data += "&type=getprojects";
		ajaxCall(data, "post", 
			function(res){
				todo_projects = res.projects;
				setProjectFilter();
			}, 
			function(res){
			});
	}
	var setShortcode = function(){
		var sc = "[ajtodo uid="+ajax_todo_info.userid;
		if(todo_filter_project != "")
			sc += " project="+todo_filter_project;
		if(ajax_todo_info.status_filter != "")
			sc += " status="+ajax_todo_info.status_filter;
		if(todo_search != "")
			sc += " search="+todo_search;
		sc += "]";
		$("#ajtodo_todo_sc").html("<kbd>"+sc+"</kbd>");
	}
	var ajtodo_init = function(){
		moment.locale(ajax_todo_info.locale);
		$("#ajtodo_hello_title").text(ajax_todo_info.hello_title);
		$("#ajtodo_hello_msg").html(ajax_todo_info.hello_msg);
		$("#ajtodo_alert_close_big").click(function(){
			$(".ajtodo_hello").slideUp(1000, function(){
				$(this).remove();
			});
			var data = "action=ajtodo_team_todo_ajax";
			data += "&nonce=" + ajax_todo_info.nonce;
			data += "&type=setcookie";
			data += "&key=hidehello";
			data += "&val=Y";
			ajaxCall(data, "post", function(res){  }, function(res){ });
		});
	}

	var getTodoTypes = function(){
		todo_status_info = [];
		var data = "action=ajtodo_team_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&uid="+ajax_todo_info.uid;
		data += "&pid="+todo_filter_project;
		data += "&planid="+ajax_todo_info.planid;
		data += "&type=gettodotypes";
		ajaxCall(data, "post", 
			function(res){
				var opencount = 0;var donecount = 0;var ingcount = 0; var allcount = 0;
				todo_todotypes = res.todotypes; 
				updateTodoTypes();
			}, 
			function(res){
			});
	}

	$(document).on("click", ".seltodotype", function(e){
		todo_nowtodotypekey = $(this).attr("val");
		var nowtodotype = gettodotypeinfo(todo_nowtodotypekey)
		$("#ajtodo_set_nowtodotype").text(nowtodotype.name);
		$("#ajtodo_set_nowtodotype").css("background", nowtodotype.color);
		$("#ajtodo_set_nowtodotypelist").empty();
		$.each(todo_alltodotype, function(k, v){
			if(v.key != todo_nowtodotypekey){
				console.log(v);
				var opt = "<a class='fs12 pt8 pb8 dropdown-item seltodotype' href='#' ";
				opt += " style='background:"+v.color+"' val='"+v.key+"'>"+v.name+"</a>";
				$("#ajtodo_set_nowtodotypelist").append(opt);
			}
		});
	});

	var updateTodoTypes = function(){
		$("#ajtodo_todotypelist").empty();
		var nowtodotype = todo_type == "" ? "" : gettodotypeinfo(todo_type);
		if(todo_type == ""){
			$("#ajtodo_now_todotype").css("background", "#fff");
			$("#ajtodo_now_todotype").css("color", "#000");
			$("#ajtodo_now_todotype").attr("val", "");
			$("#ajtodo_now_todotype").text(todo_lan.todotype + " : " + todo_lan.all);
		}else{
			$("#ajtodo_now_todotype").css("background", nowtodotype.color);
			$("#ajtodo_now_todotype").css("color", "#fff");
			$("#ajtodo_now_todotype").attr("val", "");
			$("#ajtodo_now_todotype").text(todo_lan.todotype + " : " + nowtodotype.name);
		}
		$.each(todo_alltodotype, function(k, todotype){
			var todocnt = 0;
			$.each(todo_todotypes, function(k1, data){
				if(todotype.key == data.todotype)
					todocnt = data.cnt;
			});
			var btnhtml = "<a class='fs12 pt8 pb8 dropdown-item btn-sm' style='border-left:8px solid "+todotype.color+"' href='#' val='"+todotype.key+"'>";
			btnhtml += todotype.name;
			btnhtml += "<span class='badge float-right fs12'>"+todocnt+"</span></a>";			
			var btn = $(btnhtml);
			$("#ajtodo_todotypelist").append(btn);
		});
		if(todo_type != ""){
			$("#ajtodo_todotypelist").append("<div class='dropdown-divider ajtodoonlytodotypeok' style='margin:0'></div>");
			$("#ajtodo_todotypelist").append("<a class='fs12 pt8 pb8 dropdown-item btn-sm ajtodoonlytodotypeok' style='border-left:8px solid #fff' val='' href='#'>"+todo_lan.all+"</a>");
		}
	}

	$(document).on("click", "#ajtodo_todotypelist a", function(e){
		e.preventDefault();
		todo_type = $(this).attr("val");
		updateTodoTypes();
		getList();
	});

	var getCountViewByStat = function(list){
		var oCount = 0;
		var dCount = 0;
		var aCount = 0;
		if(list.length > 0){
			$.each(list, function(k, item){
				aCount += parseInt(item.cnt);
				if(item.statuskey == "O")
					oCount = parseInt(item.cnt);
				if(item.statuskey == "D")
					dCount = parseInt(item.cnt);
			});
		}
		total_todo_count += aCount;
		var perc = aCount > 0 ? (dCount * 100 / aCount) : 0;
		var ret = "<p class='card-text'>" + todo_lan.done + " / "+todo_lan.all;
		ret += "<span class='float-right'>" + dCount + " / " + aCount + "</span></p>";
		ret += "<div class='progress'>";
		ret += "<div class='progress-bar bg-warning' role='progressbar' aria-valuemin='0' aria-valuemax='100' ";
		ret += "style='width: "+perc+"%' aria-valuenow='"+perc+"'></div>";
		ret += "</div>";
		return ret;
	}
	var getplan = function(v){
		var card = "<div class='card plancard ";
		v.donedate = v.donedate != undefined ? v.donedate : "";
		v.realstartdate = v.realstartdate != undefined ? v.realstartdate : "";
		if(ajax_todo_info.planid == v.id){
			card += " text-white bg-success";
		}
		if(v.donedate){
			card += " text-light bg-dark";
		}
		card += "' donedate='"+v.donedate+"' ";
		card += "' realstartdate='"+v.realstartdate+"' ";
		if(show_doneplan == "N" && v.donedate){
			card += "' style='display:none;' ";
		}
		card += "' val='"+v.id+"'>";
		card += "<div class='card-header'>";
		if(v.realstartdate){
			card += "<i class='fas fa-play'></i> ";
		}
		card += v.plantitle;
		card += "<span class='float-right'>";
		if(v.donedate){
			card += "<i class='fas fa-chevron-left'></i>";
		}else{
			card += "<i class='fas fa-chevron-down'></i>";
		}
		card += "</span></div>";
		if(v.donedate){
			card += "<div class='card-body' style='display:none;'>";
		}else{
			card += "<div class='card-body'>";
		}
		if(v.id > 0){
			if(v.ising == "Y"){
				card += "	<p class='card-text'>"+todo_lan.ing +"</p>";
			}
			card += "	<p class='card-text'>"+todo_lan.finday +" : "+ moment(v.finishdate).fromNow()+"</p>";
			card += "	<p class='card-text'>"+v.startdate.substring(0, 10)+" ~ "+v.finishdate.substring(0, 10)+"</p>";
		}
		card += getCountViewByStat(v.cnt);
		card += "</div>";
		if(v.id != 0 && ajax_todo_info.planperms.includes("tp_plan_manage") && (ajax_todo_info.freeze == "")){
			card += "<div class='card-footer'>";
			var href = "?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&act=addplan&plankey="+v.id+"&pid="+todo_filter_project;
			if(v.ising == "Y"){
				card += "<a href='#' class='mt8 btn btn-dark btn-sm planstop' val='"+v.id+"' style='margin:0px'><i class='fas fa-pause'></i> "+todo_lan.stop+"</a>";
			}else{
				card += "<a href='#' class='mt8 btn btn-dark btn-sm planstart' val='"+v.id+"' style='margin:0px'><i class='fas fa-play'></i> "+todo_lan.start+"</a>";
			}
			card += "<a href='"+href+"' class='mt8 btn btn-dark btn-sm float-right' style='margin:0px'><i class='fas fa-cog'></i></a>";
			card += "</div>";
		}
		card += "</div>";
		return card;
	}
	$(document).on("click", ".planstop", function(e){
		e.preventDefault();
		var planid = $(this).attr("val");
		var data = "action=ajtodo_team_todo_ajax";
		data += "&nonce="+ajax_todo_info.nonce;
		data += "&type=planstop";
		data += "&planid="+planid;
		data += "&pid="+ajax_todo_info.filter_project;
		ajaxCall(data, "post", 
			function(res){
				getPlanTodoList();
			}, 
			function(msg){
				console.log(msg);
			});
	});
	$(document).on("click", ".planstart", function(e){
		e.preventDefault();
		var planid = $(this).attr("val");
		var data = "action=ajtodo_team_todo_ajax";
		data += "&nonce="+ajax_todo_info.nonce;
		data += "&type=planstart";
		data += "&planid="+planid;
		data += "&pid="+ajax_todo_info.filter_project;
		ajaxCall(data, "post", 
			function(res){
				getPlanTodoList();
			}, 
			function(msg){
				console.log(msg);
			});
	});
	var updatePlan = function(){
		$("#aj_planlist").empty();
		$.each(todo_plan_list, function(k, v){
			if(v.ising == "Y"){
				$("#aj_planlist").append(getplan(v));
			}
		});
		$.each(todo_plan_list, function(k, v){
			if(v.id == 0){
				$("#aj_planlist").append(getplan(v));
			}
		});
		$.each(todo_plan_list, function(k, v){
			if(v.id != 0 && ajtodoisEmpty(v.donedate) && v.ising == "N")
				$("#aj_planlist").append(getplan(v));
		});
		$.each(todo_plan_list, function(k, v){
			if(v.id != 0 && !ajtodoisEmpty(v.donedate) && v.ising == "N"){
				$("#aj_planlist").append(getplan(v));
			}
		});
		setSelectedPlan(ajax_todo_info.planid);
	}
	var setPlanCardEvent = function(){
		$("#aj_planlist .card-header").click(function(){
			if($(this).find("i").hasClass("fa-chevron-left")){
				$(this).next().show();
				$(this).find("i").removeClass("fa-chevron-left").addClass("fa-chevron-down");
			}else{
				$(this).next().hide();
				$(this).find("i").removeClass("fa-chevron-down").addClass("fa-chevron-left");
			}
		});
		$("#aj_planlist .card-body").click(function(){
			$.each($("#aj_planlist .card.plancard"), function(k, v){
				$(v).removeClass("text-white").removeClass("bg-success");
			});
			var planid = $(this).parent().attr("val");
			$(this).parent().addClass("text-white").addClass("bg-success");
			ajax_todo_info.planid = planid;
			setProjectEmpty(false);
			setSelectedPlan(planid);
			getList();
		});
		$("#aj_planlist .card.plancard")
			.on("dragover", onDragOver)
			.on("dragleave", onDragLeave)
			.on("drop", onDrop)
			.on("dragenter", onDragEnter);
	}
	$(document).on("click", "#tglplan", function(e){
		show_doneplan = (show_doneplan == "N" ? "Y" : "N");
		if(show_doneplan == "Y"){
			if(ajax_todo_info.planperms.includes("tp_plan_create")){
				$(this).find("i").removeClass("fa-eye-slash").addClass("fa-eye");
			}else{
				$(this).html("<i class='far fa-eye-slash'></i> " + todo_lan.show_all_plan);
			}
			$.each($("#aj_planlist .bg-dark"),function(k, v){
				$(v).show();
			});
		}else{
			if(ajax_todo_info.planperms.includes("tp_plan_create")){
				$(this).find("i").removeClass("fa-eye").addClass("fa-eye-slash");
			}else{
				$(this).html("<i class='far fa-eye-slash'></i> " + todo_lan.show_ing_plan);
			}
			$.each($("#aj_planlist .bg-dark"),function(k, v){
				$(v).hide();
			});
		}
	});

	var setSelectedPlan = function(planid){
		$.each(todo_plan_list, function(k, v){
			if(v.id == planid)
				selectedplan = v;
		});
	}
	var setPlanSelector = function(){
		$("#todo_filter_plan").empty();
		var nowplanname = "";
		if(ajax_todo_info.planid == ""){
			nowplanname = todo_lan.all;
		}else{
			setSelectedPlan(ajax_todo_info.planid);
			nowplanname = selectedplan.plantitle;
		}
		var plist = "<button type='button' class='btn btn-info dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>"+nowplanname+"</button>";
		plist += "<div class='dropdown-menu'>";
		$.each(todo_plan_list, function(k, v){
			console.log(v);
			if(v.donedate != null)
				return;

			if(v.id != ajax_todo_info.planid){
				plist += "	<a class='fs12 pt8 pb8 dropdown-item todo_plan' href='#' val='"+v.id+"'>"+v.plantitle+"</a>";
			}
		});
		if(ajax_todo_info.planid != ""){
			plist += "	<div class='dropdown-divider'></div>";
			plist += "	<a class='fs12 pt8 pb8 dropdown-item todo_plan' href='#' val=''>"+todo_lan.all+"</a>";
		}
		plist += "</div>";
		$("#todo_filter_plan").append(plist);
		$("#todo_filter_plan a.todo_plan").click(function(e){
			e.preventDefault();
			ajax_todo_info.planid = $(this).attr("val");
			//setSelectedPlan(ajax_todo_info.planid);
			getTodoTypes();
			getProgress();
			setPlanSelector();
			getList();
		});
	}
	var getPlanTodoList = function(){
		//setLoading();
		total_todo_count = 0;
		var data = "action=ajtodo_team_todo_ajax";
		data += "&nonce="+ajax_todo_info.nonce;
		data += "&type=getplanlist";
		data += "&pid="+ajax_todo_info.filter_project;
		ajaxCall(data, "post", 
			function(res){
				todo_plan_list = res.planlist;
				if(ajax_todo_info.planview == "Y"){
					$("#aj_todolist").empty();
					updatePlan();
					setPlanCardEvent();
					setProjectEmpty(total_todo_count == 0);
				}else{
					setPlanSelector();
				}
			}, 
			function(msg){
				console.log(msg);
			});
	};
	var onDragLeave = function(event) {
		event.stopPropagation();
		event.preventDefault();
		$(event.currentTarget).removeClass("dropover");
	}
	var onDragStart = function(event) {
		event.stopPropagation();
		event.preventDefault();
	}
	var onDragOver = function(event) {
		event.stopPropagation();
		event.preventDefault();
		$(event.currentTarget).addClass("dropover");
		event.originalEvent.dataTransfer.dropEffect = 'move';
	}
	var onDragEnter = function(event) {
		event.preventDefault();
		var attr = $(event.currentTarget).attr("val");
		if (typeof attr !== typeof undefined && attr !== false) {
			to_planid = attr;
		}
	}
	var onDrop = function(event, el) {
		if(!$(event.currentTarget).hasClass("bg-dark")){
			if($.isNumeric(nowtodoid) && $.isNumeric(to_planid) && (nowtodoplanid != to_planid)){
				updateCols({ "planid" : to_planid }, nowtodoid, changePlan, updateTodoErr);
			}
		}
	}
	var changePlan = function(v){
		getPlanTodoList();
		$("#todo_list li[var='"+nowtodoid+"']").remove();
	}
	$("#addplan").click(function(){
		var url = "?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&act=addplan&pid="+ajax_todo_info.filter_project;
		window.location = url;
	});

	var ajtodo_setTextArea = function(todo, kind, valid, onlyshow){
		var ret = "";
		var val = "";
		var nomsg = "";
		switch(kind){
			case "comment" : val = todo.comment; nomsg = todo_lan.nocontent; break;
		}
		if(onlyshow){
			ret = val == "" ? todo_lan.nocontent : val;
		}else{
			ret = "<div class='fs14 ajtodo_settext_show'>" + (val != "" ? val.replace(/\n/g, "<br />") : nomsg) + "</div>";
			ret += "<textarea style='display:none;height:300px;' class='form-control ajtodo_settext_edit_box' ";
			ret += (valid ? " nvalid='Y' " : "");
			ret += " col='"+kind+"' tid='"+todo.id+"'>"+val+"</textarea>";
		}
		return ret;
	}

	var ajtodo_setText = function(todo, kind, valid, onlyshow){
		var ret = "";
		var val = "";
		var nomsg = "";
		switch(kind){
			case "title" : val = todo.title; nomsg = ""; break;
		}
		if(onlyshow){
			ret = val;
		}else{
			ret = "<div class='ajtodo_settext_show'>" + val + "</div>";
			ret += "<input type='text' style='display:none;' class='form-control ajtodo_settext_edit_box' ";
			ret += (valid ? " nvalid='Y' " : "");
			ret += " col='"+kind+"' tid='"+todo.id+"' value='"+val+"'>";
		}
		return ret;
	}

	$(document).on("click", ".todo_singleview div.ajtodo_settext_show", function(e){
		e.preventDefault();
		$(this).hide();
		$(this).next().val(($(this).text() == todo_lan.nocontent ? "" : $(this).text()));
		$(this).next().show();
		$(this).next().focus();
	});

	var updateTextBoxData = function(th, e){
		th.hide();
		th.prev().show();
		var preData = th.prev().text();
		preData = preData == todo_lan.nocontent ? "" : preData;
		if(preData != th.val().replace(/\n/g, "")){
			console.log("up");
			var col = th.attr("col");
			var tid = th.attr("tid");
			if(th.is("input")){
				th.prev().text(th.val());
			}else{
				var newta = th.val() == "" ? todo_lan.nocontent : th.val().replace(/\n/g, "<br />");
				th.prev().html(newta);
			}
			updateCol(col, th.val(), tid, refreshOnlyBase, updateTodoErr);
		}
	}

	$(document).on("focusout", ".todo_singleview input.ajtodo_settext_edit_box, .todo_singleview textarea.ajtodo_settext_edit_box", function(e){
		e.preventDefault();
		var valid = $(this).attr("nvalid");
		if(valid == "Y" && $(this).val().trim() == ""){
			$(this).hide();
			$(this).prev().show();
			$(this).val($(this).prev().val());
			return;
		}
		updateTextBoxData($(this), e);
	});

	var refreshOnlyBase = function(res){
		getSingleTodo(res.todoid, "up",
			function(response){
				var newli = gettodoitem(response.data);
				console.log(newli);
				$("li.todo_list_item[var="+response.data.id+"]").replaceWith(newli);
			},
			function(msg){
				console.log(msg);
			});
	}

	$(document).on("click", ".todo_singleview a.categorysel", function(e){
		e.preventDefault();
		var categorykeyval = $(this).attr("val");
		var tid = $(this).attr("tid");
		updateCol("categorykey", categorykeyval, tid, refreshDetailViewCategory, updateTodoErr);
	});

	var setCategory = function(todo, onlyshow){
		var ret = "";
		var type = "";
		$.each(todo_category, function(k, v){
			if(v.key == todo.categorykey)
				type = v;
		});
		if(onlyshow || todo_category.length == 0){
			ret += "<button class='btn btn-sm disable ajtodo_usersmallcard' data-toggle='tooltip' data-placement='top' title='"+type.name+"' style='padding:0px;height: 30px;' type='button' >";
			if(type != ""){
				ret += type.name;
			}else{
				ret += todo_lan.nocategorykey;
			}
			ret += "</button>";
		}else{
			ret = "<div class='btn-group'>";
			ret += "<button class='btn btn-sm dropdown-toggle ajtodo_usersmallcard' ";
			ret += " style='padding:0px;height: 30px;' ";
			//ret += " data-toggle='tooltip' data-placement='top' title='"+type.name+"' ";
			ret += " type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>";
			if(type != ""){
				ret += type.name;
			}else{
				ret += todo_lan.nocategorykey;
			}
			ret += "</button>";
			ret += "<div class='dropdown-menu'>";
			$.each(todo_category, function(k, v){
				if(v.key != todo.categorykey){
					ret += "<a class='dropdown-item categorysel' href='#' val='"+v.key+"' tid='"+todo.id+"'>";
					ret += v.name;
					ret += "</a>";
				}
			});
			if(type != ""){
				ret += "<div class='dropdown-divider'></div>";
				ret += "<a class='dropdown-item categorysel' href='#' val='' tid='"+todo.id+"'>"+todo_lan.nocategorykey+"</a>";
			}
			ret += "</div>";
			ret += "</div>";
		}
		return ret;
	}

	var setTodoType = function(todo, onlyshow){
		var ret = "";
		var type = "";
		$.each(todo_alltodotype, function(k, v){
			if(v.key == todo.todotype)
				type = v;
		});
		console.log(type);
		if(onlyshow){
			ret += "<button class='btn btn-sm disable ajtodo_usersmallcard' style='padding:0px;height: 30px;' type='button' >";
			if(type != ""){
				ret += type.name;
			}else{
				ret += todo_lan.nocategorykey;
			}
			ret += "</button>";
		}else{
			ret = "<div class='btn-group'>";
			ret += "<button class='btn btn-sm dropdown-toggle ajtodo_usersmallcard' ";
			ret += " style='padding:0px;height: 30px;' ";
			ret += " type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>";
			if(type != ""){
				ret += type.name;
			}else{
				ret += todo_lan.nocategorykey;
			}
			ret += "</button>";
			ret += "<div class='dropdown-menu'>";
			$.each(todo_alltodotype, function(k, v){
				if(v.key != todo.todotype){
					ret += "<a class='dropdown-item todotypesel' href='#' val='"+v.key+"' tid='"+todo.id+"'>";
					ret += v.name;
					ret += "</a>";
				}
			});
			//if(type != ""){
			//	ret += "<div class='dropdown-divider'></div>";
			//	ret += "<a class='dropdown-item todotypesel' href='#' val='' tid='"+todo.id+"'>"+todo_lan.nocategorykey+"</a>";
			//}
			ret += "</div>";
			ret += "</div>";
		}
		return ret;
	}

	$(document).on("click", ".todo_singleview a.todotypesel", function(e){
		e.preventDefault();
		var todotypekeyval = $(this).attr("val");
		var tid = $(this).attr("tid");
		updateCol("todotype", todotypekeyval, tid, refreshDetailViewTodoType, updateTodoErr);
	});

	var setAssignee = function(tid, uid, onlyshow, onlyavatar){
		var ret = "";
		var assginee = "";
		$.each(todo_members, function(k, v){
			if(v.id == uid)
				assginee = v;
		});
		if(onlyshow){
			if(onlyavatar){
				ret += assginee.avatar.replace("<img ", "<img class='rounded-circle' title='"+assginee.name+"' ");
			}else{
				ret += "<button class='btn btn-sm disable ajtodo_usersmallcard' style='padding:0px' type='button' >";
				if(assginee != ""){
					ret += assginee.avatar.replace("<img ", "<img class='rounded-circle'") + " " + assginee.name;
				}else{
					ret += todo_lan.noassignee;
				}
				ret += "</button>";
			}
		}else{
			ret = "<div class='btn-group'>";
			ret += "<button class='btn btn-sm dropdown-toggle ajtodo_usersmallcard' ";
			if(assginee != ""){
				ret += " style='padding:0px' ";
			}else{
				ret += " style='padding:0px;height: 30px;' ";
			}
			ret += " type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>";
			if(assginee != ""){
				ret += onlyavatar ? 
					assginee.avatar.replace("<img ", "<img class='rounded-circle'") : 
					(assginee.avatar.replace("<img ", "<img class='rounded-circle'") + " " + assginee.name);
			}else{
				ret += todo_lan.noassignee;
			}
			ret += "</button>";
			ret += "<div class='dropdown-menu'>";
			$.each(todo_members, function(k, v){
				if(v.id != uid){
					ret += "<a class='dropdown-item usel' href='#' val='"+v.id+"' tid='"+tid+"'>";
					ret += v.avatar.replace("<img ", "<img class='rounded-circle' ") + " ";

					ret += v.name;
					ret += "</a>";
				}
			});
			if(assginee != ""){
				ret += "<div class='dropdown-divider'></div>";
				ret += "<a class='dropdown-item usel' href='#' val='' tid='"+tid+"'>"+todo_lan.noassignee+"</a>";
			}
			ret += "</div>";
			ret += "</div>";
		}
		return ret;
	}

	var refreshDetailViewAssignee = function(res){
		getSingleTodo(res.todoid, "up",
			function(response){
				var newli = gettodoitem(response.data);
				$("li.todo_list_item[var="+response.data.id+"]").replaceWith(newli);
				$("#ajtodo_sv_assignee").html(setAssignee(response.data.id, response.data.assignid, false, false));
			},
			function(msg){
				console.log(msg);
			});
	}

	var refreshDetailViewCategory = function(res){
		getSingleTodo(res.todoid, "up",
			function(response){
				var newli = gettodoitem(response.data);
				$("li.todo_list_item[var="+response.data.id+"]").replaceWith(newli);
				$("#ajtodo_sv_category").html(setCategory(response.data, false));
			},
			function(msg){
				console.log(msg);
			});
	}

	var refreshDetailViewTodoType = function(res){
		getSingleTodo(res.todoid, "up",
			function(response){
				var newli = gettodoitem(response.data);
				$("li.todo_list_item[var="+response.data.id+"]").replaceWith(newli);
				$("#ajtodo_sv_todotype").html(setTodoType(response.data, false));
			},
			function(msg){
				console.log(msg);
			});
	}

	$(document).on("click", ".todo_singleview a.usel", function(e){
		e.preventDefault();
		var assignid = $(this).attr("val");
		var tid = $(this).attr("tid");
		updateCol("assignid", assignid, tid, refreshDetailViewAssignee, updateTodoErr);
	});

	getPlanTodoList();

	statusfilterinit();
	getTodoTypes();
	getProgress();
	if(ajax_todo_info.filter_project == "")
		getProjects();

	//if(ajax_todo_info.planview == "N")
	//	ajax_todo_info.planid = "";

	getList();
	ajtodo_init();

	console.log(todo_members);
	//$.notify({ message: "dfsdafds" },{ type: "success" });
});
