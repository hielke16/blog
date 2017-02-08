jQuery( document ).ready(function($) {
    var compileButton = $('#compile-scss');

    compileButton.click(function() {
        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
              action: 'compile_scss'
          },
          success: function () {
              var msg = '<span class="dashicons dashicons-yes"></span>&nbsp;&nbsp; scss gecompileerd';
              var duration = 1500;

              var el = document.createElement("div");
              el.setAttribute("style","position:absolute;top:50px;left:calc(50% - 125px);width:250px;text-align:center;font-size:18px;padding:12px 0;border-radius:5px;background-color:#FFF;");
              el.innerHTML = msg;
              setTimeout(function(){
               el.parentNode.removeChild(el);
              },duration);
              document.body.appendChild(el);
            console.log('Compiled SCSS!');
         }
        });
    });
});
