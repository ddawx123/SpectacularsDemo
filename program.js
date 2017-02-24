(function ($) {
    var container;
    // color config
    var colors = ['#96C2F1', '#BBE1F1', '#E3E197', '#F8B3D0', '#FFCC00', '#6B8E23', '#4682B4'];
    //build div container
    var createItem = function (text) {
        var color = colors[parseInt(Math.random() * 7, 10)]
        $('<div class="item"><p>' + text + '</p><a href="javascript:void(0);">隐藏</a></div>').css({ 'background': color }).appendTo(container).drag();
    };
    // container move event
    $.fn.drag = function () {
        var $this = $(this);
        var parent = $this.parent();
        var pw = parent.width();
        var ph = parent.height();
        var thisWidth = $this.width() + parseInt($this.css('padding-left'), 10) + parseInt($this.css('padding-right'), 10);
        var thisHeight = $this.height() + parseInt($this.css('padding-top'), 10) + parseInt($this.css('padding-bottom'), 10);
        var x, y, positionX, positionY;
        var isDown = false;
        var randY = parseInt(Math.random() * (ph - thisHeight), 10);
        var randX = parseInt(Math.random() * (pw - thisWidth), 10);
        parent.css({
            "position": "relative",
            "overflow": "hidden"
        });
        $this.css({
            "cursor": "move",
            "position": "absolute"
        }).css({
            top: randY,
            left: randX
        }).mousedown(function (e) {
            parent.children().css({
                "zIndex": "0"
            });
            $this.css({
                "zIndex": "1"
            });
            isDown = true;
            x = e.pageX;
            y = e.pageY;
            positionX = $this.position().left;
            positionY = $this.position().top;
            return false;
        });
        $(document).mouseup(function (e) {
            isDown = false;
        }).mousemove(function (e) {
            var xPage = e.pageX;
            var moveX = positionX + xPage - x;

            var yPage = e.pageY;
            var moveY = positionY + yPage - y;

            if (isDown == true) {
                $this.css({
                    "left": moveX,
                    "top": moveY
                });
            } else {
                return;
            }
            if (moveX < 0) {
                $this.css({
                    "left": "0"
                });
            }
            if (moveX > (pw - thisWidth)) {
                $this.css({
                    "left": pw - thisWidth
                });
            }
            if (moveY < 0) {
                $this.css({
                    "top": "0"
                });
            }
            if (moveY > (ph - thisHeight)) {
                $this.css({
                    "top": ph - thisHeight
                });
            }
        });
    };
    // Initialization Function
    var init = function () {
        container = $('#container');
        // Bind message close event
        container.on('click', 'a', function () {
            $(this).parent().remove();
			swal({
				title: "本信息窗2秒后自动关闭",
				text: "此处关闭仅在本次浏览生效，并不会移除留言记录，重新刷新页面即可重现！如果需要彻底移除某留言，请联系站点管理员并待审核通过后移除。",
				timer: 2000,
				showConfirmButton: true
			});
        })
		.height($(window).height() - 204);
		//demo data for this program..
		//var tests = ['道友，还处在凝气期吗？', 'I have a dream...', '路漫漫其修远兮。。。', '与自己为敌，与自己为友', '既然选择了远方，便只顾风雨兼程！'];
        $.each(tests, function (i, v) {
            createItem(v);
        });
        // bind inputbox event
        $('#input').keydown(function (e) {
            var $this = $(this);
            if (e.keyCode == '13') {
                var value = $this.val();
                if (value) {
					swal({
						title: "操作确认",
						text: "您确定将该消息贴到留言墙上吗？请注意文明发言哦。",
						type: "warning",
						showCancelButton: true,
						cancelButtonText: "取消",
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "确定",
						closeOnConfirm: false
						
					}, 
					function(){
						createItem(value);
						$.ajax({
							url: "./servlet.php?act=addmsg",
							type: "post",
							dataType: "json",
							data: {
								type: "addmsg",
								input: $('#input').val()
							},
							success: function (data) {
								if (data && (data.status === "pass")) {
									$this.val('');
									swal({
										title: '操作成功',
										type: 'success',
										text: '留言已经成功被贴上去啦^-^!如果需要删除该留言，请联系站点管理人员！',
										confirmButtonText: '确定',
										timer: 3000
									});
								}
								else {
									swal({
										title: '后端响应超时',
										type: 'error',
										text: '糟糕，投递失败了(*>﹏<*)本次投递将仅在本地保留！此错误可能是后端服务器宕机或当前处于全局禁言状态导致的，请联系管理员解决此问题。',
										confirmButtonText: '我知道了'
									});
								}
							}
						});
					});
					return false;
				}
				else {
					swal({
						title: '非法操作被阻断',
						type: 'error',
						text: '亲，请先填写要投递的内容哦。如果您已经输入了内容依旧显示本提示，请先升级您的浏览器。推荐使用Firefox、Chromium等现代化的浏览器！',
						confirmButtonText: '好的',
						timer: 2000
					});
					return false;
				}
            }
        });
    };
    $(function () {
        init();
    });
})(jQuery);