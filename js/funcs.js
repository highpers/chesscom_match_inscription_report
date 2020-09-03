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