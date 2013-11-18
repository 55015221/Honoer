$(document).ready(function(){
    $("form").submit(function(){
        editor.sync();
        doSubmit();
        return false;
    });
})
/**
 * 通用AJAX提交
 * @param  {string} url    表单提交地址
 * @param  {string} formObj    待提交的表单对象或ID
 */
function doSubmit(url,formObj){
    
    if(!formObj||formObj==''){
        var formObj="form";
    }
    url = $(formObj).attr("action");
    if(!url||url==''){
        var url=document.URL;
    }
    alert(url);
    $(formObj).ajaxSubmit({
        url:url,
        type:"POST",
        dataType:'json',
        beforeSubmit:function(){
            alert('开始提交');
        },
        success:function(data) {
            if(data.status==1){
                alert(data.info);
                setTimeout(function(){
                    //
                    },2000);
            }
        },
        error:function(){
            alert('Ajax error!');
        }
    });
    return false;
}
