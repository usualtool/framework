"use strict";
//页面定位
function goto(id){
	$("#"+id+"")[0].scrollIntoView();
}
//批量操作切换器
function changeaction(id){
	if($("#do").val()=="move"){
		$("#"+id+"").css("display","");
	}else{
		$("#"+id+"").css("display","none");
	}
}
//删除指定节点
function delitem(n){
	var obj = document.getElementById(n);
	obj.parentNode.removeChild(obj);
}
//UT后端搜索
if($(".type-ahead__input").length>0){
	const suggestions = document.querySelector('.type-ahead__suggestions')
	const input = document.querySelector('.type-ahead__input');
	suggestions.addEventListener('click',(e) =>{
		if(e.target.classList.contains('match')){
			input.value = e.target.parentNode.innerText;
		} else {
			input.value = e.target.innerText;
		}
		suggestions.classList.add('hidden');
	})
	input.addEventListener('keyup', (e) => {
		if(e.code === 'Enter') 	return suggestions.classList.add('hidden');
		const text = event.target.value;
		if(!text) {
			return suggestions.classList.add('hidden')
		} else {
			suggestions.classList.remove('hidden');
			var words='<li class="suggestion">搜索结果:</li>';
			$.ajax({
				url: "?p=search",
				type: 'post',
				data:{'key':text},
				dataType: 'json',
				success: function(data){
					for(var i=0;i<data.length;i++){
						words+='<li class="suggestion"><a href="?m='+data[i].mid+'&p='+data[i].modurl+'" onclick=clicknav("'+data[i].mid+'")>'+data[i].modname+' : '+data[i].mid+'</a></li>';
					};
					suggestions.innerHTML = words;
				}
			});
		};
	});	
}
function highlightmatch(sentence, targetText) {
	const regex = new RegExp(targetText, 'gi');
	return sentence.replace(regex, `<span class="match">${targetText}</span>`)
};
//选中函数
function checkbox(form){
    for (var i = 0; i < form.elements.length; i++) {
        var e = form.elements[i];
        if (e.name != 'checkall' && e.disabled != true) e.checked = form.checkall.checked;
    }
}
//风险提示
function checkform(){
	if(confirm("该操作具有一定风险,是否继续?")==true){ 
	  return true;
	}else{
	  return false;
	}
};
//UT-Navclick
function clicknav(module){
	setcookie("Nav",module);
};
//UT-Table Plugin
if($("#ut-table").length>0){
	$(document).ready(function() {  
				$('#ut-table').DataTable({  
					dom: 'Bfrtip',
					processing:true,
					searching:true, 
					ordering:false,
					paging:false,
					info:false,
					lengthchange:false,
					pagination:false,
					serverside:false,
					deferrender:true,
					sorting:[[0, 'sorting']],
					columnDefs:[{
					    targets : 0,
					    "orderable" : false
					}],
					"order":[[1,'asc']],
					language: {
						"paginate":{"next":"上页","previous":"下页"},
						"sProcessing":"载入中...",
						"sLengthMenu" : "显示 _MENU_ 条",  
						"sSearch": "<i class='fa fa-search'></i> ",
						"sZeroRecords": "无数据",
						"sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_ 条记录",
						"sInfoFiltered": "共 _MAX_ 条记录）", 
						"sInfoEmpty": "当前显示0到0条，共0条记录",
					},
				   "buttons": [  
						   {'extend':'copyHtml5','text':'复制记录','exportOptions':{'modifier': {'page': 'current'}}},
						   {'extend':'excelHtml5','text':'导出Excel','title':getrand(),'exportOptions':{'modifier':{'page': 'all'}}},
						   {'extend':'csvHtml5','text':'导出Csv','title':getrand(),'exportOptions':{'modifier':{'page': 'all'}}},
						   {'extend':'print','text': '打印','exportOptions': {'modifier': {'page': 'all'}}}
					   ]
				});  
			});
};
function getrand(){
	var outTradeNo="";
	for(var i=0;i<2;i++){
		outTradeNo += Math.floor(Math.random()*4);
	}
	outTradeNo = new Date().getTime() + outTradeNo; 
	return outTradeNo;
};
//单传
function upload(fileid,inputid,folder='',posturl=''){
	var fileid;
	var inputid;
	var folder;
	var posturl;
	var datas;
	var formData = new FormData();
	formData.append("file",document.getElementById(""+fileid).files[0]);
	formData.append("l",folder);
	$.ajax({
		url: posturl+'/?m=ut-frame&p=upload' ,  
		type: 'POST',  
		data: formData,  
		async: false,  
		cache: false,  
		contentType: false,  
		processData: false,  
		success: function(data){  
			var datas = eval("("+data+")");
			document.getElementById(""+inputid).value=""+datas.pic;
		},  
		error: function (data){  
			document.getElementById(""+inputid).value="Upload Error!";
		}
	})  
};
//多传
function uploads(number,folder,posturl,inputtype='radio',inputfield='indexpic'){
    var uploader = new plupload.Uploader({ 
        runtimes: 'html5,flash,silverlight,html4', 
        browse_button: 'btn', 		
        url: posturl+"/?p=upload", 	
		multipart_params : {
        "l" : folder
		},
        filters:{		
            mime_types:[{						
                title: "files",				
                extensions: "jpg,png,gif"					
            }]				
        },			
        multi_selection: false,				
        init: {
            FilesAdded: function(up,files){ 
                var picnum=uploader.files.length;
                if(picnum>parseInt(number)){				
                    alert("最多可上传"+parseInt(number)+"张图片!");
                }else{							
                    var li = '';						
                    plupload.each(files,function(file){								
                    li += "<li id='" + file['id'] + "'><div class='progress'><span class='bar'></span><span class='percent'>0%</span></div></li>";		
                    });							
                    $("#ul_pics").append(li);
                    uploader.start();											
                }
            },
            UploadProgress: function(up,file){
                var percent = file.percent;  
                $("#" + file.id).find('.bar').css({"width": percent + "%"});  
                $("#" + file.id).find(".percent").text("上传中 "+percent + "%");  
            },  
            FileUploaded: function(up, file, info){  
                var data = eval("("+info.response+")");
				if(inputtype=="radio"){
                    $("#" + file.id).html("<img src='"+ data.pic + "' appurl='"+ data.post + "'><i onclick='delimg(this)'>-</i><br><input type='radio' name='"+inputfield+"' value='"+ data.pic +"' checked>Selected");
				}else if(inputtype=="checkbox"){
                    $("#" + file.id).html("<img src='"+ data.pic + "' appurl='"+ data.post + "'><i onclick='delimg(this)'>-</i><br><input type='checkbox' name='"+inputfield+"[]' value='"+ data.pic +"' checked>Selected");
				} 
            },  
            Error: function(up,err){
                alert("上传错误!");  
            }  
        }  
    });
    uploader.init();
};
function delimg(o,url=""){
  var src = $(o).prev().attr("src");
  var url = $(o).prev().attr("appurl");
  var posturl;
  if(typeof url == "undefined" || url == null || url == ""){
	  posturl="?m=ut-frame&p=upload&do=del&img="+src;
  }else{
	  posturl=url+"/?m=ut-frame&p=upload&do=del&img="+src;
  }
  $.post(posturl,function(data){
	var datas = eval("("+data+")");
    if(datas.error==0){ 
        $(o).parent().remove(); 
		alert("删除图片成功!");
    }else{
	    alert("删除图片失败!");
	}
  })
};
$(".nav-search .input-group > input").focus(function(e){
	$(this).parent().addClass("focus");
}).blur(function(e){
	$(this).parent().removeClass("focus");
});
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();
	layoutsColors();
});
function layoutsColors(){
	if($('.sidebar').is('[data-background-color]')) { 
		$('html').addClass('sidebar-color');
	} else {
		$('html').removeClass('sidebar-color');
	}
	if($('.sidebar').is('[data-image]')) {
		$('.sidebar').append("<div class='sidebar-background'></div>");
		$('.sidebar-background').css('background-image', 'url("' + $('.sidebar').attr('data-image') + '")');
	} else {
		$(this).remove('.sidebar-background');
		$('.sidebar-background').css('background-image', '');
	}
}
function legendClickCallback(event) {
	event = event || window.event;
	var target = event.target || event.srcElement;
	while (target.nodeName !== 'LI') {
		target = target.parentElement;
	}
	var parent = target.parentElement;
	var chartId = parseInt(parent.classList[0].split("-")[0], 10);
	var chart = Chart.instances[chartId];
	var index = Array.prototype.slice.call(parent.children).indexOf(target);

	chart.legend.options.onClick.call(chart, event, chart.legend.legendItems[index]);
	if (chart.isDatasetVisible(index)) {
		target.classList.remove('hidden');
	} else {
		target.classList.add('hidden');
	}
}
if($(".scroll-bar").length>0){
	$(document).ready(function(){
		$('.btn-refresh-card').on('click', function(){var e=$(this).parents(".card");e.length&&(e.addClass("is-loading"),setTimeout(function(){e.removeClass("is-loading")},3e3))})
		var scrollbarDashboard = $('.sidebar .scrollbar-inner');
		if (scrollbarDashboard.length > 0) {
			scrollbarDashboard.scrollbar();
		}
		var messagesScrollbar = $('.messages-scroll.scrollbar-outer');
		if (messagesScrollbar.length > 0) {
			messagesScrollbar.scrollbar();
		}
		var tasksScrollbar = $('.tasks-scroll.scrollbar-outer');
		if (tasksScrollbar.length > 0) {
			tasksScrollbar.scrollbar();
		}
		var quickScrollbar = $('.quick-scroll');
		if (quickScrollbar.length > 0) {
			quickScrollbar.scrollbar();
		}
		var messageNotifScrollbar = $('.message-notif-scroll');
		if (messageNotifScrollbar.length > 0) {
			messageNotifScrollbar.scrollbar();
		}
		var notifScrollbar = $('.notif-scroll');
		if (notifScrollbar.length > 0) {
			notifScrollbar.scrollbar();
		}
		$('.scroll-bar').draggable();
		var toggle_sidebar = false,
		toggle_topbar = false,
		minimize_sidebar = false,
		toggle_page_sidebar = false,
		nav_open = 0,
		topbar_open = 0,
		mini_sidebar = 0,
		page_sidebar_open =0;
		if(!toggle_sidebar) {
			var toggle = $('.sidenav-toggler');
	
			toggle.on('click', function(){
				if (nav_open == 1){
					$('html').removeClass('nav_open');
					toggle.removeClass('toggled');
					nav_open = 0;
				}  else {
					$('html').addClass('nav_open');
					toggle.addClass('toggled');
					nav_open = 1;
				}
			});
			toggle_sidebar = true;
		}
		if(!toggle_topbar) {
			var topbar = $('.topbar-toggler');
	
			topbar.on('click', function() {
				if (topbar_open == 1) {
					$('html').removeClass('topbar_open');
					topbar.removeClass('toggled');
					topbar_open = 0;
				} else {
					$('html').addClass('topbar_open');
					topbar.addClass('toggled');
					topbar_open = 1;
				}
			});
			toggle_topbar = true;
		}
		if(!minimize_sidebar){
			var minibutton = $('.btn-minimize');
			if($('html').hasClass('sidebar_minimize')){
				mini_sidebar = 1;
				minibutton.addClass('toggled');
				minibutton.html('<i class="fa fa-ellipsis-v"></i>');
			}
			minibutton.on('click', function() {
				if (mini_sidebar == 1) {
					$('html').removeClass('sidebar_minimize');
					minibutton.removeClass('toggled');
					minibutton.html('<i class="fa fa-bars"></i>');
					mini_sidebar = 0;
				} else {
					$('html').addClass('sidebar_minimize');
					minibutton.addClass('toggled');
					minibutton.html('<i class="fa fa-ellipsis-v"></i>');
					mini_sidebar = 1;
				}
				$(window).resize();
			});
			minimize_sidebar = true;
		}
		if(!toggle_page_sidebar) {
			var pageSidebarToggler = $('.page-sidebar-toggler');
			pageSidebarToggler.on('click', function() {
				if (page_sidebar_open == 1) {
					$('html').removeClass('pagesidebar_open');
					pageSidebarToggler.removeClass('toggled');
					page_sidebar_open = 0;
				} else {
					$('html').addClass('pagesidebar_open');
					pageSidebarToggler.addClass('toggled');
					page_sidebar_open = 1;
				}
			});
			var pageSidebarClose = $('.page-sidebar .back');
			pageSidebarClose.on('click', function() {
				$('html').removeClass('pagesidebar_open');
				pageSidebarToggler.removeClass('toggled');
				page_sidebar_open = 0;
			});		
			toggle_page_sidebar = true;
		}
		$('.sidebar').hover(function() {
			if ($('html').hasClass('sidebar_minimize')){
				$('html').addClass('sidebar_minimize_hover');
			}
		}, function(){
			if ($('html').hasClass('sidebar_minimize')){
				$('html').removeClass('sidebar_minimize_hover');
			}
		});
		// addClass if nav-item click and has subnav
		$(".nav-item a").on('click', (function(){
			if ( $(this).parent().find('.collapse').hasClass("show") ) {
				$(this).parent().removeClass('submenu');
			} else {
				$(this).parent().addClass('submenu');
			}
		}));
		//Chat Open
		$('.messages-contact .user a').on('click', function(){
			$('.tab-chat').addClass('show-chat')
		});
		$('.messages-wrapper .return').on('click', function(){
			$('.tab-chat').removeClass('show-chat')
		});
		//select all
		$('[data-select="checkbox"]').change(function(){
			var target = $(this).attr('data-target');
			$(target).prop('checked', $(this).prop("checked"));
		})
		//form-group-default active if input focus
		$(".form-group-default .form-control").focus(function(){
			$(this).parent().addClass("active");
		}).blur(function(){
			$(this).parent().removeClass("active");
		})
	});	
}
// Input File Image
function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$(input).parent('.input-file-image').find('.img-upload-preview').attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}
}
$('.input-file-image input[type="file"').change(function () {
	readURL(this);
});
// Show Password
function showPassword(button) {
	var inputPassword = $(button).parent().find('input');
	if (inputPassword.attr('type') === "password") {
		inputPassword.attr('type', 'text');
	} else {
		inputPassword.attr('type','password');
	}
}
$('.show-password').on('click', function(){
	showPassword(this);
})
// Sign In & Sign Up
var containerSignIn = $('.container-login'),
containerSignUp = $('.container-signup'),
showSignIn = true,
showSignUp = false;
function changeContainer(){
	if(showSignIn == true){
		containerSignIn.css('display', 'block')
	} else {
		containerSignIn.css('display', 'none')
	}

	if(showSignUp == true){
		containerSignUp.css('display', 'block')
	} else {
		containerSignUp.css('display', 'none')
	}
}
$('#show-signup').on('click', function(){ 
	showSignUp = true;
	showSignIn = false;
	changeContainer();
})
$('#show-signin').on('click', function(){ 
	showSignUp = false;
	showSignIn = true;
	changeContainer();
})
changeContainer();
//Input with Floating Label
$('.form-floating-label .form-control').keyup(function(){
	if($(this).val() !== '') {
		$(this).addClass('filled');
	} else {
		$(this).removeClass('filled');
	}
})
if($("#tags").length>0){
	$(document).ready(function() {
		var tags_a = $("#tags").find("a");
		tags_a.each(function() {
			var x = 6;
			var y = 0;
			var bgcolors = "badge-success,badge-danger,badge-info,badge-primary,badge-secondary,badge-info,badge-dark";
			var bgcolor = bgcolors.split(",");
			var rand = parseInt(Math.random() * (x - y + 1) + y);
			$(this).addClass("h" + rand + "");
			$(this).find("span").addClass(bgcolor[rand]);
		});
	});	
}