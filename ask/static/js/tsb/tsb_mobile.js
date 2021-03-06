jQuery.fn.extend({
    highText: function (searchWords, htmlTag, tagClass)
    {
        return this.each(function ()
        {
            $(this).html(function high(replaced, search, htmlTag, tagClass)
            {
                var pattarn = search.replace(/\b(\w+)\b/g, "($1)").replace(/\s+/g, "|");

                return replaced.replace(new RegExp(pattarn, "ig"), function (keyword)
                {
                    return $("<" + htmlTag + " class=" + tagClass + ">" + keyword + "</" + htmlTag + ">").outerHTML();
                });
            }($(this).text(), searchWords, htmlTag, tagClass));
        });
    },
    outerHTML: function (s)
    {
        return (s) ? this.before(s).remove() : jQuery("<p>").append(this.eq(0).clone()).html();
    },
    insertAtCaret : function (textFeildValue)
    {
    	var textObj = $(this).get(0);
        if (document.all && textObj.createTextRange && textObj.caretPos)
        {
            var caretPos = textObj.caretPos;
            caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ?
                textFeildValue + '' : textFeildValue;
        }
        else if (textObj.setSelectionRange)
        {
            var rangeStart = textObj.selectionStart;
            var rangeEnd = textObj.selectionEnd;
            var tempStr1 = textObj.value.substring(0, rangeStart);
            var tempStr2 = textObj.value.substring(rangeEnd);
            textObj.value = tempStr1 + textFeildValue + tempStr2;
            textObj.focus();
            var len = textFeildValue.length;
            textObj.setSelectionRange(rangeStart + len, rangeStart + len);
            textObj.blur();
        }
        else
        {
            textObj.value += textFeildValue;
        }
    }

});

$(window).on('hashchange', function() {
	if (window.location.hash.indexOf('#!') != -1)
	{
		if ($('a[name=' + window.location.hash.replace('#!', '') + ']').length)
		{
			$.scrollTo($('a[name=' + window.location.hash.replace('#!', '') + ']').offset()['top'] - 20, 600, {queue:true});
		}
	}
});

$(document).ready(function () {
	// 判断是否微信打开
    if (G_IN_WECHAT == true)
    {
        $('header, nav, footer').hide();
        // if ($.cookie('wechat-tips-close') != 'true')
        // {
        // 	$('.aw-global-tips').show();
        // }
    }

	if (window.location.hash.indexOf('#!') != -1)
	{
		if ($('a[name=' + window.location.hash.replace('#!', '') + ']').length)
		{
			$.scrollTo($('a[name=' + window.location.hash.replace('#!', '') + ']').offset()['top'] - 20, 600, {queue:true});
		}
	}
	
	$('a[rel=lightbox]').fancybox(
    {
        openEffect: 'none',
        closeEffect: 'none',

        prevEffect: 'none',
        nextEffect: 'none',

        closeBtn: false,

        helpers:
        {
            buttons:
            {
                position: 'bottom'
            }
        },

        afterLoad: function ()
        {
            this.title = '第 ' + (this.index + 1) + ' 张, 共 ' + this.group.length + ' 张' + (this.title ? ' - ' + this.title : '');
        }
    });
	
	init_comment_box('.aw-add-comment');
	init_article_comment_box('.aw-article-comment');
	
	$('img#captcha').attr('src', G_BASE_URL + '/account/captcha/');
	
	$('#aw-top-nav-profile').click(function(){
		$('.aw-top-nav-popup').hide();
		$('.aw-top-nav-profile').show();
	});

	$('#aw-top-nav-notic').click(function()
	{
		$('.aw-top-nav-popup').hide();
		$('.aw-top-nav-notic').toggle();
	})

	//邀请回答按钮
	$('.aw-invite-replay-btn').click(function()
	{
		if ($(this).parents('.aw-question-detail').find('.aw-invite-replay').is(':visible'))
		{
			$(this).parents('.aw-question-detail').find('.aw-invite-replay').hide();
		}else
		{
			$(this).parents('.aw-question-detail').find('.aw-invite-replay').show();
		}
	});

	/* 点击下拉菜单外得地方隐藏　*/
	$(document).click(function(e)
	{
		var target = $(e.target);
		if (target.closest('#aw-top-nav-notic, #aw-top-nav-profile, .aw-top-nav-popup, .dropdown-list').length == 0)
		{
			$('.aw-top-nav-popup, .dropdown-list').hide();
		}
	});

	/* 话题编辑删除按钮 */
	$(document).on('click', '.aw-question-detail .aw-topic-edit-box .aw-topic-box i', function()
	{
		var _this = $(this);
		$.post(G_BASE_URL + '/question/ajax/delete_topic/?question_id', {'question_id' : $(this).parents('.aw-topic-edit-box').attr('data-id'), 'topic_id' : $(this).parents('.aw-topic-name').attr('data-id')} , function(result)
		{
			if (result.errno == 1) 
			{
				_this.parents('.aw-topic-name').detach();
			}else
			{
				alert(result.err);
			}
		}, 'json');
		return false;
	});

	dropdown_list('.tsb-search-input','search');
	dropdown_list('.aw-invite-input','invite');
	add_topic_box('.aw-question-detail .aw-add-topic-box','question');
	add_topic_box('.aw-mod-publish .aw-add-topic-box','publish');

});

/* 弹窗 */
function alert_box(type , data)
{
	var template;
	
	switch (type)
	{
		case 'publish' : 
			template = Hogan.compile(TSB_MOBILE_TEMPLATE.publish).render({
	            'category_id': data.category_id,
	            'ask_user_id': data.ask_user_id
	        });
		break;

		case 'redirect' : 
			template = Hogan.compile(TSB_MOBILE_TEMPLATE.redirect).render({
				'data-id' : data
			});
		break;

		case 'message' :
			template = Hogan.compile(TSB_MOBILE_TEMPLATE.message).render({
				'data-name' : data
			});
			//alert(template);
		break;
	}
	if (template)
	{
		$('#aw-ajax-box').empty().append(template);
		
		switch (type)
		{
			case 'message' :
				dropdown_list('.aw-message-input','message');
			break;
			
			case 'redirect' : 
				dropdown_list('.aw-redirect-input','redirect');
			break;

			case 'publish' :
				if (parseInt(data.category_enable) == 1)
	        	{
		        	$.get(G_BASE_URL + '/publish/ajax/fetch_question_category/', function (result)
		            {
		                add_dropdown_list('.alert-publish .aw-publish-dropdown', eval(result), data.category_id);

		                $('.alert-publish .aw-publish-dropdown li a').click(function ()
		                {
		                    $('#quick_publish_category_id').val($(this).attr('data-value'));
		                });
		            });
		            
		            $('#quick_publish_topic_chooser').hide();
	        	}
	        	else
	        	{
		        	$('#quick_publish_category_chooser').hide();
	        	}
	
	            if ($('#aw-search-query').val() && $('#aw-search-query').val() != $('#aw-search-query').attr('placeholder'))
	            {
		            $('#quick_publish_question_content').val($('#aw-search-query').val());
	            }
	            
	            add_topic_box('.alert-publish .aw-topic-edit-box .aw-add-topic-box', 'publish');
				
	            $('#quick_publish .aw-add-topic-box').click();
	            
	            if (G_QUICK_PUBLISH_HUMAN_VALID)
	            {
		            $('#quick_publish_captcha').show();
		            $('#captcha').click();
	            }
			break;
		}
	}
	
	$('.alert-' + type).modal('show');
}

/* 下拉列表 */
var aw_dropdown_list_interval, aw_dropdown_list_flag = 0;

function dropdown_list(element, type)
{
	var ul = $(element).next().find('ul');
	
	$(element).keydown(function()
	{
		if (aw_dropdown_list_flag == 0)
		{
			aw_dropdown_list_interval = setInterval(function()
			{
				if ($(element).val().length >= 2)
				{
					switch (type)
					{
						case 'search' : 
							$.get(G_BASE_URL + '/tsbm/ajax/search/?q=' + encodeURIComponent($(element).val()) + '&limit=5',function(result)
							{
								if (result.length > 0)
								{
									ul.html('');
									
									$.each(result, function(i, e)
									{
										switch(result[i].type)
										{
											case 'questions' :
												ul.append('<li><a href="' + decodeURIComponent(result[i].url) + '">' + result[i].name + '<span class="num small">&nbsp;' + result[i].detail.answer_count + ' &nbsp;个回答</span></a></li>');
												break;
												
											case 'articles' :
												ul.append('<li><a href="' + decodeURIComponent(result[i].url) + '">' + result[i].name + '<span class="num">' + result[i].detail.comments + ' 个评论</span></a></li>');
												break;

											case 'topics' :
												ul.append('<li><a class="aw-topic-name" href="' + decodeURIComponent(result[i].url) + '">' + result[i].name  + '</a><span class="num">' + result[i].detail.discuss_count + ' &nbsp;个问题</span></li>');
												break;

											case 'users' :
												ul.append('<li><a href="' + decodeURIComponent(result[i].url) + '"><img src="' + result[i].detail.avatar_file + '"><span>&nbsp;' + result[i].name + '</span></a></li>');
												break;
										}
									});
									
									$(element).next().show();
								}else
								{
									$(element).next().hide();
								}
							},'json');
						break;

						case 'message' :
							$.get(G_BASE_URL + '/tsbm/ajax/search/?type-users__q-' + encodeURIComponent($(element).val()) + '__limit-10',function(result)
							{
								
								if (result.length > 0)
								{
									ul.html('');
									$.each(result ,function(i, e)
									{
										ul.append('<li><a><img src="' + result[i].detail.avatar_file + '"><span>' + result[i].name + '</span></a></li>')
									});	
									$('.alert-message .dropdown-list ul li a').click(function()
									{
										$(element).val($(this).text());
										$(element).next().hide();
									});
									
									$(element).next().show();
								}else
								{
									$(element).next().hide();
								}
							},'json');
						break;

						case 'invite' : 
							$.get(G_BASE_URL + '/tsbm/ajax/search/?type-users__q-' + encodeURIComponent($(element).val()) + '__limit-10',function(result)
							{
								if (result.length > 0)
								{
									ul.html('');
									
									$.each(result ,function(i, e)
									{
										ul.append('<li><a data-id="' + result[i].uid + '"><img src="' + result[i].detail.avatar_file + '"><span>' + result[i].name + '</span></a></li>')
									});
									
									$('.aw-invite-replay .dropdown-list ul li a').click(function()
									{
										$.post(G_BASE_URL + '/question/ajax/save_invite/',
									    {
									        'question_id': QUESTION_ID,
									        'uid': $(this).attr('data-id')
									    },function(result)
									    {
									    	if (result.errno == -1)
									    	{
									    		alert(result.err);
									    	}else
									    	{
									    		location.reload();
									    	}
									    }, 'json');
									});
									
									$(element).next().show();
								}else
								{
									$(element).next().hide();
								}
							},'json');
						break;

						case 'redirect' :
							$.get(G_BASE_URL + '/tsbm/ajax/search/?q=' + encodeURIComponent($(element).val()) + '&type=questions&limit-30',function(result)
							{
								if (result.length > 0)
								{
									ul.html('');
									
									$.each(result ,function(i, e)
									{
										ul.append('<li><a onclick="ajax_request(' + "'" + G_BASE_URL + "/question/ajax/redirect/', 'item_id=" + $(element).attr('data-id') + "&target_id=" + result[i].search_id + "'" +')">' + result[i].name +'</a></li>')
									});	
									
									$(element).next().show();
								}else
								{
									$(element).next().hide();
								}
							},'json');
						break;

						case 'topic' :
							$.get(G_BASE_URL + '/tsbm/ajax/search/?type-topics__q-' + encodeURIComponent($(element).val()) + '__limit-10',function(result)
							{
								if (result.length > 0)
								{
									ul.html('');
									
									$.each(result ,function(i, e)
									{
										ul.append('<li><a>' + result[i].name +'</a></li>')
									});	
									
									$('.aw-question-detail .aw-topic-edit-box .dropdown-list ul li').click(function()
									{
										var _this = $(this);
										
										$.post(G_BASE_URL + '/question/ajax/save_topic/question_id-' + $(this).parents('.aw-topic-edit-box').attr('data-id'), 'topic_title=' + $(this).text(), function(result)
										{
											if (result.errno == 1)
											{
												$(element).parents('.aw-topic-edit-box').find('.aw-topic-box').prepend('<span class="aw-topic-name" data-id="' + result.rsm.topic_id + '"><a>' + _this.text() + '</a><a href="#"><i>X</i></a></span>');
												$(element).val('');
												$(element).next().hide();
											}else
											{
												alert(result.err);
											}
										}, 'json');
									});
									
									$('.alert-publish .aw-topic-edit-box .dropdown-list ul li, .aw-mod-publish .aw-topic-edit-box .dropdown-list ul li').click(function()
									{
										$(element).parents('.aw-topic-edit-box').find('.aw-topic-box').prepend('<span class="aw-topic-name"><a>' + $(this).text() + '</a><input type="hidden" name="topics[]" value="' + $(this).text() + '"><a href="#"><i onclick="$(this).parents(\'.aw-topic-name\').detach();">X</i></a></span>');
										$(element).val('');
										$('.alert-publish .aw-topic-edit-box .dropdown-list, .aw-mod-publish .aw-topic-edit-box .dropdown-list').hide();
									});
									
									$(element).next().show();
								}else
								{
									$(element).next().hide();
								}
							},'json');
						break;
					}
				}
				else
				{
					$(element).next().hide();
				}
			},1000);

			switch (type)
			{
				case 'message' :
					$('.alert-message .dropdown-list ul li').click(function()
					{
						$(element).val($(this).find('span').html());
						$(element).next().hide();
					});
				break;

				case 'invite' : 
				break;
			}
			aw_dropdown_list_flag = 1;
			return aw_dropdown_list_interval;
		}
	});
	
	$(element).blur(function()
	{
		clearInterval(aw_dropdown_list_interval);
		
		aw_dropdown_list_flag = 0;
	});
}

/* 话题编辑 */
function add_topic_box(element, type)
{
	$(element).click(function()
	{
		var data_id = $(this).parents('.aw-topic-edit-box').attr('data-id');
		$(element).hide();
		$(element).parents('.aw-topic-edit-box').append(TSB_MOBILE_TEMPLATE.topic_edit_box);
		$.each($(element).parents('.aw-topic-edit-box').find('.aw-topic-name'), function(i, e)
		{
			if (!$(e).has('i')[0])
			{

				$(e).append('<a href="#"><i>X</i></a>');
			}
		});
		dropdown_list('.aw-topic-box-selector .aw-topic-input','topic');
		/* 话题编辑添加按钮 */
		$('.aw-topic-box-selector .add').click(function()
		{
			switch (type)
			{
				case 'publish' :
					$(this).parents('.aw-topic-edit-box').find('.aw-topic-box').prepend('<span class="aw-topic-name"><a>' + $(this).parents('.aw-topic-box-selector').find('.aw-topic-input').val() + '</a><input type="hidden" name="topics[]" value="' + $(this).parents('.aw-topic-box-selector').find('.aw-topic-input').val() + '"><a><i onclick="$(this).parents(\'.aw-topic-name\').detach();">X</i></a></span>');
					$(this).parents('.aw-topic-edit-box').find('.aw-topic-input').val('');
					$(this).parents('.aw-topic-edit-box').find('.dropdown-list').hide();
				break;
				case 'question' :
					var _this = $(this);
					$.post(G_BASE_URL + '/question/ajax/save_topic/question_id-' + data_id, 'topic_title=' + $(this).parents('.aw-topic-box-selector').find('.aw-topic-input').val(), function(result)
					{
						if (result.errno == 1)
						{
							_this.parents('.aw-topic-edit-box').find('.aw-topic-box').prepend('<span class="aw-topic-name" data-id="'+ result.rsm.topic_id +'"><a>' + _this.parents('.aw-topic-box-selector').find('.aw-topic-input').val() + '</a><a><i>X</i></a></span>');
							_this.parents('.aw-topic-edit-box').find('.aw-topic-input').val('');
							_this.parents('.aw-topic-edit-box').find('.dropdown-list').hide();
						}else
						{
							alert(result.err);
							_this.parents('.aw-topic-edit-box').find('.dropdown-list').hide();
						}
					}, 'json');
				break;
			}
			
		});
		/* 话题编辑取消按钮 */
		$('.aw-topic-box-selector .cancel').click(function()
		{
			$(this).parents('.aw-topic-edit-box').find('.aw-add-topic-box').show();
			$.each($(this).parents('.aw-topic-edit-box').find('.aw-topic-name'), function(i, e)
			{
				if ($(e).has('i')[0])
				{
					$(e).find('i').detach();
				}
			});
			$(this).parents('.aw-topic-box-selector').detach();
		});
	});
}

/*取消邀请*/
function disinvite_user(obj, uid)
{
    $.get(G_BASE_URL + '/question/ajax/cancel_question_invite/question_id-' + QUESTION_ID + "__recipients_uid-" + uid);
}

/*动态插入下拉菜单模板*/
function add_dropdown_list(selecter, data, selected)
{
    $(selecter).append(Hogan.compile(TSB_MOBILE_TEMPLATE.dropdownList).render(
    {
        'items': data
    }));

    $(selecter + ' .dropdown-menu li a').click(function ()
    {
        $('.aw-publish-dropdown span').html($(this).text());
    });

    if (selected)
    {
        $(selecter + " .dropdown-menu li a[data-value='" + selected + "']").click();
    }
}

/*修复focus时光标位置*/
function _fix_textarea_focus_cursor_position(elTextarea)
{
    if (/MSIE/.test(navigator.userAgent) || /Opera/.test(navigator.userAgent))
    {
        var rng = elTextarea.createTextRange();
        rng.text = elTextarea.value;
        rng.collapse(false);
    }
    else if (/WebKit/.test(navigator.userAgent))
    {
        elTextarea.select();
        window.getSelection().collapseToEnd();
    }
}

function _quick_publish_processer(result)
{
	$.loading('hide');
	
    if (typeof (result.errno) == 'undefined')
    {
        alert(result);
    }
    else if (result.errno != 1)
    {
        $('#quick_publish_error em').html(result.err);
        $('#quick_publish_error').fadeIn();
    }
    else
    {
        if (result.rsm && result.rsm.url)
        {
            window.location = decodeURIComponent(result.rsm.url);
        }
        else
        {
            window.location.reload();
        }
    }
}

function init_fileuploader(element_id, action_url)
{
    if (!document.getElementById(element_id))
    {
        return false;
    }
    
    if (G_UPLOAD_ENABLE == 'Y')
    {
    	$('.aw-upload-tips').show();
    }

    return new _ajax_uploader.FileUploader(
    {
        element: document.getElementById(element_id),
        action: action_url,
        debug: false
    });
}

function insert_attach(element, attach_id, attach_tag)
{
    $(element).parents('form').find('textarea').insertAtCaret("\n[" + attach_tag + "]" + attach_id + "[/" + attach_tag + "]\n");
}

/* 文章赞同反对 */
function article_vote(element, article_id, rating)
{
	$.loading('show');
	
	if ($(element).hasClass('active'))
	{
		rating = 0;
	}
	
	$.post(G_BASE_URL + '/article/ajax/article_vote/', 'type=article&item_id=' + article_id + '&rating=' + rating, function (result) {
		$.loading('hide');
		
		if (result.errno != 1)
	    {
	        $.alert(result.err);
	    }
	    else
	    {
			if (rating == 0)
			{

				$(element).removeClass('active');
                $(element).find('b').html(parseInt($(element).find('b').html()) - 1);
			}
            else if (rating == -1)
            {
                if ($(element).parents('.aw-article-vote').find('.agree').hasClass('active'))
                {
                    $(element).parents('.aw-article-vote').find('b').html(parseInt($(element).parents('.aw-article-vote').find('b').html()) - 1);
                    $(element).parents('.aw-article-vote').find('a').removeClass('active');
                }
                $(element).addClass('active');
            }
			else
			{
				$(element).parents('.aw-article-vote').find('a').removeClass('active');
				$(element).addClass('active');
                $(element).find('b').html(parseInt($(element).find('b').html()) + 1);
			}
	    }
	}, 'json');
}


var aw_loading_timer;
var aw_loading_bg_count = 12;

$.loading = function (s) {
	if ($('#aw-loading').length == 0)
    {
        $('#aw-ajax-box').append('<div id="aw-loading" ><div id="aw-loading-box"></div></div>');
    }
    
	if (s == 'show')
	{
		if ($('#aw-loading').css('display') == 'block')
	    {
		    return false;
	    }
		
		$('#aw-loading').fadeIn();
	
		aw_loading_timer = setInterval(function () {
			aw_loading_bg_count = aw_loading_bg_count - 1;
			
			$('#aw-loading-box').css('background-position', '0px ' + aw_loading_bg_count * 40 + 'px');
			
			if (aw_loading_bg_count == 1)
			{
				aw_loading_bg_count = 12;
			}
		}, 100);
	}
	else
	{
		$('#aw-loading').fadeOut();
	
		clearInterval(aw_loading_timer);
	}
};

function _t(string, replace)
{	
	if (typeof(aws_lang) == 'undefined')
	{
		if (replace)
		{
			string = string.replace('%s', replace);
		}
		
		return string;
	}
	
	if (aws_lang[string])
	{
		string = aws_lang[string];
		
		if (replace)
		{
			string = string.replace('%s', replace);
		}
		
		return string;
	}	
}

var _list_view_pages = new Array();

function load_list_view(url, list_view, ul_button, start_page, callback_func)
{	
	if (!ul_button.attr('id'))
	{
		return false;
	}
	
	if (!start_page)
	{
		start_page = 0
	}
	
	_list_view_pages[ul_button.attr('id')] = start_page;
	
	ul_button.unbind('click');
	
	ul_button.bind('click', function () {
		var _this = this;
			
		$.loading('show');
	
		$(_this).addClass('disabled');
			
		$.get(url + '__page-' + _list_view_pages[ul_button.attr('id')], function (response)
		{			
			if ($.trim(response) != '')
			{
				if (_list_view_pages[ul_button.attr('id')] == start_page)
				{
					list_view.html(response);
				}
				else
				{
					list_view.append(response);
				}
				
				_list_view_pages[ul_button.attr('id')]++; 
				
				$(_this).removeClass('disabled');
			}
			else
			{
				if ($.trim(list_view.html()) == '')
				{
					list_view.append('<p align="center">没有相关内容</p>');
				}
							
				$(_this).unbind('click').bind('click', function () { return false; });
			}
				
			$.loading('hide');
			
			if (callback_func != null)
			{
				callback_func();
			}
		});
			
		return false;
	});
	
	ul_button.click();
}

function ajax_post(formEl, processer)	// 表单对象，用 jQuery 获取，回调函数名
{	
	if (typeof(processer) != 'function')
	{
		processer = _ajax_post_processer;
	}
	
	var custom_data = {
		_post_type:'ajax',
		_is_mobile:'true'
	};
	
	$.loading('show');
	
	formEl.ajaxSubmit({
		dataType: 'json',
		data: custom_data,
		success: processer,
		error:	function (error) { if ($.trim(error.responseText) != '') { $.loading('hide'); alert(_t('发生错误, 返回的信息:') + ' ' + error.responseText); } }
	});
}

function _ajax_post_processer(result)
{
	$.loading('hide');
	
	if (typeof(result.errno) == 'undefined')
	{
		alert(result);
	}
	else if (result.errno != 1)
	{
		alert(result.err);
	}
	else
	{		
		if (result.rsm && result.rsm.url)
		{
			window.location = decodeURIComponent(result.rsm.url);
		}
		else
		{
			window.location.reload();
		}
	}
}

function ajax_request(url, params)
{
	$.loading('show');
	
	if (params)
	{
		$.post(url, params, function (result) {
			$.loading('hide');
			
			if (result.err)
			{
				alert(result.err);
			}
			else if (result.rsm && result.rsm.url)
			{
				window.location = decodeURIComponent(result.rsm.url);
			}
			else
			{
				window.location.reload();
			}
		}, 'json').error(function (error) { if ($.trim(error.responseText) != '') {  $.loading('hide'); alert(_t('发生错误, 返回的信息:') + ' ' + error.responseText); } });
	}
	else
	{
		$.get(url, function (result) {
			$.loading('hide');
			
			if (result.err)
			{
				alert(result.err);
			}
			else if (result.rsm && result.rsm.url)
			{
				window.location = decodeURIComponent(result.rsm.url);
			}
			else
			{
				window.location.reload();
			}
		}, 'json').error(function (error) { if ($.trim(error.responseText) != '') { $.loading('hide'); alert(_t('发生错误, 返回的信息:') + ' ' + error.responseText); } });
	}
	
	return false;
}

function focus_question(el, text_el, question_id)
{
	if (el.hasClass('aw-active'))
	{
		text_el.html(_t('关注'));
		
		el.removeClass('aw-active');
	}
	else
	{
		text_el.html(_t('取消关注'));
		
		el.addClass('aw-active');
	}
	
	$.loading('show');
	
	$.get(G_BASE_URL + '/question/ajax/focus/question_id-' + question_id, function (data)
	{
		$.loading('hide');
		
		if (data.errno != 1)
		{
			if (data.err)
			{
				alert(data.err);
			}
			
			if (data.rsm.url)
			{
				window.location = decodeURIComponent(data.rsm.url);
			}
		}
	}, 'json');
}

function focus_topic(el, text_el, topic_id)
{
	if (el.hasClass('aw-active'))
	{
		text_el.html(_t('关注'));
		el.removeClass('aw-active');
	}
	else
	{
		text_el.html(_t('取消关注'));
		el.addClass('aw-active');
	}
	
	$.loading('show');
	
	$.get(G_BASE_URL + '/topic/ajax/focus_topic/topic_id-' + topic_id, function (data)
	{
		$.loading('hide');
		
		if (data.errno != 1)
		{
			if (data.err)
			{
				alert(data.err);
			}
			
			if (data.rsm.url)
			{
				window.location = decodeURIComponent(data.rsm.url);
			}
		}
	}, 'json');
}

function follow_people(el, text_el, uid)
{
	if (el.hasClass('aw-active'))
	{
		text_el.html(_t('关注'));
		el.removeClass('aw-active');
	}
	else
	{
		text_el.html(_t('取消关注'));
		el.addClass('aw-active');
	}
	
	$.loading('show');
	
	$.get(G_BASE_URL + '/follow/ajax/follow_people/uid-' + uid, function (data)
	{
		$.loading('hide');
		
		if (data.errno != 1)
		{
			if (data.err)
			{
				alert(data.err);
			}
			
			if (data.rsm.url)
			{
				window.location = decodeURIComponent(data.rsm.url);
			}
		}
	}, 'json');
}

function answer_user_rate(answer_id, type, element)
{
	$.loading('show');
	
	$.post(G_BASE_URL + '/question/ajax/question_answer_rate/', 'type=' + type + '&answer_id=' + answer_id, function (result) {
		
		$.loading('hide');
		
		if (result.errno != 1)
		{
			alert(result.err);
		}
		else if (result.errno == 1)
		{
			switch (type)
			{
				case 'thanks':
					if (result.rsm.action == 'add')
					{
						$(element).find('span.ui-btn-text').html(_t('已感谢'));
						$(element).removeAttr('onclick');
					}
					else
					{
						$(element).html(_t('感谢'));
					}
				break;
				
				case 'uninterested':
					if (result.rsm.action == 'add')
					{
						$(element).find('span.ui-btn-text').html(_t('撤消没有帮助'));
					}
					else
					{
						$(element).find('span.ui-btn-text').html(_t('没有帮助'));
					}
				break;
			}
		}
	}, 'json');
}

function _ajax_post_confirm_processer(result)
{
	$.loading('hide');
	
	if (typeof(result.errno) == 'undefined')
	{
		alert(result);
	}
	else if (result.errno != 1)
	{
		if (!confirm(result.err))
		{
			return false;	
		}
	}
	
	if (result.errno == 1 && result.err)
	{
		alert(result.err);
	}
	
	if (result.rsm && result.rsm.url)
	{
		window.location = decodeURIComponent(result.rsm.url);
	}
	else
	{
		window.location.reload();
	}
}

function answer_vote(element, answer_id, val)
{
	var data_theme = element.attr('data-theme');
	
	$('.ui-dialog').dialog('close');
	
	$.loading('show');
	
	$.post(G_BASE_URL + '/question/ajax/answer_vote/', 'answer_id=' + answer_id + '&value=' + val, function (result) {
		$.loading('hide');
		
		if (data_theme == 'd')
		{
			$('#answer_vote_button').removeClass('ui-btn-up-d').removeClass('ui-btn-hover-d');
		
			$('#answer_vote_button').addClass('ui-btn-up-b');
			$('#answer_vote_button').attr('data-theme', 'b');
			
			if (parseInt(val) > 0)
			{
				$('#answer_vote_button').find('span.ui-btn-text').html((parseInt($('#answer_vote_button').find('span.ui-btn-text').text()) + parseInt(val)));
			}
		}
		else
		{
			$('#answer_vote_button').removeClass('ui-btn-up-b').removeClass('ui-btn-hover-b');
		
			$('#answer_vote_button').addClass('ui-btn-up-d');
			$('#answer_vote_button').attr('data-theme', 'd');
			
			if (parseInt(val) > 0)
			{
				$('#answer_vote_button').find('span.ui-btn-text').html((parseInt($('#answer_vote_button').find('span.ui-btn-text').text()) - parseInt(val)));
			}
		}
	});
}

function init_comment_box(selecter)
{
    $(document).on('click', selecter, function ()
    {
        if (!$(this).attr('data-type') || !$(this).attr('data-id'))
        {
            return true;
        }

        var comment_box_id = '#aw-comment-box-' + $(this).attr('data-type') + '-' + 　$(this).attr('data-id');
		
        if ($(comment_box_id).length > 0)
        {
            if ($(comment_box_id).css('display') == 'none')
            {
                $(comment_box_id).fadeIn();
            }
            else
            {
                $(comment_box_id).fadeOut();
            }
        }
        else
        {
            // 动态插入commentBox
            switch ($(this).attr('data-type'))
            {
	            case 'question':
	                var comment_form_action = G_BASE_URL + '/question/ajax/save_question_comment/question_id-' + $(this).attr('data-id');
	                var comment_data_url = G_BASE_URL + '/question/ajax/get_question_comments/question_id-' + $(this).attr('data-id');
	                break;
	
	            case 'answer':
	                var comment_form_action = G_BASE_URL + '/question/ajax/save_answer_comment/answer_id-' + $(this).attr('data-id');
	                var comment_data_url = G_BASE_URL + '/question/ajax/get_answer_comments/answer_id-' + $(this).attr('data-id');
	                break;
            }

            if (G_USER_ID && $(this).attr('data-close') != 'true')
            {
                $(this).parents('.aw-mod-footer').append(Hogan.compile(TSB_MOBILE_TEMPLATE.commentBox).render(
                {
                    'comment_form_id': comment_box_id.replace('#', ''),
                    'comment_form_action': comment_form_action
                }));
				
                $(comment_box_id).find('.close-comment-box').click(function ()
                {
                    $(comment_box_id).fadeOut();
                });

                $(comment_box_id).find('.aw-comment-txt').autosize();
            }
            else
            {
                $(this).parent().parent().append(Hogan.compile(TSB_MOBILE_TEMPLATE.commentBoxClose).render(
                {
                    'comment_form_id': comment_box_id.replace('#', ''),
                    'comment_form_action': comment_form_action
                }));
            }

            //判断是否有评论数据
            $.get(comment_data_url, function (result)
            {
                if ($.trim(result) == '')
                {
                    result = '<p align="center">' + _t('暂无评论') + '</p>';
                }

                $(comment_box_id).find('.aw-comment-list').html(result);
            });

            //var left = $(this).width()/2 + $(this).prev().width();
            /*给三角形定位*/
            //$(comment_box_id).find('.i-comment-triangle').css('left', $(this).width() / 2 + $(this).prev().width() + 15);
        }
    });
}

function init_article_comment_box(selecter)
{
	$(document).on('click', selecter, function ()
    {
        if ($(this).parents('.aw-item').find('.aw-comment-box').length)
        {
            if ($(this).parents('.aw-item').find('.aw-comment-box').css('display') == 'block')
            {
               $(this).parents('.aw-item').find('.aw-comment-box').fadeOut();
            }
            else
            {
                $(this).parents('.aw-item').find('.aw-comment-box').fadeIn();
            }
        }
        else
        {
            $(this).parents('.aw-item').append(Hogan.compile(TSB_MOBILE_TEMPLATE.articleCommentBox).render(
            {
                'at_uid' : $(this).attr('data-id'),
                'article_id' : $('.aw-anwser-box input[name="article_id"]').val()
            }));
            $(this).parents('.aw-item').find('.close-comment-box').click(function ()
            {
                $(this).parents('.aw-item').find('.aw-comment-box').fadeOut();
            });
            $(this).parents('.aw-item').find('.aw-comment-txt').autosize();
        }
    });
}

function save_comment(save_button_el)
{
    $(save_button_el).attr('_onclick', $(save_button_el).attr('onclick')).addClass('disabled').removeAttr('onclick').addClass('_save_comment');

    ajax_post($(save_button_el).parents('form'), _comments_form_processer);
}

function _comments_form_processer(result)
{
	$.loading('hide');
	
    $.each($('a._save_comment.disabled'), function (i, e)
    {
        $(e).attr('onclick', $(this).attr('_onclick')).removeAttr('_onclick').removeClass('disabled').removeClass('_save_comment');
    });

    if (result.errno != 1)
    {
        alert(result.err);
    }
    else
    {
        reload_comments_list(result.rsm.item_id, result.rsm.item_id, result.rsm.type_name);

        $('#aw-comment-box-' + result.rsm.type_name + '-' + result.rsm.item_id + ' form input').val('');
        $('#aw-comment-box-' + result.rsm.type_name + '-' + result.rsm.item_id + ' form').fadeOut();
    }
}

function remove_comment(el, type, comment_id)
{
	$.get(G_BASE_URL + '/question/ajax/remove_comment/type-' + type + '__comment_id-' + comment_id);
	
	$(el).parents('.aw-comment-box li').fadeOut();
}

function reload_comments_list(item_id, element_id, type_name)
{
    $('#aw-comment-box-' + type_name + '-' + element_id + ' .aw-comment-list').html('<p align="center" class="aw-padding10"><i class="aw-loading"></i></p>');

    $.get(G_BASE_URL + '/question/ajax/get_' + type_name + '_comments/' + type_name + '_id-' + item_id, function (data)
    {
        $('#aw-comment-box-' + type_name + '-' + element_id + ' .aw-comment-list').html(data);
    });
}

function question_thanks(question_id, element)
{
    $.post(G_BASE_URL + '/question/ajax/question_thanks/', 'question_id=' + question_id, function (result)
    {
        if (result.errno != 1)
        {
            alert(result.err);
        }
        else if (result.rsm.action == 'add')
        {
            $(element).html($(element).html().replace(_t('感谢'), _t('已感谢')));
            $(element).removeAttr('onclick');
        }
        else
        {
            $(element).html($(element).html().replace(_t('已感谢'), _t('感谢')));
        }
    }, 'json');
}

//赞成投票
function agreeVote(element, answer_id)
{
	$.post(G_BASE_URL + '/question/ajax/answer_vote/', 'answer_id=' + answer_id + '&value=1', function (result) {});
	
    //判断是否投票过   
    if ($(element).find('i').hasClass('active'))
    {
    	return false;
    }
    else
    {
    	$(element).find('i').addClass('active');
    	
    	$(element).find('em').html(parseInt($(element).find('em').html()) + 1);
    }
}

//反对投票
function disagreeVote(element, answer_id)
{
    $.post(G_BASE_URL + '/question/ajax/answer_vote/', 'answer_id=' + answer_id + '&value=-1', function (result) {});
    
    //判断是否投票过
    if ($(element).find('.aw-icon').hasClass('active'))
    {
    	$(element).parents('.aw-mod-footer').find('a.answer_vote .aw-icon').removeClass('active');
    	
        $(element).find('.aw-icon').removeClass('active');
    }
    else
    {
    	if ($(element).parents('.aw-mod-footer').find('.agree').hasClass('active'))
    	{
    		if (parseInt($(element).parents('.aw-mod-footer').find('.agree').next().html()) > 0)
	    	{
	    		$(element).parents('.aw-mod-footer').find('.agree').next().html(parseInt($(element).parents('.aw-mod-footer').find('.agree').next().html()) - 1);
	    	}
    	}

    	$(element).parents('.aw-mod-footer').find('a.answer_vote .aw-icon').removeClass('active');
    	
    	$(element).find('.aw-icon').addClass('active');
    	
    	
       	//$(element).parents('.aw-mod-footer').find('a.answer_vote em').html(parseInt($(element).parents('.aw-mod-footer').find('a.answer_vote em').html()) - 1);
    }
}

function getList(key,feature_id, category_id, listview, more) {

            var url = G_BASE_URL + "/tsbm/ajax/discuss/?sort_type=new&feature_id=" + feature_id + "&category=" + category_id + "&template=m&";
            //alert("ur__aa-b"+"__cc");
            // if($('#board-collapse_btn_'+key).hasClass('glyphicon-collapse-down')){
                // $('#board-collapse_btn_'+key).removeClass('glyphicon-collapse-down ');
                // $('#board-collapse_btn_'+key).addClass('glyphicon-collapse-up');
                // var scroll_offset = $('#panel_'+key).offset(); //得到pos这个div层的offset，包含两个值，top和left
                // $("body,html").animate({
                    // scrollTop:scroll_offset.top
                // },0);
//                 
            // }else{
                // $('#board-collapse_btn_'+key).removeClass('glyphicon-collapse-up ');
                // $('#board-collapse_btn_'+key).addClass('glyphicon-collapse-down');
            // }
            
            
            load_list_view(url, listview, more, 1);
            
            
            

}

function answer_user_rate(answer_id, type, element)
{
    $.post(G_BASE_URL + '/question/ajax/question_answer_rate/', 'type=' + type + '&answer_id=' + answer_id, function (result)
    {
        if (result.errno != 1)
        {
            alert(result.err);
        }
        else if (result.errno == 1)
        {
            switch (type)
            {
            case 'thanks':
                if (result.rsm.action == 'add')
                {
                    $(element).html($(element).html().replace(_t('感谢'), _t('已感谢')));
                    $(element).removeAttr('onclick');
                }
                else
                {
                    $(element).html($(element).html().replace(_t('已感谢'), _t('感谢')));
                }
                break;

            case 'uninterested':
                if (result.rsm.action == 'add')
                {
                    $(element).html(_t('撤消没有帮助'));
                }
                else
                {
                    $(element).html(_t('没有帮助'));
                }
                break;
            }
        }
    }, 'json');
}

function comment_vote(element, comment_id, rating)
{
	$.loading('show');
	
	if ($(element).hasClass('active'))
	{
		rating = 0;
	}
	
	$.post(G_BASE_URL + '/article/ajax/article_vote/', 'type=comment&item_id=' + comment_id + '&rating=' + rating, function (result) {
		$.loading('hide');
		
		if (result.errno != 1)
	    {
	        alert(result.err);
	    }
	    else
	    {
			if (rating == 0)
			{
				$(element).removeClass('active');
				$(element).html($(element).html().replace(_t('我已赞'), _t('赞')));
			}
			else
			{
				$(element).addClass('active');
				$(element).html($(element).html().replace(_t('赞'), _t('我已赞')));
			}
	    }
	}, 'json');
}