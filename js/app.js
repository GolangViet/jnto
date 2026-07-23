const AppMain = (function(){
  return {
        init: function(){
           alert(1)
        },
    }
})();

document.addEventListener("DOMContentLoaded", function (event) {
    AppMain.init();
});