    $("#form_team").submit(function(e){
        e.preventDefault();
        load_matches();
  //or
  //return false;
});

function load_matches(){

    var team = $('#team').val();


    $.post("ajax_get_matches.php",
        {
            name: team
            
        },
        function (data) {
           $('#form_match').html(data);
        });
        return false ;// avoid action
};


function switch_cab(val){

    if(val.length){
        document.getElementById('cab').style.display = 'block';
        document.getElementById('rating_limit').style.display = 'block';
    }else{
        document.getElementById('cab').style.display = 'none';
        document.getElementById('max_rating').value = 0 ;
        document.getElementById('rating_limit').style.display = 'none';
    }

}