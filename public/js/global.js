  /* Composants materialize */
  
  document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.sidenav');
    var instances = M.Sidenav.init(elems, {});
  });
  
   document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.tooltipped');    
    var instances = M.Tooltip.init(elems, {});
  });