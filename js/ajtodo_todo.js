jQuery(document).ready(function($){
	var todo_status_changed = false;
	var todo_search = "";
	var todo_filter_project = ajax_todo_info.filter_project;
	var todo_option = $(todo_option_html);
	var todo_status_s = "";
	var todo_status_d = "";
	var todo_status_when_s = [];
	var todo_status_when_d = [];
	var todo_status_when_i = [];
	var todo_projects = [];
	var todo_list = [];
	$.notifyDefaults({ delay : ajax_todo_info.noti_delay });
	$("#sjtodo_search").keydown(function(key){
		if(key.keyCode == 13){
			todo_search = $(this).val().trim();
			getList();
		}
	});
	$("#btnQuickTodo").click(function(key){
		if($("#ajtodo_title").val().length > 0){
			var data = "action=ajtodo_todo_ajax";
			data += "&nonce=" + ajax_todo_info.nonce;
			data += "&type=createtodo";
			data += "&project_filter=" + $("#ajtodo_projectid").val();
			data += "&title=" + $("#ajtodo_title").val();
			ajaxCall(data, "post", 
				function(res){
					$("#ajtodo_title").val("");
					console.log(1);
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
			var data = "action=ajtodo_todo_ajax";
			data += "&nonce=" + ajax_todo_info.nonce;
			data += "&type=createtodo";
			data += "&project_filter=" + todo_filter_project;
			data += "&title=" + $(this).val();
			ajaxCall(data, "post", 
				function(res){
					$("#ajtodo_todotitle").val("");
					getProgress();
					getSingleTodo(res.todoid, 
						function(response){
							if((todo_filter_project != "no" && todo_filter_project != "" && response.data.projectid != todo_filter_project) ||
								(todo_filter_project == "no" && response.data.projectid != undefined)){
								return;
							}
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
	var ajaxCall = function(data, type, success, error){
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
					success(res);
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
		var data = "action=ajtodo_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&type=gettodolist";
		data += "&uid="+ajax_todo_info.uid;
		data += "&filter_project="+todo_filter_project;
		data += "&filter_user="+ajax_todo_info.user_filter;
		data += "&filter_status="+ajax_todo_info.status_filter;
		data += "&search="+todo_search;
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
	var viewDetail = function(todo){
		$("#todoview").css("width","50%");
	}
	var getSingleTodo = function(todo_id, suc, err){
		var data = "action=ajtodo_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&type=getsingletodo";
		data += "&todo_id="+todo_id;
		ajaxCall(data, "post", 
			function(res){
				suc(res);
			}, 
			function(res){
				console.log(msg);
			});
	}
	var updateTodoSuc = function(v){
		getSingleTodo(v.todoid, 
			function(response){
				getProgress();
				var newli = gettodoitem(response.data);
				$("li.todo_list_item[var="+v.todoid+"]").replaceWith(newli);
				var ishide = false;
				if(ajax_todo_info.auto_done_hidden == "Y"){
					if((ajax_todo_info.status_filter == "O" && response.data.donedated != null) || 
						(ajax_todo_info.status_filter == "D" && response.data.donedated == null) ||
						(todo_filter_project != "no" && todo_filter_project != "" && response.data.projectid != todo_filter_project) ||
						(todo_filter_project == "no" && response.data.projectid != undefined)
						){
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
	var getactionstatuscss = function(statustype){
		switch(statustype){
			case "S" : return "primary";
			case "I" : return "warning";
			case "D" : return "success";
		}
	}
	var updateCols = function(colval, tid, suc, err){
		var data = "action=ajtodo_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&type=updatetodo";
		data += "&colval="+JSON.stringify(colval);
		data += "&todo_id="+tid;
		ajaxCall(data, "post", updateTodoSuc, updateTodoErr);
	}
	var updateCol = function(col, val, tid, suc, err){
		var data = "action=ajtodo_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&type=updatetodo";
		data += "&col="+col;
		data += "&val="+val;
		data += "&todo_id="+tid;
		ajaxCall(data, "post", updateTodoSuc, updateTodoErr);
	}
	var showDate = function(dt){
		if(moment(dt) > moment().add(-3, 'days')){
			return moment(dt).fromNow();
		}else{
			return moment(dt).format("YYYY-MM-DD");
		}
	}
	var gettodoitem = function(v){
		var todo;
		if(ajax_todo_info.freeze == ""){
			todo = $("<li class='todo_list_item list-group-item-action list-group-item' status='"+v.status_type+"' var='"+v.id+"'></li>");
		}else{
			todo = $("<li class='todo_list_item list-group-item-action list-group-item'></li>");
		}
		if(v.status_type == "S"){
			todo.append("<i class='far fa-square'></i>");
		}else if(v.status_type == "I"){
			todo.append("<i class='far fa-caret-square-right'></i>");
		}else{
			todo.append("<i class='far fa-check-square'></i>");
		}
		if(ajax_todo_info.freeze == ""){
			todo.append("<a href='#' class='todo_title "+(v.donedated != null ? "done" : "")+"'>"+v.title+"</a>");
		}else{
			todo.append("<span class='todo_title'>"+v.title+"</span>");
		}
		if(v.status_type == "D"){
			todo.append("<kbd class='clearfix float-right'>"+showDate(v.donedated)+"</kbd>");
		}
		if(ajax_todo_info.freeze == ""){
			todo.find("i").click(function(e){
				todo_status_changed = true;
				$("li.todo_list_item[var="+v.id+"]").css("background", "#efefef");
				if($(this).hasClass("fa-square")){ //open
					if(ajax_todo_info.direct_done == "Y"){
						setTodo(v.id, todo_status_d, updateTodoSuc, updateTodoErr);
					}else{
						updateCol("statustypeid", ajax_todo_info.inprogress_status_id, v.id, 
								updateTodoSuc, updateTodoErr);
					}
				}
				if($(this).hasClass("fa-caret-square-right")){ //in progress
					setTodo(v.id, todo_status_d, updateTodoSuc, updateTodoErr);
				}else if($(this).hasClass("fa-check-square")){ //done
					updateCols({ "statustypeid" : ajax_todo_info.done_click , 
								 "donedated" : ""}, 
							v.id, 
							updateTodoSuc, updateTodoErr);
					//updateCol("statustypeid|donedated", ajax_todo_info.done_click+"|", v.id, updateTodoSuc, updateTodoErr);
				}
			});
			todo.find("a.todo_tile").click(function(e){
				e.preventDefault();
				viewDetail($(this).parent().attr("var"));
			});
			todo.mouseenter(function(){
				$(this).find('kbd').hide();
				$(todo_option).attr("val", v.id);

				$(todo_option).find(".ajtodo_del").removeClass("ajtodo_del_now").text(todo_lan.del);
				if(todo_projects.length > 0){
					$(todo_option).find("#todo_projects").empty();
					$(todo_option).find("#todo_projects").append(setProjectList(v));
				}

				$(todo_option).find("#todo_status").empty();
				if(v.status_type == "S"){
					$.each(todo_status_when_s, function (k, av){
						var css = getactionstatuscss(av.statustype);
						var btn = "<button type='button' class='btn btn-"+css+" btn_status' val='"+av.id+"'>"+av.title+"</button>";
						$(todo_option).find("#todo_status").append(btn);
					});
				}else if(v.status_type == "I"){
					$.each(todo_status_when_i, function (k, av){
						if(v.statustypeid != av.id){
							var css = getactionstatuscss(av.statustype);
							var btn = "<button type='button' class='btn btn-"+css+" btn_status' val='"+av.id+"'>"+av.title+"</button>";
							$(todo_option).find("#todo_status").append(btn);
						}
					});
				}else if(v.status_type == "D"){
					$.each(todo_status_when_d, function (k, av){
						var css = getactionstatuscss(av.statustype);
						var btn = "<button type='button' class='btn btn-"+css+" btn_status' val='"+av.id+"'>"+av.title+"</button>";
						$(todo_option).find("#todo_status").append(btn);
					});
				}
				$(this).append($(todo_option));
				$(todo_option).find(".btn").click(function(e){
					if($(this).hasClass("ajtodo_del")){
						if($(this).hasClass("ajtodo_del_now")){
							delTodo($(todo_option).attr("val"));
						}else{
							delTodoCheck($(this), $(todo_option).attr("val"));
						}
					}
					if($(this).hasClass("btn_status")){
						todo_status_changed = true;
						setTodo($(todo_option).attr("val"), $(this).attr("val"), updateTodoSuc, updateTodoErr);
					}
				});

				//$(todo_option).find("a.todo_project").click(function(e){
				//	e.preventDefault();
				//	var tid = $(this).attr("tid");
				//	var pid = $(this).attr("val");
				//	todo_status_changed = false;
				//	updateCol("projectid", pid, tid, updateTodoSuc, updateTodoErr);
				//});
			});
			todo.mouseleave(function(){
				$(this).find('kbd').show();
				$(this).find('.todo_option_inline').remove();
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
		var data = "action=ajtodo_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&type=deltodo";
		data += "&todo_id="+$(todo_option).attr("val");
		ajaxCall(data, "post", 
			function(response){
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
		var data = "action=ajtodo_todo_ajax";
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
	var filterInit = function(){
		$.each( $("#ajtodo_todolist_user_filter button"), function(v){
			$(this).removeClass("btn-primary");
			if(!$(this).hasClass("btn-primary"))
				$(this).addClass("btn-secondary");
		});
		$.each( $("#ajtodo_todolist_status_filter button"), function(v){
			$(this).removeClass("btn-primary").addClass("btn-secondary");
			if(!$(this).hasClass("btn-primary"))
				$(this).addClass("btn-secondary");
		});
		$("#ajtodo_todolist_user_filter button[val='"+ajax_todo_info.user_filter+"']")
			.removeClass("btn-secondary")
			.addClass("btn-primary");
		$("#ajtodo_todolist_status_filter button[val='"+ajax_todo_info.status_filter+"']")
			.removeClass("btn-secondary")
			.addClass("btn-primary");
	}
	$("#ajtodo_todolist_user_filter button").click(function(e){
		e.preventDefault();
		ajax_todo_info.user_filter = $(this).attr("val");
		filterInit();
		getList();
	});
	$("#ajtodo_todolist_status_filter button").click(function(e){
		e.preventDefault();
		ajax_todo_info.status_filter = $(this).attr("val");
		filterInit();
		getList();
	});
	var getProgress = function(){
		var data = "action=ajtodo_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
		data += "&uid="+ajax_todo_info.uid;
		data += "&filter_project="+todo_filter_project;
		data += "&planid="+ajax_todo_info.planid;
		data += "&type=getprogress";
		ajaxCall(data, "post", 
			function(res){
				if(res.totalcount > 0){
					var s = res.opencount * 100 / res.totalcount;
					var d = res.donecount * 100 / res.totalcount;
					var i = 100 - s - d;
					$("#ajtodo_userprogress .bg-primary").css("width", s+"%");
					$("#ajtodo_userprogress .bg-warning").css("width", i+"%");
					$("#ajtodo_userprogress .bg-success").css("width", d+"%");

					$(".todo_filter_inline .ajtodo_cnt_d").text(res.donecount);
					$(".todo_filter_inline .ajtodo_cnt_s").text(res.opencount);
					$(".todo_filter_inline .ajtodo_cnt_a").text(res.totalcount);
				}
			}, 
			function(res){
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
		var data = "action=ajtodo_todo_ajax";
		data += "&nonce=" + ajax_todo_info.nonce;
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
			var data = "action=ajtodo_todo_ajax";
			data += "&nonce=" + ajax_todo_info.nonce;
			data += "&type=setcookie";
			data += "&key=hidehello";
			data += "&val=Y";
			ajaxCall(data, "post", function(res){  }, function(res){ });
		});
	}
	filterInit();
	getProgress();
	getProjects();
	getList();
	ajtodo_init();
	//$.notify({ message: "dfsdafds" },{ type: "success" });
});
