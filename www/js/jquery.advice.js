$(document).ready(function(){
    $(".close_advice").click(function(e) {
        document.cookie = "langtest=0";
        $(this).parent().fadeOut(function(){$(".content").removeClass("advices");});
    });
});
