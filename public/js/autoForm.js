function materialChange(number){
    //console.log('materialForm'+number);
     var material_form = document.getElementById('material_form'+number);
      //submit()でフォームの内容を送信
       material_form.submit();
}

function materialDetailChange(number){
    //console.log('materialDetailForm'+number);
     var material_detail_form = document.getElementById('material_detail_form'+number);
       //submit()でフォームの内容を送信
       material_detail_form.submit();
}

function simulationChange(){
    //console.log('materialForm'+number);
     var simulation_form = document.getElementById('simulation_inf');
      //submit()でフォームの内容を送信
       simulation_form.submit();
}