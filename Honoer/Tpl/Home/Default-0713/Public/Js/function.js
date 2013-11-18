$(document).ready(function(){
    var t;
    /** 图片异步 **/
    $("img").lazyload({
        effect : "fadeIn"
    });
    /** 语法高亮 **/
    SyntaxHighlighter.defaults['toolbar'] = true;
    SyntaxHighlighter.defaults['gutter'] = false;
    SyntaxHighlighter.config.clipboardSwf = PUBLIC +'/Plugins/syntaxhighlighter/scripts/clipboard.swf';
    SyntaxHighlighter.all();
    
    /** 顶部导航 **/
    $(".nav-child").parent("li").hover(function(){
        $(this).addClass("this").find("dl").show();
    },function(){
        $(this).removeClass("this").find("dl").hide();
    });
    $(".tab-title").find("a").hover(function(){
        var thisTab =$(this);
        t = setTimeout(function(){
            thisTab.parent("li").addClass("active").siblings("li").removeClass("active");
            $(thisTab.attr("data-toggle")).fadeIn().siblings("div").hide();
        },300);
    },function(){
        clearTimeout(t);
    });
    
    /** 返回顶部 **/
    $(window).scroll(function(){
        var scroller = $('.rollto');
        document.documentElement.scrollTop+document.body.scrollTop>200?scroller.fadeIn():scroller.fadeOut();
    })
    var scrollBox = '<div class="rollto"><a title="回顶部" class="ico ico-totop"></a></div>';
    $("body").append(scrollBox);
    $('.ico-totop').click(function(){
        $('html,body').animate({
            scrollTop:0
        },600);
    })

    $('.ico-torespond').click(function(){
        scrollTo('.ico-torespond');
        $('#comment').focus();
    })
    function scrollTo(name){
        var ID = $($(name).attr('href'));
        if(ID.length>0)
            $('html,body').animate({
                scrollTop: ID.offset().top-10
            },600);
    }
    
})
